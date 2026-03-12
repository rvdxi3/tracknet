@extends('auth.layout')

@section('title', 'Reset Password')
@section('subtitle', 'Reset your password')

@section('content')

@if (session('status'))
    <div class="alert alert-success py-2 mb-3">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
    </div>
@endif

<p class="text-muted mb-4" style="font-size:.9rem;">
    Enter your email address and we'll send you a link to reset your password.
</p>

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="mb-4">
        <label for="email" class="form-label fw-semibold">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus placeholder="you@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="fas fa-paper-plane me-2"></i>Send Reset Link
    </button>
</form>
@endsection

@section('footer')
    <a href="{{ route('login') }}" class="text-primary">
        <i class="fas fa-arrow-left me-1"></i>Back to Sign In
    </a>
@endsection
