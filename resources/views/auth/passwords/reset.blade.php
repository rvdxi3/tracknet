@extends('auth.layout')

@section('title', 'Set New Password')
@section('subtitle', 'Choose a new password')

@section('content')
<p class="text-muted mb-4" style="font-size:.9rem;">
    Setting a new password for <strong>{{ session('pw_reset_email') }}</strong>.
</p>

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <div class="mb-3">
        <label for="password" class="form-label fw-semibold">New Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="new-password" placeholder="Min 8 chars, uppercase, number, symbol">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="text-muted">Must be at least 8 characters with uppercase, lowercase, number &amp; special character.</small>
    </div>

    <div class="mb-4">
        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control" required placeholder="Re-enter new password">
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="fas fa-key me-2"></i>Reset Password
    </button>
</form>
@endsection
