@extends('website.layouts.app')

@section('title', 'Checkout')

@push('styles')
<style>
    .checkout-header {
        background: linear-gradient(135deg, #0f172a, #1e3a8a);
        color: #fff;
        padding: 2rem 0;
        margin: -1rem 0 2rem;
    }
    .checkout-header h1 { font-size: 1.7rem; font-weight: 800; margin: 0; }

    /* Step indicator */
    .checkout-steps {
        display: flex;
        align-items: center;
        gap: 0;
        margin-bottom: 2rem;
    }
    .checkout-step {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.82rem;
        font-weight: 600;
        color: #94a3b8;
    }
    .checkout-step .step-num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .checkout-step.active .step-num { background: #2563eb; color: #fff; }
    .checkout-step.active { color: #2563eb; }
    .checkout-step.done .step-num { background: #10b981; color: #fff; }
    .checkout-step.done { color: #10b981; }
    .checkout-step-divider {
        flex: 1;
        height: 2px;
        background: #e2e8f0;
        margin: 0 0.5rem;
        min-width: 30px;
    }

    /* Form sections */
    .checkout-section {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 1.75rem;
        margin-bottom: 1.25rem;
    }
    .checkout-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .checkout-section-title i { color: #2563eb; width: 20px; text-align: center; }

    /* Payment method cards */
    .payment-options { display: flex; flex-direction: column; gap: 0.75rem; }
    .payment-option {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.9rem 1.1rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .payment-option:hover { border-color: #93c5fd; background: #f8fafc; }
    .payment-option input[type=radio] { accent-color: #2563eb; width: 16px; height: 16px; flex-shrink: 0; }
    .payment-option.selected { border-color: #2563eb; background: #eff6ff; }
    .payment-option-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .payment-option-label { font-weight: 600; font-size: 0.9rem; color: #0f172a; }
    .payment-option-sub { font-size: 0.75rem; color: #64748b; }

    /* Order summary sidebar */
    .order-summary-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 1.75rem;
        position: sticky;
        top: 90px;
    }
    .order-summary-card h5 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .order-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .order-item:last-of-type { border-bottom: none; }
    .order-item-img {
        width: 48px;
        height: 48px;
        object-fit: contain;
        background: #f1f5f9;
        border-radius: 8px;
        padding: 4px;
        flex-shrink: 0;
    }
    .order-item-name { font-size: 0.82rem; font-weight: 600; color: #0f172a; line-height: 1.3; }
    .order-item-qty  { font-size: 0.75rem; color: #94a3b8; }
    .order-item-price { font-size: 0.875rem; font-weight: 700; color: #2563eb; margin-left: auto; white-space: nowrap; }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.45rem 0;
        font-size: 0.875rem;
        color: #64748b;
    }
    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.8rem 0 0.2rem;
        margin-top: 0.5rem;
        border-top: 1.5px solid #e2e8f0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }
    .summary-total .price { color: #2563eb; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="checkout-header" style="margin:-1rem -0.75rem 2rem;padding-left:0.75rem;padding-right:0.75rem;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:rgba(255,255,255,0.6);">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}" style="color:rgba(255,255,255,0.6);">Cart</a></li>
                <li class="breadcrumb-item active" style="color:rgba(255,255,255,0.9);">Checkout</li>
            </ol>
        </nav>
        <h1><i class="fas fa-credit-card me-2"></i>Checkout</h1>
    </div>
</div>

{{-- Steps --}}
<div class="checkout-steps mb-4">
    <div class="checkout-step done">
        <div class="step-num"><i class="fas fa-check" style="font-size:0.7rem;"></i></div>
        <span class="d-none d-sm-inline">Cart</span>
    </div>
    <div class="checkout-step-divider"></div>
    <div class="checkout-step active">
        <div class="step-num">2</div>
        <span class="d-none d-sm-inline">Checkout</span>
    </div>
    <div class="checkout-step-divider"></div>
    <div class="checkout-step">
        <div class="step-num">3</div>
        <span class="d-none d-sm-inline">Confirmation</span>
    </div>
</div>

<form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
    @csrf
    <div class="row g-4">
        {{-- ── Left: Form ── --}}
        <div class="col-lg-7">

            {{-- Contact Information --}}
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-user"></i> Contact Information
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone" style="font-size:0.8rem;"></i></span>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                   name="phone" value="{{ old('phone') }}" placeholder="+63 9XX XXX XXXX" required>
                        </div>
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-map-marker-alt"></i> Shipping Address
                </div>
                <div class="mb-3">
                    <label class="form-label">Full Shipping Address <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('shipping_address') is-invalid @enderror"
                              name="shipping_address" rows="3"
                              placeholder="House/Unit No., Street, Barangay, City, Province, ZIP"
                              required>{{ old('shipping_address') }}</textarea>
                    @error('shipping_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Same as billing --}}
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="same_billing"
                           name="same_billing" value="1"
                           {{ old('same_billing', '1') == '1' ? 'checked' : '' }}
                           onchange="toggleBilling(this)">
                    <label class="form-check-label" for="same_billing" style="font-size:0.875rem;font-weight:500;">
                        Billing address same as shipping
                    </label>
                </div>

                {{-- Billing address (hidden by default) --}}
                <div id="billing-address-wrap" style="{{ old('same_billing', '1') == '1' ? 'display:none;' : '' }}">
                    <label class="form-label">Billing Address</label>
                    <textarea class="form-control @error('billing_address') is-invalid @enderror"
                              name="billing_address" rows="3"
                              placeholder="House/Unit No., Street, Barangay, City, Province, ZIP">{{ old('billing_address') }}</textarea>
                    @error('billing_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Payment Method --}}
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-wallet"></i> Payment Method
                </div>
                <div class="payment-options" id="payment-options">
                    {{-- COD --}}
                    <label class="payment-option {{ old('payment_method','cod') == 'cod' ? 'selected' : '' }}">
                        <input type="radio" name="payment_method" value="cod"
                               {{ old('payment_method','cod') == 'cod' ? 'checked' : '' }} required>
                        <div class="payment-option-icon" style="color:#f59e0b;background:#fef3c7;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <div class="payment-option-label">Cash on Delivery</div>
                            <div class="payment-option-sub">Pay when your order arrives</div>
                        </div>
                    </label>
                    {{-- GCash --}}
                    <label class="payment-option {{ old('payment_method') == 'gcash' ? 'selected' : '' }}">
                        <input type="radio" name="payment_method" value="gcash"
                               {{ old('payment_method') == 'gcash' ? 'checked' : '' }}>
                        <div class="payment-option-icon" style="color:#0070E0;background:#E6F2FF;">
                            <i class="fas fa-mobile-screen-button"></i>
                        </div>
                        <div>
                            <div class="payment-option-label">GCash</div>
                            <div class="payment-option-sub">Pay via GCash e-wallet</div>
                        </div>
                    </label>
                    {{-- Maya --}}
                    <label class="payment-option {{ old('payment_method') == 'maya' ? 'selected' : '' }}">
                        <input type="radio" name="payment_method" value="maya"
                               {{ old('payment_method') == 'maya' ? 'checked' : '' }}>
                        <div class="payment-option-icon" style="color:#2BC467;background:#E8FAF0;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <div class="payment-option-label">Maya</div>
                            <div class="payment-option-sub">Pay via Maya / PayMaya</div>
                        </div>
                    </label>
                    {{-- Credit / Debit Card --}}
                    <label class="payment-option {{ old('payment_method') == 'card' ? 'selected' : '' }}">
                        <input type="radio" name="payment_method" value="card"
                               {{ old('payment_method') == 'card' ? 'checked' : '' }}>
                        <div class="payment-option-icon" style="color:#2563eb;background:#dbeafe;">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <div class="payment-option-label">Credit / Debit Card</div>
                            <div class="payment-option-sub">Visa, Mastercard, JCB via PayMongo</div>
                        </div>
                    </label>
                    {{-- GrabPay --}}
                    <label class="payment-option {{ old('payment_method') == 'grabpay' ? 'selected' : '' }}">
                        <input type="radio" name="payment_method" value="grabpay"
                               {{ old('payment_method') == 'grabpay' ? 'checked' : '' }}>
                        <div class="payment-option-icon" style="color:#00B14F;background:#E6F9EF;">
                            <i class="fas fa-taxi"></i>
                        </div>
                        <div>
                            <div class="payment-option-label">GrabPay</div>
                            <div class="payment-option-sub">Pay via Grab e-wallet</div>
                        </div>
                    </label>
                </div>
                @error('payment_method')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>

            {{-- Order Notes --}}
            <div class="checkout-section">
                <div class="checkout-section-title">
                    <i class="fas fa-sticky-note"></i> Order Notes (Optional)
                </div>
                <textarea class="form-control" name="notes" rows="3"
                          placeholder="Special instructions for delivery, gift message, etc.">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- ── Right: Order Summary ── --}}
        <div class="col-lg-5">
            <div class="order-summary-card">
                <h5><i class="fas fa-receipt me-2 text-primary"></i>Order Summary</h5>

                {{-- Items list --}}
                @foreach($cartItems as $item)
                    <div class="order-item">
                        <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : 'https://placehold.co/96x96/f1f5f9/94a3b8?text=?' }}"
                             class="order-item-img" alt="{{ $item->product->name }}">
                        <div class="flex-fill min-width-0">
                            <div class="order-item-name">{{ Str::limit($item->product->name, 40) }}</div>
                            <div class="order-item-qty">Qty: {{ $item->quantity }}</div>
                        </div>
                        <div class="order-item-price">
                            ₱{{ number_format($item->product->price * $item->quantity, 2) }}
                        </div>
                    </div>
                @endforeach

                {{-- Totals --}}
                <div class="mt-3 pt-2 border-top">
                    <div class="summary-row">
                        <span>Subtotal ({{ $cartItems->count() }} items)</span>
                        <span>₱{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax ({{ config('cart.tax_rate', 10) }}%)</span>
                        <span>₱{{ number_format($tax, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span class="text-success fw-semibold">
                            @if($shipping == 0) Free @else ₱{{ number_format($shipping, 2) }} @endif
                        </span>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span class="price">₱{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                {{-- Submit button --}}
                <button type="submit" class="btn btn-primary w-100 btn-lg mt-4" id="place-order-btn">
                    <i class="fas fa-lock me-2"></i>Place Order
                </button>

                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                    <i class="fas fa-arrow-left me-1"></i>Back to Cart
                </a>

                {{-- Security note --}}
                <div class="text-center mt-3" style="font-size:0.75rem;color:#94a3b8;">
                    <i class="fas fa-lock me-1"></i>
                    Your personal data is protected and will not be shared.
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    // Toggle billing address visibility
    function toggleBilling(checkbox) {
        const wrap = document.getElementById('billing-address-wrap');
        wrap.style.display = checkbox.checked ? 'none' : 'block';
    }

    // Highlight selected payment option + dynamic button text
    const onlineMethods = ['gcash', 'maya', 'card', 'grabpay'];
    document.querySelectorAll('.payment-option input[type=radio]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
            radio.closest('.payment-option').classList.add('selected');

            const btn = document.getElementById('place-order-btn');
            if (onlineMethods.includes(radio.value)) {
                btn.innerHTML = '<i class="fas fa-external-link-alt me-2"></i>Proceed to Payment';
            } else {
                btn.innerHTML = '<i class="fas fa-lock me-2"></i>Place Order';
            }
        });
    });

    // Prevent double submission
    document.getElementById('checkout-form').addEventListener('submit', function() {
        const btn = document.getElementById('place-order-btn');
        const selected = document.querySelector('.payment-option input[type=radio]:checked');
        btn.disabled = true;
        if (selected && onlineMethods.includes(selected.value)) {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Redirecting to Payment...';
        } else {
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Placing Order...';
        }
    });
</script>
@endpush
