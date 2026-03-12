<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    // -------------------------------------------------------------------------
    // Show the new password form (guarded by session OTP verification)
    // -------------------------------------------------------------------------

    public function showResetForm()
    {
        if (! session('pw_reset_email') || ! session('pw_reset_otp_verified')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Please verify your identity first.']);
        }

        return view('auth.passwords.reset');
    }

    // -------------------------------------------------------------------------
    // Process the new password
    // -------------------------------------------------------------------------

    public function reset(Request $request)
    {
        if (! session('pw_reset_email') || ! session('pw_reset_otp_verified')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Please verify your identity first.']);
        }

        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ]);

        $user = User::where('email', session('pw_reset_email'))->first();

        if (! $user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'No account found. Please start again.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Clear all password reset session data
        $request->session()->forget(['pw_reset_email', 'pw_reset_otp_verified', 'dev_pw_reset_otp']);

        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully. Please sign in with your new password.');
    }
}
