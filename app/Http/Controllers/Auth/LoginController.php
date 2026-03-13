<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MfaCode;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\TotpService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override attemptLogin: email is encrypted in DB, so we look up by email_hash.
     */
    protected function attemptLogin(Request $request)
    {
        $user = User::findByEmail($request->input($this->username()));

        if ($user && Hash::check($request->input('password'), $user->password)) {
            $this->guard()->login($user, $request->boolean('remember'));
            return true;
        }

        return false;
    }

    protected function authenticated(Request $request, $user)
    {
        // Reject inactive accounts (pending approval or rejected)
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = $user->rejected_at
                ? 'Your account registration was not approved. Please contact support.'
                : 'Your account is pending admin approval. You will be notified by email once activated.';

            return redirect()->route('login')->with('error', $message);
        }

        // --- Login MFA: require OTP verification on every login ---
        // Log the user out temporarily; they must verify OTP first
        Auth::logout();
        $request->session()->put('login_mfa_user_id', $user->id);
        $request->session()->put('login_mfa_remember', $request->boolean('remember'));

        // Send email OTP (works for all users — TOTP users also get email OTP on login)
        $this->sendLoginOtp($user);

        return redirect()->route('login.verify');
    }

    // -------------------------------------------------------------------------
    // Login OTP verification
    // -------------------------------------------------------------------------

    public function showLoginVerify()
    {
        $user = $this->getLoginMfaUser();
        if (!$user) {
            return redirect()->route('login');
        }

        return view('auth.login-verify', compact('user'));
    }

    public function verifyLoginOtp(Request $request)
    {
        $user = $this->getLoginMfaUser();
        if (!$user) {
            return redirect()->route('login');
        }

        $request->validate(['code' => 'required|digits:6']);

        // Check if user has TOTP set up and verify against authenticator app
        if ($user->mfa_method === 'totp' && $user->mfa_secret) {
            $totp = app(TotpService::class);
            if ($totp->verify($user->mfa_secret, $request->code)) {
                return $this->completeLoginMfa($request, $user);
            }
        }

        // Check email OTP code
        $mfaCode = MfaCode::where('user_id', $user->id)
            ->where('type', 'login')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if ($mfaCode && $mfaCode->code === $request->code) {
            $mfaCode->update(['used_at' => now()]);
            return $this->completeLoginMfa($request, $user);
        }

        return back()->withErrors(['code' => 'Invalid or expired code. Please try again.']);
    }

    public function resendLoginOtp(Request $request)
    {
        $user = $this->getLoginMfaUser();
        if (!$user) {
            return redirect()->route('login');
        }

        $key = 'login-otp-resend:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['resend' => "Too many resend requests. Please wait {$seconds} seconds."]);
        }

        RateLimiter::hit($key, 60);
        $this->sendLoginOtp($user);

        return back()->with('success', 'A new code has been sent to your email.');
    }

    // -------------------------------------------------------------------------
    // Failed login + logout
    // -------------------------------------------------------------------------

    protected function sendFailedLoginResponse(Request $request)
    {
        ActivityLogService::loginFailed($request->input($this->username()), $request);

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogService::logout($user->id, $user->email, $request);
        }

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function getLoginMfaUser(): ?User
    {
        $id = session('login_mfa_user_id');
        if (!$id) return null;
        return User::find($id);
    }

    private function sendLoginOtp(User $user): void
    {
        // Invalidate previous unused login codes
        MfaCode::where('user_id', $user->id)->where('type', 'login')->whereNull('used_at')->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        MfaCode::create([
            'user_id'    => $user->id,
            'code'       => $code,
            'type'       => 'login',
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            \Mail::raw(
                "Your TrackNet login verification code is: {$code}\n\nThis code expires in 10 minutes.\n\nIf you did not attempt to log in, please change your password immediately.",
                function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                        ->subject('TrackNet — Login Verification Code')
                        ->from(config('mail.from.address', 'noreply@tracknet.com'), 'TrackNet');
                }
            );
        } catch (\Exception $e) {
            \Log::error('Login MFA email failed: ' . $e->getMessage());
        }

        if (config('mail.default') === 'log') {
            session(['dev_login_otp' => $code]);
        }
    }

    private function completeLoginMfa(Request $request, User $user)
    {
        // Get remember preference, then clear MFA session data
        $remember = $request->session()->pull('login_mfa_remember', false);
        $request->session()->forget(['login_mfa_user_id', 'dev_login_otp']);

        // Log the user in
        Auth::login($user, $remember);
        $request->session()->regenerate();

        ActivityLogService::loginSuccess($user->id, $user->email, $request);

        // Route based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isInventory()) {
            return redirect()->route('inventory.dashboard');
        } elseif ($user->isSales()) {
            return redirect()->route('sales.dashboard');
        }

        return redirect()->route('home');
    }
}
