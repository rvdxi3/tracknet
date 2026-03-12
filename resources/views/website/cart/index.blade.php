@extends('website.layouts.app')

@section('title', 'Shopping Cart')

@push('styles')
<style>
    .cart-page-header {
        background: linear-gradient(135deg, #0f172a, #1e3a8a);
        color: #fff;
        padding: 2rem 0;
        margin: -1rem 0 2rem;
    }
    .cart-page-header h1 { font-size: 1.7rem; font-weight: 800; margin: 0; }

    /* Cart table */
    .cart-table-wrap {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .cart-table { margin: 0; }
    .cart-table th {
        background: #f8fafc;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #64748b;
        padding: 1rem 1.25rem;
        border-bottom: 1.5px solid #e2e8f0;
    }
    .cart-table td {
        padding: 1.1rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }
    .cart-table tbody tr:last-child td { border-bottom: none; }
    .cart-table tbody tr:hover { background: #fafbfc; }

    .cart-img {
        width: 70px;
        height: 70px;
        object-fit: contain;
        border-radius: 10px;
        background: #f1f5f9;
        padding: 6px;
    }
    .cart-product-name { font-weight: 600; font-size: 0.9rem; color: #0f172a; }
    .cart-product-cat  { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

    /* Qty control */
    .qty-control {
        display: flex;
        align-items: center;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        width: fit-content;
    }
    .qty-control button {
        border: none;
        background: #f8fafc;
        width: 32px;
        height: 36px;
        font-size: 1rem;
        color: #475569;
        cursor: pointer;
        transition: background 0.15s;
    }
    .qty-control button:hover { background: #e2e8f0; }
    .qty-control input {
        border: none;
        border-left: 1.5px solid #e2e8f0;
        border-right: 1.5px solid #e2e8f0;
        width: 48px;
        height: 36px;
        text-align: center;
        font-weight: 700;
        font-size: 0.875rem;
        outline: none;
    }

    /* Cart summary card */
    .cart-summary-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 1.75rem;
        position: sticky;
        top: 90px;
    }
    .cart-summary-card h5 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1.5px solid #e2e8f0;
    }
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

    /* Empty cart */
    .empty-cart-wrap {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 4rem 2rem;
        text-align: center;
    }
    .empty-cart-icon {
        width: 100px;
        height: 100px;
        background: #eff6ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2.5rem;
        color: #2563eb;
    }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="cart-page-header" style="margin:-1rem -0.75rem 2rem;padding-left:0.75rem;padding-right:0.75rem;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:0.82rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" style="color:rgba(255,255,255,0.6);">Home</a>
                </li>
                <li class="breadcrumb-item active" style="color:rgba(255,255,255,0.9);">Shopping Cart</li>
            </ol>
        </nav>
        <h1><i class="fas fa-shopping-cart me-2"></i>Shopping Cart
            @if($cartItems->count() > 0)
                <span style="font-size:1rem;font-weight:500;opacity:0.7;">({{ $cartItems->count() }} items)</span>
            @endif
        </h1>
    </div>
</div>

@if($cartItems->count() > 0)
    <div class="row g-4">
        {{-- Cart Items --}}
        <div class="col-lg-8">
            <div class="cart-table-wrap">
                <table class="table cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartItems as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : 'https://placehold.co/140x140/f1f5f9/94a3b8?text=No+Image' }}"
                                             class="cart-img" alt="{{ $item->product->name }}">
                                        <div>
                                            <a href="{{ route('products.show', $item->product) }}"
                                               class="cart-product-name text-decoration-none">
                                                {{ $item->product->name }}
                                            </a>
                                            <div class="cart-product-cat">{{ $item->product->category->name ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight:600;">₱{{ number_format($item->product->price, 2) }}</td>
                                <td>
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex align-items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="qty-control">
                                            <button type="button"
                                                    onclick="adjustQty(this, -1, {{ $item->product->stock }})">&#8722;</button>
                                            <input type="number" name="quantity"
                                                   value="{{ $item->quantity }}"
                                                   min="1" max="{{ $item->product->stock }}"
                                                   oninput="this.form.submit()">
                                            <button type="button"
                                                    onclick="adjustQty(this, 1, {{ $item->product->stock }})">&#43;</button>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Update">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </form>
                                </td>
                                <td style="font-weight:700;color:#2563eb;">
                                    ₱{{ number_format($item->product->price * $item->quantity, 2) }}
                                </td>
                                <td>
                                    <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Remove"
                                                onclick="return confirm('Remove this item?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="col-lg-4">
            <div class="cart-summary-card">
                <h5><i class="fas fa-receipt me-2 text-primary"></i>Order Summary</h5>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₱{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Tax ({{ config('cart.tax_rate', 10) }}%)</span>
                    <span>₱{{ number_format($tax, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span class="text-success fw-600">
                        @if($subtotal >= 500) Free
                        @else TBD at checkout
                        @endif
                    </span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span class="price">₱{{ number_format($total, 2) }}</span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 btn-lg mt-4">
                    Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                </a>

                {{-- Trust badges --}}
                <div class="mt-3 pt-3 border-top d-flex flex-column gap-2">
                    <div class="d-flex align-items-center gap-2" style="font-size:0.78rem;color:#64748b;">
                        <i class="fas fa-lock text-primary"></i> Secure SSL checkout
                    </div>
                    <div class="d-flex align-items-center gap-2" style="font-size:0.78rem;color:#64748b;">
                        <i class="fas fa-shield-alt text-success"></i> 1-year warranty on all items
                    </div>
                    <div class="d-flex align-items-center gap-2" style="font-size:0.78rem;color:#64748b;">
                        <i class="fas fa-undo text-warning"></i> 15-day hassle-free returns
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="empty-cart-wrap">
        <div class="empty-cart-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <h4 class="fw-bold text-dark mb-2">Your cart is empty</h4>
        <p class="text-muted mb-4">Looks like you haven't added anything yet. Browse our products and find something you'll love.</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg px-5">
            <i class="fas fa-shopping-bag me-2"></i>Start Shopping
        </a>
    </div>
@endif

@endsection

@push('scripts')
<script>
    function adjustQty(btn, delta, maxStock) {
        const row = btn.closest('.qty-control');
        const input = row.querySelector('input[type=number]');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > maxStock) val = maxStock;
        input.value = val;
    }
</script>
@endpush
