<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MfaCode;
use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use App\Services\ActivityLogService;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class MfaController extends Controller
{
    public function __construct(private TotpService $totp) {}

    // -------------------------------------------------------------------------
    // Step 1 — Choose MFA method
    // -------------------------------------------------------------------------

    public function showMethodSelect()
    {
        if (!$user = $this->getPendingUser()) {
            return redirect()->route('login');
        }

        return view('auth.mfa-method-select', compact('user'));
    }

    public function setMethod(Request $request)
    {
        if (!$user = $this->getPendingUser()) {
            return redirect()->route('login');
        }

        $request->validate(['mfa_method' => 'required|in:email,totp']);

        $user->update(['mfa_method' => $request->mfa_method]);

        if ($request->mfa_method === 'totp') {
            // Generate and store encrypted TOTP secret
            $secret = $this->totp->generateSecret();
            $user->update(['mfa_secret' => $secret]);
            return redirect()->route('mfa.totp.setup');
        }

        // Email OTP — send code
        $this->sendEmailOtp($user);
        return redirect()->route('mfa.email');
    }

    // -------------------------------------------------------------------------
    // Step 2a — Email OTP verify
    // -------------------------------------------------------------------------

    public function showEmailVerify()
    {
        if (!$user = $this->getPendingUser()) {
            return redirect()->route('login');
        }

        if ($user->mfa_method !== 'email') {
            return redirect()->route('mfa.method');
        }

        return view('auth.mfa-email-verify', compact('user'));
    }

    public function verifyEmail(Request $request)
    {
        if (!$user = $this->getPendingUser()) {
            return redirect()->route('login');
        }

        $request->validate(['code' => 'required|digits:6']);

        $mfaCode = MfaCode::where('user_id', $user->id)
            ->where('type', 'email')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$mfaCode || $mfaCode->code !== $request->code) {
            return back()->withErrors(['code' => 'Invalid or expired code. Please try again.']);
        }

        $mfaCode->update(['used_at' => now()]);
        $this->completeMfa($user, 'email', $request);

        return redirect()->route('mfa.pending');
    }

    public function resendEmail(Request $request)
    {
        if (!$user = $this->getPendingUser()) {
            return redirect()->route('login');
        }

        $key = 'mfa-resend:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['resend' => "Too many resend requests. Please wait {$seconds} seconds."]);
        }

        RateLimiter::hit($key, 60);
        $this->sendEmailOtp($user);

        return back()->with('success', 'A new code has been sent to your email.');
    }

    // -------------------------------------------------------------------------
    // Step 2b — TOTP setup
    // -------------------------------------------------------------------------

    public function showTotpSetup()
    {
        if (!$user = $this->getPendingUser()) {
            return redirect()->route('login');
        }

        if ($user->mfa_method !== 'totp' || !$user->mfa_secret) {
            return redirect()->route('mfa.method');
        }

        $secret = $user->mfa_secret; // decrypted automatically via cast
        $qrUrl  = $this->totp->getQrUrl($secret, $user->email);

        return view('auth.mfa-totp-setup', compact('user', 'secret', 'qrUrl'));
    }

    public function confirmTotp(Request $request)
    {
        if (!$user = $this->getPendingUser()) {
            return redirect()->route('login');
        }

        $request->validate(['code' => 'required|digits:6']);

        if (!$this->totp->verify($user->mfa_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code. Ensure your authenticator app time is correct and try again.']);
        }

        $this->completeMfa($user, 'totp', $request);
        return redirect()->route('mfa.pending');
    }

    // -------------------------------------------------------------------------
    // Step 3 — Pending approval page
    // -------------------------------------------------------------------------

    public function showPending()
    {
        // Allow recently-verified users OR still-pending session users
        if ($userId = session('pending_user_id')) {
            $user = User::find($userId);
            if ($user && $user->mfa_verified_at) {
                return view('auth.pending-approval', compact('user'));
            }
        }

        return redirect()->route('login');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function getPendingUser(): ?User
    {
        $id = session('pending_user_id');
        if (!$id) return null;

        $user = User::find($id);
        if (!$user || $user->mfa_verified_at) return null; // already verified

        return $user;
    }

    private function sendEmailOtp(User $user): void
    {
        // Invalidate previous unused codes
        MfaCode::where('user_id', $user->id)->whereNull('used_at')->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        MfaCode::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'type'       => 'email',
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send via Laravel mail — wrapped in try/catch so a mail failure
        // never crashes the registration flow.
        try {
            \Mail::raw(
                "Your TrackNet verification code is: {$code}\n\nThis code expires in 10 minutes.",
                function ($message) use ($user, $code) {
                    $message->to($user->email, $user->name)
                        ->subject('TrackNet — Your Verification Code: ' . $code)
                        ->from(config('mail.from.address', 'noreply@tracknet.com'), 'TrackNet');
                }
            );
        } catch (\Exception $e) {
            \Log::error('MFA email failed: ' . $e->getMessage());
        }

        // In development (log mail driver), surface the code on-screen so testing doesn't require reading logs
        if (config('mail.default') === 'log') {
            session(['dev_otp_code' => $code]);
        }
    }

    private function completeMfa(User $user, string $method, Request $request): void
    {
        $user->update(['mfa_verified_at' => now()]);

        ActivityLogService::mfaVerified($user->id, $method, $request);

        // Notify all admin users
        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewUserRegisteredNotification($user));
        }
    }
}
