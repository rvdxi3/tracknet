@extends('auth.layout')

@section('title', 'Verify Your Identity')
@section('subtitle', 'Enter your verification code')

@push('styles')
<style>
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
    #countdown { font-variant-numeric: tabular-nums; }
</style>
@endpush

@section('content')
<p class="text-muted mb-4" style="font-size:.9rem;">
    A 6-digit code was sent to <strong>{{ $email }}</strong>.
    Enter it below. The code expires in <strong>10 minutes</strong>.
</p>

@if ($errors->has('code'))
    <div class="alert alert-danger py-2 mb-3">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first('code') }}
    </div>
@endif

@if (session('status'))
    <div class="alert alert-success py-2 mb-3">
        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
    </div>
@endif

{{-- Development helper: show code when using log mail driver --}}
@if(session('dev_pw_reset_otp'))
    <div class="alert py-2 mb-3" style="background:#fefce8;border:1px solid #fbbf24;border-radius:8px;">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="fas fa-flask" style="color:#d97706;"></i>
            <span class="fw-bold" style="font-size:.8rem;color:#92400e;text-transform:uppercase;letter-spacing:.04em;">Dev Mode — Email not sent</span>
        </div>
        <div style="font-size:.85rem;color:#78350f;">
            Your code is:
            <strong id="devCode" style="font-size:1.2rem;letter-spacing:.18em;font-family:monospace;">{{ session('dev_pw_reset_otp') }}</strong>
            <button type="button" onclick="autofillCode('{{ session('dev_pw_reset_otp') }}')"
                    class="btn btn-sm ms-2" style="background:#fbbf24;border:none;font-size:.75rem;padding:.15rem .5rem;">
                Fill in
            </button>
        </div>
        <div class="mt-1" style="font-size:.75rem;color:#92400e;">
            In production, set <code>MAIL_MAILER=smtp</code> in <code>.env</code> to send real emails.
        </div>
    </div>
@endif

<form method="POST" action="{{ route('password.otp.verify') }}" id="otpForm">
    @csrf

    {{-- Hidden input for the combined code --}}
    <input type="hidden" name="code" id="codeInput">

    {{-- 6 individual boxes --}}
    <div class="d-flex justify-content-center gap-2 mb-4">
        @for ($i = 0; $i < 6; $i++)
            <input type="text" inputmode="numeric" maxlength="1"
                   class="otp-input" data-index="{{ $i }}" autocomplete="off">
        @endfor
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3" id="verifyBtn">
        <i class="fas fa-check-circle me-2"></i>Verify Code
    </button>
</form>

<div class="text-center">
    <span class="text-muted" style="font-size:.85rem;">Didn't receive it? </span>
    <form method="POST" action="{{ route('password.otp.resend') }}" class="d-inline" id="resendForm">
        @csrf
        <button type="submit" class="btn btn-link p-0 fw-semibold" id="resendBtn" style="font-size:.85rem;">
            Resend code
        </button>
    </form>
    <span class="text-muted" id="countdown" style="font-size:.83rem;display:none;"></span>

    @error('resend')
        <div class="text-danger mt-2" style="font-size:.83rem;">{{ $message }}</div>
    @enderror
</div>
@endsection

@section('footer')
    <a href="{{ route('password.request') }}" class="text-primary" style="font-size:.85rem;">
        <i class="fas fa-arrow-left me-1"></i>Use a different email
    </a>
@endsection

@push('scripts')
<script>
    const inputs    = document.querySelectorAll('.otp-input');
    const codeIn    = document.getElementById('codeInput');
    const resendBtn = document.getElementById('resendBtn');
    const countdown = document.getElementById('countdown');

    inputs.forEach((input, idx) => {
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(-1);
            if (this.value && idx < 5) inputs[idx + 1].focus();
            syncCode();
        });
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                inputs[idx - 1].focus();
            }
        });
        input.addEventListener('paste', function (e) {
            e.preventDefault();
            const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            [...pasted].slice(0, 6).forEach((ch, i) => {
                if (inputs[i]) inputs[i].value = ch;
            });
            inputs[Math.min(pasted.length, 5)].focus();
            syncCode();
        });
    });

    function syncCode() {
        codeIn.value = [...inputs].map(i => i.value).join('');
    }

    inputs[0].focus();

    function autofillCode(code) {
        [...code].forEach((ch, i) => { if (inputs[i]) inputs[i].value = ch; });
        syncCode();
        inputs[5].focus();
    }

    function startCountdown(seconds) {
        resendBtn.disabled = true;
        resendBtn.style.display = 'none';
        countdown.style.display = 'inline';
        const end = Date.now() + seconds * 1000;
        const tick = () => {
            const left = Math.ceil((end - Date.now()) / 1000);
            if (left <= 0) {
                resendBtn.disabled = false;
                resendBtn.style.display = 'inline';
                countdown.style.display = 'none';
            } else {
                countdown.textContent = '(resend in ' + left + 's)';
                setTimeout(tick, 500);
            }
        };
        tick();
    }

    @if(session('success'))
        startCountdown(60);
    @endif

    document.getElementById('resendForm').addEventListener('submit', function () {
        startCountdown(60);
    });
</script>
@endpush
