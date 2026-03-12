@extends('auth.layout')

@section('title', 'Set Up Authenticator')
@section('subtitle', 'Connect your authenticator app')
@section('card-class', 'auth-card-wide')

@push('styles')
<style>
    .secret-box {
        background: #f1f5f9;
        border: 1px dashed #94a3b8;
        border-radius: 8px;
        padding: .6rem 1rem;
        font-family: monospace;
        font-size: .95rem;
        letter-spacing: .12em;
        word-break: break-all;
    }
    .otp-input {
        width: 48px; height: 56px;
        font-size: 1.4rem; font-weight: 700;
        text-align: center;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        transition: border-color .2s;
    }
    .otp-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 .2rem rgba(37,99,235,.15);
        outline: none;
    }
</style>
@endpush

@section('content')

{{-- Steps --}}
<ol class="text-muted mb-4" style="font-size:.88rem;padding-left:1.2rem;line-height:1.9;">
    <li>Open <strong>Google Authenticator</strong>, <strong>Authy</strong>, or any TOTP app.</li>
    <li>Tap <strong>"+"</strong> or <strong>"Add account"</strong>.</li>
    <li>Scan the QR code below, or enter the manual key.</li>
    <li>Enter the 6-digit code shown in the app to confirm.</li>
</ol>

{{-- QR Code --}}
<div class="text-center mb-3">
    <img src="{{ $qrUrl }}" alt="QR Code" width="180" height="180"
         style="border:6px solid white;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.12);">
</div>

{{-- Manual key --}}
<div class="mb-4">
    <div class="text-muted fw-semibold mb-1" style="font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;">
        Manual Key (if you can't scan)
    </div>
    <div class="secret-box">{{ $secret }}</div>
</div>

{{-- Confirm code --}}
@if ($errors->has('code'))
    <div class="alert alert-danger py-2 mb-3">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first('code') }}
    </div>
@endif

<form method="POST" action="{{ route('mfa.totp.confirm') }}" id="totpForm">
    @csrf

    <input type="hidden" name="code" id="codeInput">

    <div class="mb-1 fw-semibold" style="font-size:.9rem;">Enter the 6-digit code from your app:</div>
    <div class="d-flex justify-content-center gap-2 mb-4">
        @for ($i = 0; $i < 6; $i++)
            <input type="text" inputmode="numeric" maxlength="1"
                   class="otp-input" data-index="{{ $i }}" autocomplete="off">
        @endfor
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        <i class="fas fa-shield-alt me-2"></i>Confirm &amp; Activate
    </button>
</form>
@endsection

@section('footer')
    <a href="{{ route('mfa.method') }}" class="text-primary" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Use email OTP instead
    </a>
@endsection

@push('scripts')
<script>
    const inputs = document.querySelectorAll('.otp-input');
    const codeIn = document.getElementById('codeInput');

    inputs.forEach((input, idx) => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(-1);
            if (this.value && idx < 5) inputs[idx + 1].focus();
            syncCode();
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) inputs[idx - 1].focus();
        });
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            [...pasted].slice(0, 6).forEach((ch, i) => { if (inputs[i]) inputs[i].value = ch; });
            inputs[Math.min(pasted.length, 5)].focus();
            syncCode();
        });
    });

    function syncCode() {
        codeIn.value = [...inputs].map(i => i.value).join('');
    }

    inputs[0].focus();
</script>
@endpush
