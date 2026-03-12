<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/mfa/method';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'password.min'        => 'Password must be at least 8 characters.',
            'password.mixed_case' => 'Password must contain at least one uppercase and one lowercase letter.',
            'password.numbers'    => 'Password must contain at least one number.',
            'password.symbols'    => 'Password must contain at least one special character (e.g. !@#$%^&*).',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'customer',
            'is_active' => false,
        ]);
    }

    protected function registered(Request $request, $user)
    {
        // Don't auto-login the new user — they must complete MFA + await approval
        Auth::logout();

        ActivityLogService::registered($user->id, $user->email, $request);

        // Store pending user ID in session for MFA flow
        session(['pending_user_id' => $user->id]);

        return redirect()->route('mfa.method');
    }
}
