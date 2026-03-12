@extends('auth.layout')

@section('title', 'Pending Approval')
@section('subtitle', 'Verification complete')

@push('styles')
<style>
    .status-icon {
        width: 72px; height: 72px;
        background: #dcfce7;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: #16a34a;
        margin: 0 auto 1.5rem;
    }
    .step-item {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .65rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .step-item:last-child { border-bottom: none; }
    .step-num {
        width: 26px; height: 26px;
        background: #2563eb;
        border-radius: 50%;
        color: #fff;
        font-size: .78rem;
        font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .step-num.done {
        background: #16a34a;
    }
</style>
@endpush

@section('content')

<div class="status-icon">
    <i class="fas fa-check"></i>
</div>

<h5 class="fw-bold text-center mb-1">Identity Verified!</h5>
<p class="text-center text-muted mb-4" style="font-size:.9rem;">
    Your account has been created and your identity confirmed.
    An admin will review and activate your account shortly.
</p>

<div class="mb-4">
    <div class="step-item">
        <div class="step-num done"><i class="fas fa-check" style="font-size:.6rem;"></i></div>
        <div>
            <div class="fw-semibold" style="font-size:.9rem;">Account registered</div>
            <div class="text-muted" style="font-size:.82rem;">{{ $user->email }}</div>
        </div>
    </div>
    <div class="step-item">
        <div class="step-num done"><i class="fas fa-check" style="font-size:.6rem;"></i></div>
        <div>
            <div class="fw-semibold" style="font-size:.9rem;">Identity verified via {{ strtoupper($user->mfa_method ?? 'MFA') }}</div>
            <div class="text-muted" style="font-size:.82rem;">Completed {{ $user->mfa_verified_at?->diffForHumans() }}</div>
        </div>
    </div>
    <div class="step-item">
        <div class="step-num" style="background:#94a3b8;">3</div>
        <div>
            <div class="fw-semibold" style="font-size:.9rem;">Awaiting admin approval</div>
            <div class="text-muted" style="font-size:.82rem;">You'll receive an email when your account is activated.</div>
        </div>
    </div>
</div>

<div class="alert alert-info py-2" style="font-size:.85rem;">
    <i class="fas fa-info-circle me-2"></i>
    You'll be notified at <strong>{{ $user->email }}</strong> once your account is approved.
</div>

@endsection

@section('footer')
    <a href="{{ route('login') }}" class="text-primary">
        <i class="fas fa-sign-in-alt me-1"></i>Back to Login
    </a>
@endsection
