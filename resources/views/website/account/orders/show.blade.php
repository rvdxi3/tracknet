@extends('website.layouts.app')

@section('title', 'Order #' . $order->order_number)

@push('styles')
<style>
    .account-header {
        background: linear-gradient(135deg, #0f172a, #1e3a8a);
        color: #fff;
        padding: 2rem 0;
        margin: -1rem 0 2rem;
    }
    .account-header h1 { font-size: 1.7rem; font-weight: 800; margin: 0; }

    /* Account sidebar (same as orders/index) */
    .account-sidebar-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        position: sticky;
        top: 90px;
    }
    .account-sidebar-user {
        padding: 1.5rem 1.25rem;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
    }
    .account-avatar {
        width: 52px; height: 52px; background: rgba(255,255,255,0.2); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; font-weight: 800; color: #fff; margin-bottom: 0.75rem;
    }
    .account-user-name { font-weight: 700; font-size: 0.95rem; }
    .account-user-email { font-size: 0.78rem; opacity: 0.8; }
    .account-nav { padding: 0.5rem 0; }
    .account-nav-link {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 0.7rem 1.25rem; font-size: 0.875rem; font-weight: 500;
        color: #475569; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;
    }
    .account-nav-link:hover { background: #f8fafc; color: #2563eb; border-left-color: #93c5fd; }
    .account-nav-link.active { background: #eff6ff; color: #2563eb; border-left-color: #2563eb; font-weight: 600; }
    .account-nav-link i { width: 18px; text-align: center; font-size: 0.85rem; opacity: 0.7; }
    .account-nav-link.active i { opacity: 1; }
    .account-nav-divider { height: 1px; background: #e2e8f0; margin: 0.5rem 0; }

    /* Order detail card */
    .order-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .order-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .order-card-header h5 { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; }
    .order-card-body { padding: 1.5rem; }

    /* Status badges */
    .status-badge {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.35rem 0.9rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700;
    }
    .status-delivered  { background: #d1fae5; color: #065f46; }
    .status-shipped    { background: #dbeafe; color: #1e40af; }
    .status-processing { background: #fef9c3; color: #713f12; }
    .status-cancelled  { background: #fee2e2; color: #991b1b; }
    .status-pending    { background: #f1f5f9; color: #475569; }
    .status-awaiting   { background: #fef3c7; color: #92400e; }
    .status-paid       { background: #d1fae5; color: #065f46; }
    .status-failed     { background: #fee2e2; color: #991b1b; }
    .status-expired    { background: #f1f5f9; color: #6b7280; }

    /* Order items */
    .order-item-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.9rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .order-item-row:last-child { border-bottom: none; }
    .order-item-img {
        width: 60px; height: 60px; object-fit: contain;
        background: #f1f5f9; border-radius: 10px; padding: 6px; flex-shrink: 0;
    }
    .order-item-name { font-size: 0.9rem; font-weight: 600; color: #0f172a; line-height: 1.3; }
    .order-item-cat  { font-size: 0.75rem; color: #94a3b8; }
    .order-item-price { font-size: 0.875rem; color: #64748b; }
    .order-item-total { font-size: 1rem; font-weight: 700; color: #2563eb; margin-left: auto; white-space: nowrap; }

    /* Totals block */
    .totals-block { background: #f8fafc; border-radius: 10px; padding: 1.25rem; }
    .total-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.4rem 0; font-size: 0.875rem; color: #64748b;
    }
    .total-row.grand {
        padding-top: 0.8rem; margin-top: 0.5rem; border-top: 1.5px solid #e2e8f0;
        font-size: 1.1rem; font-weight: 800; color: #0f172a;
    }
    .total-row.grand .price { color: #2563eb; }

    /* Info card */
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 576px) { .info-grid { grid-template-columns: 1fr; } }
    .info-block h6 { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8; margin-bottom: 0.4rem; }
    .info-block p  { font-size: 0.875rem; color: #1e293b; margin: 0; line-height: 1.6; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="account-header" style="margin:-1rem -0.75rem 2rem;padding-left:0.75rem;padding-right:0.75rem;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:rgba(255,255,255,0.6);">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('account.orders') }}" style="color:rgba(255,255,255,0.6);">My Orders</a></li>
                <li class="breadcrumb-item active" style="color:rgba(255,255,255,0.9);">{{ $order->order_number }}</li>
            </ol>
        </nav>
        <h1><i class="fas fa-receipt me-2"></i>Order Details</h1>
    </div>
</div>

<div class="row g-4">
    {{-- ── Account Sidebar ── --}}
    <div class="col-md-3">
        <div class="account-sidebar-card">
            <div class="account-sidebar-user">
                <div class="account-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="account-user-name">{{ auth()->user()->name }}</div>
                <div class="account-user-email">{{ auth()->user()->masked_email }}</div>
            </div>
            <nav class="account-nav">
                <a href="{{ route('account.index') }}" class="account-nav-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('account.orders') }}" class="account-nav-link active">
                    <i class="fas fa-box"></i> My Orders
                </a>
                <a href="{{ route('account.edit') }}" class="account-nav-link">
                    <i class="fas fa-user-edit"></i> Account Details
                </a>
                <div class="account-nav-divider"></div>
                <a href="{{ route('products.index') }}" class="account-nav-link">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
                <a href="{{ route('logout') }}" class="account-nav-link text-danger"
                   onclick="event.preventDefault(); document.getElementById('show-logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="show-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </nav>
        </div>
    </div>

    {{-- ── Order Detail ── --}}
    <div class="col-md-9">

        {{-- Order header card --}}
        <div class="order-card">
            <div class="order-card-header">
                <div>
                    <h5><i class="fas fa-hashtag text-primary me-1"></i>{{ $order->order_number }}</h5>
                    <div style="font-size:0.8rem;color:#64748b;">
                        Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @php
                        $ps = $order->payment_status ?? ($order->sale->payment_status ?? 'pending');
                        $fs = $order->sale->fulfillment_status ?? 'pending';
                    @endphp
                    {{-- Payment status --}}
                    @if($ps == 'paid')
                        <span class="status-badge status-paid"><i class="fas fa-check-circle"></i> Paid</span>
                    @elseif($ps == 'awaiting_payment')
                        <span class="status-badge status-awaiting"><i class="fas fa-hourglass-half"></i> Awaiting Payment</span>
                    @elseif($ps == 'failed')
                        <span class="status-badge status-failed"><i class="fas fa-times-circle"></i> Failed</span>
                    @elseif($ps == 'expired')
                        <span class="status-badge status-expired"><i class="fas fa-clock"></i> Expired</span>
                    @else
                        <span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span>
                    @endif
                    {{-- Fulfillment status --}}
                    @if($fs == 'delivered')
                        <span class="status-badge status-delivered"><i class="fas fa-check-circle"></i> Delivered</span>
                    @elseif($fs == 'shipped')
                        <span class="status-badge status-shipped"><i class="fas fa-truck"></i> Shipped</span>
                    @elseif($fs == 'processing')
                        <span class="status-badge status-processing"><i class="fas fa-cog"></i> Processing</span>
                    @elseif($fs == 'cancelled')
                        <span class="status-badge status-cancelled"><i class="fas fa-times-circle"></i> Cancelled</span>
                    @else
                        <span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span>
                    @endif
                </div>
            </div>
            <div class="order-card-body">
                <div class="info-grid">
                    <div class="info-block">
                        <h6><i class="fas fa-map-marker-alt me-1"></i>Shipping Address</h6>
                        <p>{{ $order->shipping_address ?? 'N/A' }}</p>
                    </div>
                    @if($order->billing_address && $order->billing_address !== $order->shipping_address)
                        <div class="info-block">
                            <h6><i class="fas fa-file-invoice me-1"></i>Billing Address</h6>
                            <p>{{ $order->billing_address }}</p>
                        </div>
                    @endif
                    <div class="info-block">
                        <h6><i class="fas fa-wallet me-1"></i>Payment Method</h6>
                        <p>
                            @php
                                $methodLabels = [
                                    'cod' => 'Cash on Delivery',
                                    'gcash' => 'GCash',
                                    'maya' => 'Maya',
                                    'card' => 'Credit / Debit Card',
                                    'grabpay' => 'GrabPay',
                                ];
                            @endphp
                            {{ $methodLabels[$order->payment_method] ?? ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}
                            @if($order->paymongo_payment_intent_id)
                                <br><span style="font-size:0.75rem;color:#94a3b8;">Ref: {{ $order->paymongo_payment_intent_id }}</span>
                            @endif
                        </p>
                    </div>
                    @if($order->notes)
                        <div class="info-block">
                            <h6><i class="fas fa-sticky-note me-1"></i>Order Notes</h6>
                            <p>{{ $order->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="order-card">
            <div class="order-card-header">
                <h5><i class="fas fa-boxes me-2 text-primary"></i>Items Ordered</h5>
                <span class="text-muted" style="font-size:0.82rem;">{{ $order->items->count() }} item(s)</span>
            </div>
            <div class="order-card-body" style="padding-top:0.5rem;padding-bottom:0.5rem;">
                @foreach($order->items as $item)
                    <div class="order-item-row">
                        <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : 'https://placehold.co/120x120/f1f5f9/94a3b8?text=?' }}"
                             class="order-item-img" alt="{{ $item->product->name }}">
                        <div class="flex-fill">
                            <div class="order-item-name">{{ $item->product->name }}</div>
                            <div class="order-item-cat">{{ $item->product->category->name ?? '' }}</div>
                            <div class="order-item-price">₱{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</div>
                        </div>
                        <div class="order-item-total">₱{{ number_format($item->total_price, 2) }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Order Totals --}}
        <div class="order-card">
            <div class="order-card-header">
                <h5><i class="fas fa-calculator me-2 text-primary"></i>Order Totals</h5>
            </div>
            <div class="order-card-body">
                <div class="totals-block">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span>Tax</span>
                        <span>₱{{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span>Shipping</span>
                        <span>
                            @if($order->shipping == 0)
                                <span class="text-success fw-semibold">Free</span>
                            @else
                                ₱{{ number_format($order->shipping, 2) }}
                            @endif
                        </span>
                    </div>
                    <div class="total-row grand">
                        <span>Total</span>
                        <span class="price">₱{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4 flex-wrap">
                    <a href="{{ route('account.orders') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Orders
                    </a>
                    <a href="{{ route('account.orders.receipt', $order) }}" class="btn btn-outline-dark" target="_blank">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </a>
                    <a href="{{ route('account.orders.receipt.pdf', $order) }}" class="btn btn-outline-success">
                        <i class="fas fa-file-pdf me-2"></i>Download Invoice PDF
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Again
                    </a>
                    @if($order->sale && $order->sale->fulfillment_status === 'pending')
                    <form action="{{ route('account.orders.cancel', $order) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to cancel this order? This cannot be undone.')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-times me-2"></i>Cancel Order
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
