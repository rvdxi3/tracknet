<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MfaCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class ForgotPasswordController extends Controller
{
    // -------------------------------------------------------------------------
    // Step 1 — Show email form
    // -------------------------------------------------------------------------

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // -------------------------------------------------------------------------
    // Step 2 — Send OTP
    // -------------------------------------------------------------------------

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $key = 'pw-reset:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['email' => "Too many attempts. Please wait {$seconds} seconds."]);
        }

        RateLimiter::hit($key, 60);

        $user = User::findByEmail($request->email);

        // Always store email in session and redirect — avoid email enumeration
        session(['pw_reset_email' => $request->email]);

        if ($user) {
            $this->sendOtp($user);
        }

        return redirect()->route('password.otp')
            ->with('status', 'If an account exists with that email, a 6-digit code has been sent.');
    }

    // -------------------------------------------------------------------------
    // Step 3 — Show OTP form
    // -------------------------------------------------------------------------

    public function showOtpForm()
    {
        if (! session('pw_reset_email')) {
            return redirect()->route('password.request');
        }

        $email = session('pw_reset_email');

        return view('auth.passwords.otp', compact('email'));
    }

    // -------------------------------------------------------------------------
    // Step 4 — Verify OTP
    // -------------------------------------------------------------------------

    public function verifyOtp(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $email = session('pw_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $user = User::findByEmail($email);

        if ($user) {
            $mfaCode = MfaCode::where('user_id', $user->id)
                ->where('type', 'password_reset')
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->latest()
                ->first();

            if ($mfaCode && $mfaCode->code === $request->code) {
                $mfaCode->update(['used_at' => now()]);
                session(['pw_reset_otp_verified' => true]);

                return redirect()->route('password.reset.form');
            }
        }

        return back()->withErrors(['code' => 'Invalid or expired code. Please try again.']);
    }

    // -------------------------------------------------------------------------
    // Resend OTP
    // -------------------------------------------------------------------------

    public function resendOtp(Request $request)
    {
        $email = session('pw_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $key = 'pw-reset-resend:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['resend' => "Too many resend requests. Please wait {$seconds} seconds."]);
        }

        RateLimiter::hit($key, 60);

        $user = User::findByEmail($email);

        if ($user) {
            $this->sendOtp($user);
        }

        return back()->with('success', 'A new code has been sent to your email.');
    }

    // -------------------------------------------------------------------------
    // Private helper
    // -------------------------------------------------------------------------

    private function sendOtp(User $user): void
    {
        // Invalidate previous unused password reset codes
        MfaCode::where('user_id', $user->id)
            ->where('type', 'password_reset')
            ->whereNull('used_at')
            ->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        MfaCode::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'type'       => 'password_reset',
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw(
            "Your TrackNet password reset code is: {$code}\n\nThis code expires in 10 minutes.\n\nIf you did not request a password reset, please ignore this email.",
            function ($message) use ($user, $code) {
                $message->to($user->email, $user->name)
                    ->subject('TrackNet — Password Reset Code: ' . $code)
                    ->from(config('mail.from.address', 'noreply@tracknet.com'), 'TrackNet');
            }
        );

        // Surface the code on-screen in dev mode (log mail driver)
        if (config('mail.default') === 'log') {
            session(['dev_pw_reset_otp' => $code]);
        }
    }
}
