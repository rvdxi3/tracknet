@extends('auth.layout')

@section('title', 'Choose MFA Method')
@section('subtitle', 'Secure your account')
@section('card-class', 'auth-card-wide')

@push('styles')
<style>
    .mfa-option {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
        cursor: pointer;
        transition: border-color .2s, background .2s;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
    }
    .mfa-option:hover { border-color: #2563eb; background: #eff6ff; }
    input[type=radio]:checked + label .mfa-option {
        border-color: #2563eb;
        background: #eff6ff;
    }
    .mfa-icon {
        width: 48px; height: 48px;
        background: #dbeafe;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; color: #2563eb;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<p class="text-muted mb-4" style="font-size:.9rem;">
    Hi <strong>{{ $user->name }}</strong>! To protect your account, please choose a verification method.
    You'll use this to confirm your identity now.
</p>

<form method="POST" action="{{ route('mfa.method.set') }}">
    @csrf

    <div class="d-flex flex-column gap-3 mb-4">

        <div>
            <input type="radio" name="mfa_method" id="method_email" value="email" class="visually-hidden"
                   {{ old('mfa_method', 'email') === 'email' ? 'checked' : '' }}>
            <label for="method_email" class="d-block">
                <div class="mfa-option">
                    <div class="mfa-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="fw-bold">Email OTP</div>
                        <div class="text-muted" style="font-size:.85rem;">
                            We'll send a 6-digit code to <strong>{{ $user->email }}</strong>.
                            Quick and no extra app needed.
                        </div>
                    </div>
                </div>
            </label>
        </div>

        <div>
            <input type="radio" name="mfa_method" id="method_totp" value="totp" class="visually-hidden"
                   {{ old('mfa_method') === 'totp' ? 'checked' : '' }}>
            <label for="method_totp" class="d-block">
                <div class="mfa-option">
                    <div class="mfa-icon"><i class="fas fa-mobile-alt"></i></div>
                    <div>
                        <div class="fw-bold">Authenticator App (TOTP)</div>
                        <div class="text-muted" style="font-size:.85rem;">
                            Use Google Authenticator, Authy, or any TOTP app.
                            More secure — works offline.
                        </div>
                    </div>
                </div>
            </label>
        </div>

    </div>

    @error('mfa_method')
        <div class="alert alert-danger py-2 mb-3">{{ $message }}</div>
    @enderror

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="fas fa-arrow-right me-2"></i>Continue
    </button>
</form>
@endsection

@push('scripts')
<script>
    // Make entire card clickable
    document.querySelectorAll('.mfa-option').forEach(card => {
        card.addEventListener('click', function () {
            const radio = this.closest('label').previousElementSibling;
            radio.checked = true;
        });
    });
</script>
@endpush
