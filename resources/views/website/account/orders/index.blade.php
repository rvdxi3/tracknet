@extends('website.layouts.app')

@section('title', 'My Orders')

@push('styles')
<style>
    .account-header {
        background: linear-gradient(135deg, #0f172a, #1e3a8a);
        color: #fff;
        padding: 2rem 0;
        margin: -1rem 0 2rem;
    }
    .account-header h1 { font-size: 1.7rem; font-weight: 800; margin: 0; }

    /* Account sidebar */
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
        width: 52px;
        height: 52px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 0.75rem;
    }
    .account-user-name { font-weight: 700; font-size: 0.95rem; }
    .account-user-email { font-size: 0.78rem; opacity: 0.8; }
    .account-nav { padding: 0.5rem 0; }
    .account-nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.7rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #475569;
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: all 0.2s;
    }
    .account-nav-link:hover { background: #f8fafc; color: #2563eb; border-left-color: #93c5fd; }
    .account-nav-link.active { background: #eff6ff; color: #2563eb; border-left-color: #2563eb; font-weight: 600; }
    .account-nav-link i { width: 18px; text-align: center; font-size: 0.85rem; opacity: 0.7; }
    .account-nav-link.active i { opacity: 1; }
    .account-nav-divider { height: 1px; background: #e2e8f0; margin: 0.5rem 0; }

    /* Orders table */
    .orders-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }
    .orders-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .orders-card-header h5 { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; }
    .orders-table th {
        background: #f8fafc;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        padding: 0.9rem 1.25rem;
    }
    .orders-table td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
    .orders-table tbody tr:last-child td { border-bottom: none; }
    .orders-table tbody tr:hover { background: #fafbfc; }

    /* Status badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .status-delivered  { background: #d1fae5; color: #065f46; }
    .status-shipped    { background: #dbeafe; color: #1e40af; }
    .status-processing { background: #fef9c3; color: #713f12; }
    .status-cancelled  { background: #fee2e2; color: #991b1b; }
    .status-pending    { background: #f1f5f9; color: #475569; }
    .status-awaiting   { background: #fef3c7; color: #92400e; }
    .status-paid       { background: #d1fae5; color: #065f46; }
    .status-failed     { background: #fee2e2; color: #991b1b; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="account-header" style="margin:-1rem -0.75rem 2rem;padding-left:0.75rem;padding-right:0.75rem;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" style="color:rgba(255,255,255,0.6);">Home</a></li>
                <li class="breadcrumb-item active" style="color:rgba(255,255,255,0.9);">My Orders</li>
            </ol>
        </nav>
        <h1><i class="fas fa-box me-2"></i>My Account</h1>
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
                   onclick="event.preventDefault(); document.getElementById('acct-logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="acct-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </nav>
        </div>
    </div>

    {{-- ── Orders List ── --}}
    <div class="col-md-9">
        <div class="orders-card">
            <div class="orders-card-header">
                <h5><i class="fas fa-list me-2 text-primary"></i>Order History</h5>
                <span class="text-muted" style="font-size:0.82rem;">{{ $orders->total() }} orders total</span>
            </div>

            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table orders-table mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('account.orders.show', $order) }}"
                                           class="fw-bold text-primary text-decoration-none">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $ps = $order->payment_status ?? ($order->sale->payment_status ?? 'pending');
                                            $fs = $order->sale->fulfillment_status ?? 'pending';
                                        @endphp
                                        {{-- Payment --}}
                                        @if($ps == 'paid')
                                            <span class="status-badge status-paid"><i class="fas fa-check-circle"></i> Paid</span>
                                        @elseif($ps == 'awaiting_payment')
                                            <span class="status-badge status-awaiting"><i class="fas fa-hourglass-half"></i> Awaiting</span>
                                        @elseif($ps == 'failed')
                                            <span class="status-badge status-failed"><i class="fas fa-times-circle"></i> Failed</span>
                                        @else
                                            <span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending</span>
                                        @endif
                                        {{-- Fulfillment --}}
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
                                    </td>
                                    <td>{{ $order->items->count() }} item(s)</td>
                                    <td class="fw-bold" style="color:#2563eb;">₱{{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <a href="{{ route('account.orders.show', $order) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3 d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3 d-block" style="opacity:0.2;"></i>
                    <h5 class="text-muted fw-bold">No orders yet</h5>
                    <p class="text-muted small mb-4">You haven't placed any orders. Start shopping to see your orders here.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary px-4">
                        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
