<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
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
}
