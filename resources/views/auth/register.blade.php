@extends('auth.layout')

@section('title', 'Create Account')
@section('subtitle', 'Create your customer account')
@section('card-class', 'auth-card-wide')

@section('content')
<form method="POST" action="{{ route('register') }}" id="registerForm">
    @csrf

    {{-- Name --}}
    <div class="mb-3">
        <label for="name" class="form-label fw-semibold">Full Name</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
            <input id="name" type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Your full name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label for="email" class="form-label fw-semibold">Email Address</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autocomplete="email"
                   placeholder="you@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label for="password" class="form-label fw-semibold">Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   required autocomplete="new-password" placeholder="Min 8 chars, uppercase, number, symbol"
                   oninput="checkStrength(this.value)">
            <button class="btn btn-outline-secondary" type="button" id="togglePw" tabindex="-1">
                <i class="fas fa-eye" id="eyePw"></i>
            </button>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        {{-- Strength bar --}}
        <div class="mt-2">
            <div class="progress" style="height:4px;">
                <div id="strengthBar" class="progress-bar" role="progressbar" style="width:0%;transition:width .3s,background .3s;"></div>
            </div>
            <small id="strengthLabel" class="text-muted"></small>
        </div>
    </div>

    {{-- Confirm Password --}}
    <div class="mb-4">
        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control" required autocomplete="new-password" placeholder="Re-enter password">
        </div>
    </div>

    {{-- Password policy reminder --}}
    <div class="alert alert-info py-2 mb-4" style="font-size:.82rem;">
        <i class="fas fa-shield-alt me-1"></i>
        Password must be at least <strong>8 characters</strong> and include uppercase, lowercase, a number, and a special character.
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="fas fa-user-plus me-2"></i>Create Account &amp; Set Up MFA
    </button>
</form>
@endsection

@section('footer')
    Already have an account?
    <a href="{{ route('login') }}" class="text-primary fw-semibold">Sign in</a>
@endsection

@push('scripts')
<script>
    // Show/hide password
    document.getElementById('togglePw').addEventListener('click', function () {
        const pw   = document.getElementById('password');
        const icon = document.getElementById('eyePw');
        if (pw.type === 'password') {
            pw.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pw.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    // Simple strength meter
    function checkStrength(value) {
        let score = 0;
        if (value.length >= 8)              score++;
        if (/[A-Z]/.test(value))            score++;
        if (/[a-z]/.test(value))            score++;
        if (/[0-9]/.test(value))            score++;
        if (/[^A-Za-z0-9]/.test(value))     score++;

        const bar    = document.getElementById('strengthBar');
        const label  = document.getElementById('strengthLabel');
        const pct    = (score / 5) * 100;
        bar.style.width = pct + '%';

        const levels = ['', 'Very Weak', 'Weak', 'Fair', 'Strong', 'Very Strong'];
        const colors = ['', '#ef4444', '#f97316', '#eab308', '#22c55e', '#16a34a'];
        bar.style.background = colors[score] || '#e2e8f0';
        label.textContent    = value.length ? levels[score] : '';
        label.style.color    = colors[score] || '#94a3b8';
    }
</script>
@endpush
