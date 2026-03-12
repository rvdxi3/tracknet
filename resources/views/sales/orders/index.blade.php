@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- ── Page Header ── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%); margin-top: -1rem; position: relative; overflow: hidden;">
                <div style="position:absolute; top:-40px; right:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.04); pointer-events:none;"></div>
                <div class="position-relative" style="z-index:1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2); color:rgba(255,255,255,0.9); padding:.35rem 1rem; border-radius:20px; font-size:.7rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase;">
                            <i class="fas fa-shopping-bag me-1"></i> Sales Panel
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">Order Management</h2>
                            <p class="text-white-50 mb-0" style="font-size:.9rem;">
                                <i class="fas fa-receipt me-1"></i> {{ $orders->total() }} orders total
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filter Bar ── --}}
    <div class="card border-0 mb-4" style="border-radius:16px; box-shadow:0 4px 20px -4px rgba(13,20,40,.09); border:1px solid #eef2f6 !important;">
        <div class="card-body py-3 px-4">
            <form method="GET" action="{{ route('sales.orders.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="filter-label">Search</label>
                    <div style="position:relative;">
                        <i class="fas fa-search" style="position:absolute; left:.75rem; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:.8rem; pointer-events:none;"></i>
                        <input type="text" class="filter-input" style="padding-left:2.2rem;" name="search"
                               value="{{ request('search') }}" placeholder="Order # or customer name…">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="filter-label">Fulfillment Status</label>
                    <select class="filter-input" name="status">
                        <option value="">All statuses</option>
                        <option value="pending"    {{ request('status') == 'pending'    ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped"    {{ request('status') == 'shipped'    ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered"  {{ request('status') == 'delivered'  ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled"  {{ request('status') == 'cancelled'  ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="filter-label">Payment Status</label>
                    <select class="filter-input" name="payment">
                        <option value="">All payments</option>
                        <option value="pending"  {{ request('payment') == 'pending'  ? 'selected' : '' }}>Pending</option>
                        <option value="paid"     {{ request('payment') == 'paid'     ? 'selected' : '' }}>Paid</option>
                        <option value="failed"   {{ request('payment') == 'failed'   ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('payment') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" style="flex:1; height:42px; border-radius:10px; border:none; background:linear-gradient(135deg,#2563eb,#1e3a8a); color:#fff; font-weight:600; font-size:.85rem; cursor:pointer;">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    @if(request()->hasAny(['search','status','payment']))
                        <a href="{{ route('sales.orders.index') }}" style="width:42px; height:42px; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafd; color:#64748b; display:inline-flex; align-items:center; justify-content:center; text-decoration:none; flex-shrink:0;" title="Clear filters">
                            <i class="fas fa-times" style="font-size:.85rem;"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ── Orders Table ── --}}
    <div class="card border-0" style="border-radius:20px; box-shadow:0 10px 40px -5px rgba(13,20,40,.1); overflow:hidden;">
        <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #eef2f6;">
            <div style="width:38px; height:38px; border-radius:11px; background:linear-gradient(135deg,#2563eb15,#1e3a8a15); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-list" style="color:#2563eb; font-size:.9rem;"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:.95rem;">All Orders</h6>
                <small class="text-muted" style="font-size:.75rem;">{{ $orders->total() }} records</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:.87rem;">
                    <thead>
                        <tr style="background:#f8fafd;">
                            <th class="px-4 py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Order #</th>
                            <th class="py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Customer</th>
                            <th class="py-3 col-hide-xs" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Date</th>
                            <th class="py-3 col-hide-xs" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Items</th>
                            <th class="py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Amount</th>
                            <th class="py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Payment</th>
                            <th class="py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Fulfillment</th>
                            <th class="px-4 py-3" style="border:none;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td class="px-4 py-3">
                                <span style="font-weight:700; color:#2563eb; font-size:.85rem; cursor:pointer;"
                                      onclick="openModal('viewOrderOverlay-{{ $order->id }}')">
                                    {{ $order->order_number }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#2563eb,#1e3a8a); color:#fff; font-weight:800; font-size:.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        {{ strtoupper(substr($order->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span style="font-weight:600; color:#0f172a;">{{ $order->user->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="py-3 col-hide-xs" style="color:#64748b; font-size:.82rem;">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3 col-hide-xs">
                                <span style="display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:6px; background:#eff6ff; color:#2563eb; font-size:.75rem; font-weight:700;">
                                    {{ $order->items->count() }}
                                </span>
                            </td>
                            <td class="py-3">
                                <span style="font-weight:700; color:#0f172a;">₱{{ number_format($order->total, 2) }}</span>
                            </td>
                            <td class="py-3">
                                @if($order->sale)
                                    @php $ps = $order->sale->payment_status; @endphp
                                    @php $psStyle = match($ps) {
                                        'paid'     => 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;',
                                        'refunded' => 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;',
                                        'failed'   => 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;',
                                        default    => 'background:#fef9c3; color:#a16207; border:1px solid #fef08a;',
                                    }; @endphp
                                    <span style="padding:.25rem .65rem; border-radius:20px; font-size:.72rem; font-weight:700; {{ $psStyle }}">{{ ucfirst($ps) }}</span>
                                @else
                                    <span style="padding:.25rem .65rem; border-radius:20px; font-size:.72rem; font-weight:700; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">—</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($order->sale)
                                    @php $fs = $order->sale->fulfillment_status; @endphp
                                    @php $fsStyle = match($fs) {
                                        'delivered'  => 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;',
                                        'shipped'    => 'background:#dbeafe; color:#1d4ed8; border:1px solid #bfdbfe;',
                                        'processing' => 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;',
                                        'cancelled'  => 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;',
                                        default      => 'background:#fef9c3; color:#a16207; border:1px solid #fef08a;',
                                    }; @endphp
                                    <span style="padding:.25rem .65rem; border-radius:20px; font-size:.72rem; font-weight:700; {{ $fsStyle }}">{{ ucfirst($fs) }}</span>
                                @else
                                    <span style="padding:.25rem .65rem; border-radius:20px; font-size:.72rem; font-weight:700; background:#fef9c3; color:#a16207; border:1px solid #fef08a;">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button onclick="openModal('viewOrderOverlay-{{ $order->id }}')"
                                            style="width:32px; height:32px; border-radius:8px; background:#eff6ff; border:1.5px solid #bfdbfe; color:#2563eb; display:inline-flex; align-items:center; justify-content:center; font-size:.78rem; cursor:pointer;"
                                            title="View Order">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(!($order->sale && $order->sale->fulfillment_status === 'delivered'))
                                        <button onclick="openModal('editOrderOverlay-{{ $order->id }}')"
                                                style="width:32px; height:32px; border-radius:8px; background:#f8fafd; border:1.5px solid #e2e8f0; color:#475569; display:inline-flex; align-items:center; justify-content:center; font-size:.78rem; cursor:pointer;"
                                                title="Edit Order">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-inbox mb-3" style="font-size:2.5rem; color:#cbd5e1; display:block;"></i>
                                <div style="font-weight:600; color:#475569; font-size:.9rem;">No orders found</div>
                                @if(request()->hasAny(['search','status','payment']))
                                    <a href="{{ route('sales.orders.index') }}" style="color:#2563eb; font-size:.82rem; text-decoration:none;">Clear filters</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="card-footer bg-transparent px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="border-top:1px solid #eef2f6;">
            <small style="color:#94a3b8; font-size:.78rem;">
                Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }}
            </small>
            <nav class="custom-pagination">
                @if($orders->onFirstPage())
                    <button class="page-btn" disabled><i class="fas fa-chevron-left" style="font-size:.65rem;"></i></button>
                @else
                    <a href="{{ $orders->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left" style="font-size:.65rem;"></i></a>
                @endif
                @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                    @if($page == $orders->currentPage())
                        <button class="page-btn active">{{ $page }}</button>
                    @elseif(abs($page - $orders->currentPage()) <= 2 || $page == 1 || $page == $orders->lastPage())
                        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                    @elseif(abs($page - $orders->currentPage()) == 3)
                        <span class="page-btn" style="cursor:default; color:#94a3b8;">…</span>
                    @endif
                @endforeach
                @if($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i></a>
                @else
                    <button class="page-btn" disabled><i class="fas fa-chevron-right" style="font-size:.65rem;"></i></button>
                @endif
            </nav>
        </div>
        @endif
    </div>

</div>

{{-- ════════════════════════════════════════════
     PER-ROW MODALS
════════════════════════════════════════════ --}}
@foreach($orders as $order)
@php
    $ps = $order->sale?->payment_status ?? null;
    $fs = $order->sale?->fulfillment_status ?? null;
    $psStyle = $ps ? match($ps) {
        'paid'     => 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;',
        'refunded' => 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;',
        'failed'   => 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;',
        default    => 'background:#fef9c3; color:#a16207; border:1px solid #fef08a;',
    } : 'background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;';
    $fsStyle = $fs ? match($fs) {
        'delivered'  => 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;',
        'shipped'    => 'background:#dbeafe; color:#1d4ed8; border:1px solid #bfdbfe;',
        'processing' => 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;',
        'cancelled'  => 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;',
        default      => 'background:#fef9c3; color:#a16207; border:1px solid #fef08a;',
    } : 'background:#fef9c3; color:#a16207; border:1px solid #fef08a;';
@endphp

{{-- VIEW ORDER MODAL --}}
<div class="modal-overlay" id="viewOrderOverlay-{{ $order->id }}" onclick="if(event.target===this)closeModal('viewOrderOverlay-{{ $order->id }}')">
    <div class="modal-box" style="max-width:780px; width:95%;">

        <div class="modal-box-header" style="background:linear-gradient(135deg,#0f172a,#1e3a8a,#2563eb);">
            <div>
                <div style="font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,0.6); margin-bottom:.2rem;">Order Detail</div>
                <div style="font-size:1.15rem; font-weight:800; color:#fff;">{{ $order->order_number }}</div>
                <div style="font-size:.78rem; color:rgba(255,255,255,.55); margin-top:.1rem;">{{ $order->created_at->format('F d, Y \a\t g:i A') }}</div>
            </div>
            <button onclick="closeModal('viewOrderOverlay-{{ $order->id }}')" class="modal-close-btn">&times;</button>
        </div>

        <div class="modal-box-body" style="padding:1.25rem 1.5rem; overflow-y:auto; max-height:65vh;">

            {{-- Customer + Status row --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div style="padding:.85rem 1rem; background:#f8fafd; border:1px solid #eef2f6; border-radius:12px; display:flex; align-items:center; gap:.75rem;">
                        <div style="width:42px; height:42px; border-radius:50%; background:linear-gradient(135deg,#2563eb,#1e3a8a); color:#fff; font-weight:800; font-size:1rem; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            {{ strtoupper(substr($order->user->name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:700; color:#0f172a; font-size:.9rem;">{{ $order->user->name ?? '—' }}</div>
                            <div style="font-size:.75rem; color:#64748b;">{{ $order->user->email ?? '' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div style="padding:.85rem 1rem; background:#f8fafd; border:1px solid #eef2f6; border-radius:12px; height:100%; display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                        <div>
                            <div style="font-size:.65rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.3rem;">Payment</div>
                            <span style="padding:.25rem .7rem; border-radius:20px; font-size:.75rem; font-weight:700; {{ $psStyle }}">{{ ucfirst($ps ?? '—') }}</span>
                        </div>
                        <div style="width:1px; height:30px; background:#e2e8f0;"></div>
                        <div>
                            <div style="font-size:.65rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.3rem;">Fulfillment</div>
                            <span style="padding:.25rem .7rem; border-radius:20px; font-size:.75rem; font-weight:700; {{ $fsStyle }}">{{ ucfirst($fs ?? 'Pending') }}</span>
                        </div>
                        <div style="margin-left:auto;">
                            <div style="font-size:.65rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em; margin-bottom:.3rem;">Total</div>
                            <div style="font-weight:800; color:#2563eb; font-size:1rem;">₱{{ number_format($order->total, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items --}}
            <div style="font-size:.7rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; margin-bottom:.5rem;">
                <i class="fas fa-boxes me-1" style="color:#2563eb;"></i> Items ({{ $order->items->count() }})
            </div>
            <div style="border:1px solid #eef2f6; border-radius:12px; overflow:hidden; margin-bottom:1.25rem;">
                <table style="width:100%; font-size:.84rem; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafd;">
                            <th style="padding:.55rem 1rem; color:#94a3b8; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; text-align:left;">Product</th>
                            <th style="padding:.55rem .75rem; color:#94a3b8; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; text-align:center;">Qty</th>
                            <th style="padding:.55rem .75rem; color:#94a3b8; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; text-align:right;">Unit</th>
                            <th style="padding:.55rem 1rem; color:#94a3b8; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr style="border-top:1px solid #f1f5f9;">
                            <td style="padding:.6rem 1rem;">
                                <div style="font-weight:700; color:#0f172a; font-size:.84rem;">{{ $item->product->name ?? '[Deleted]' }}</div>
                                <div style="font-size:.72rem; color:#64748b;">{{ $item->product->category->name ?? '' }}@if($item->product->sku ?? null) · {{ $item->product->sku }}@endif</div>
                            </td>
                            <td style="padding:.6rem .75rem; text-align:center;">
                                <span style="display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; border-radius:6px; background:#eff6ff; color:#2563eb; font-size:.75rem; font-weight:700;">{{ $item->quantity }}</span>
                            </td>
                            <td style="padding:.6rem .75rem; text-align:right; color:#475569;">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td style="padding:.6rem 1rem; text-align:right; font-weight:700; color:#0f172a;">₱{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8fafd; border-top:1px solid #eef2f6;">
                            <td colspan="3" style="padding:.4rem 1rem; text-align:right; font-size:.75rem; color:#64748b;">Subtotal</td>
                            <td style="padding:.4rem 1rem; text-align:right; color:#0f172a;">₱{{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        <tr style="background:#f8fafd;">
                            <td colspan="3" style="padding:.4rem 1rem; text-align:right; font-size:.75rem; color:#64748b;">Tax</td>
                            <td style="padding:.4rem 1rem; text-align:right; color:#0f172a;">₱{{ number_format($order->tax, 2) }}</td>
                        </tr>
                        <tr style="background:#f8fafd;">
                            <td colspan="3" style="padding:.4rem 1rem; text-align:right; font-size:.75rem; color:#64748b;">Shipping</td>
                            <td style="padding:.4rem 1rem; text-align:right; color:#0f172a;">{{ $order->shipping == 0 ? 'Free' : '₱'.number_format($order->shipping, 2) }}</td>
                        </tr>
                        <tr style="background:#eff6ff; border-top:2px solid #bfdbfe;">
                            <td colspan="3" style="padding:.6rem 1rem; text-align:right; font-weight:800; color:#1e3a8a;">Total</td>
                            <td style="padding:.6rem 1rem; text-align:right; font-weight:800; color:#2563eb; font-size:1rem;">₱{{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Addresses --}}
            <div class="row g-3">
                <div class="col-sm-6">
                    <div style="font-size:.68rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; margin-bottom:.4rem;"><i class="fas fa-truck me-1"></i> Shipping Address</div>
                    <div style="padding:.75rem .9rem; background:#f8fafd; border:1px solid #eef2f6; border-radius:10px; font-size:.84rem; color:#0f172a; line-height:1.5;">{{ $order->shipping_address ?? '—' }}</div>
                </div>
                <div class="col-sm-6">
                    <div style="font-size:.68rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; margin-bottom:.4rem;"><i class="fas fa-file-invoice me-1"></i> Billing Address</div>
                    <div style="padding:.75rem .9rem; background:#f8fafd; border:1px solid #eef2f6; border-radius:10px; font-size:.84rem; color:#0f172a; line-height:1.5;">
                        @if($order->billing_address && $order->billing_address !== $order->shipping_address)
                            {{ $order->billing_address }}
                        @else
                            <span style="color:#94a3b8; font-style:italic;">Same as shipping</span>
                        @endif
                    </div>
                </div>
                @if($order->notes)
                <div class="col-12">
                    <div style="font-size:.68rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.07em; margin-bottom:.4rem;"><i class="fas fa-sticky-note me-1"></i> Notes</div>
                    <div style="padding:.75rem .9rem; background:#fffbeb; border:1px solid #fef08a; border-radius:10px; font-size:.84rem; color:#78350f;">{{ $order->notes }}</div>
                </div>
                @endif
            </div>

        </div>

        {{-- Footer --}}
        <div style="padding:1rem 1.5rem; border-top:1px solid #eef2f6; display:flex; flex-wrap:wrap; gap:.5rem; background:#f8fafd; align-items:center;">
            @if($order->sale)
                @if(!in_array($order->sale->fulfillment_status, ['delivered','cancelled']))
                    <form action="{{ route('sales.orders.fulfill', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Mark as delivered and paid?')" class="od-btn od-btn-green">
                            <i class="fas fa-check"></i> Delivered
                        </button>
                    </form>
                    <form action="{{ route('sales.orders.cancel', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Cancel this order and restore inventory?')" class="od-btn od-btn-red">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </form>
                @endif
                @if($order->sale->payment_status !== 'refunded')
                    <form action="{{ route('sales.orders.refund', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Issue a refund for this order?')" class="od-btn od-btn-amber">
                            <i class="fas fa-undo"></i> Refund
                        </button>
                    </form>
                @endif
            @endif
            @if(!($order->sale && $order->sale->fulfillment_status === 'delivered'))
                <button onclick="closeModal('viewOrderOverlay-{{ $order->id }}'); openModal('editOrderOverlay-{{ $order->id }}')" class="od-btn od-btn-blue">
                    <i class="fas fa-edit"></i> Edit Status
                </button>
            @endif
            <a href="{{ route('sales.orders.receipt', $order) }}" target="_blank" class="od-btn od-btn-neutral">
                <i class="fas fa-print"></i> Print
            </a>
        </div>
    </div>
</div>

{{-- EDIT ORDER MODAL --}}
<div class="modal-overlay" id="editOrderOverlay-{{ $order->id }}" onclick="if(event.target===this)closeModal('editOrderOverlay-{{ $order->id }}')">
    <div class="modal-box" style="max-width:460px; width:95%;">

        <div class="modal-box-header" style="background:linear-gradient(135deg,#0f172a,#1e3a8a,#2563eb);">
            <div>
                <div style="font-size:.7rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,0.6); margin-bottom:.2rem;">Edit Order</div>
                <div style="font-size:1.1rem; font-weight:800; color:#fff;">{{ $order->order_number }}</div>
                <div style="font-size:.78rem; color:rgba(255,255,255,.55);">{{ $order->user->name ?? '—' }}</div>
            </div>
            <button onclick="closeModal('editOrderOverlay-{{ $order->id }}')" class="modal-close-btn">&times;</button>
        </div>

        <div class="modal-box-body" style="padding:1.25rem 1.5rem;">

            {{-- Order preview strip --}}
            <div style="display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; background:#f8fafd; border:1px solid #eef2f6; border-radius:12px; margin-bottom:1.25rem;">
                <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,#2563eb,#1e3a8a); display:flex; align-items:center; justify-content:center; color:#fff; font-size:.8rem; flex-shrink:0;">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <div style="font-weight:700; color:#0f172a; font-size:.875rem;">{{ $order->order_number }}</div>
                    <div style="font-size:.75rem; color:#64748b;">₱{{ number_format($order->total, 2) }} · {{ $order->items->count() }} item(s)</div>
                </div>
            </div>

            <form method="POST" action="{{ route('sales.orders.update', $order) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_order_id" value="{{ $order->id }}">

                <div class="mb-3">
                    <label class="modal-label">Payment Status</label>
                    <div style="position:relative;">
                        <select name="payment_status" class="modal-input @error('payment_status') is-invalid @enderror" style="padding-right:2.5rem;">
                            @foreach(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded'] as $value => $lbl)
                                <option value="{{ $value }}"
                                    {{ old('payment_status', $order->sale->payment_status ?? 'pending') === $value ? 'selected' : '' }}>
                                    {{ $lbl }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.72rem;pointer-events:none;"></i>
                    </div>
                    @error('payment_status')<div class="invalid-feedback d-block" style="font-size:.8rem;">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="modal-label">Fulfillment Status</label>
                    <div style="position:relative;">
                        <select name="fulfillment_status" class="modal-input @error('fulfillment_status') is-invalid @enderror" style="padding-right:2.5rem;">
                            @foreach(['pending' => 'Pending', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $value => $lbl)
                                <option value="{{ $value }}"
                                    {{ old('fulfillment_status', $order->sale->fulfillment_status ?? 'pending') === $value ? 'selected' : '' }}>
                                    {{ $lbl }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.72rem;pointer-events:none;"></i>
                    </div>
                    @error('fulfillment_status')<div class="invalid-feedback d-block" style="font-size:.8rem;">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="modal-label">Order Notes</label>
                    <textarea name="notes" rows="3" class="modal-input @error('notes') is-invalid @enderror"
                              style="height:auto; padding:.65rem .85rem; resize:vertical;"
                              placeholder="Internal notes…">{{ old('notes', $order->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback d-block" style="font-size:.8rem;">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="edit-submit-btn" style="flex:1;">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <button type="button" onclick="closeModal('editOrderOverlay-{{ $order->id }}')" class="edit-cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endforeach

{{-- Validation re-open --}}
@if(old('_order_id'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        openModal('editOrderOverlay-{{ old('_order_id') }}');
    });
</script>
@endif

<style>
.container-fluid { background:#f8fafd; min-height:100vh; }

.filter-label { display:block; font-size:.72rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.35rem; }
.filter-input { width:100%; height:42px; padding:0 .85rem; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafd; color:#0f172a; font-size:.875rem; outline:none; transition:border-color .18s, box-shadow .18s; appearance:none; }
.filter-input:focus { border-color:#2563eb; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.1); }

/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); backdrop-filter:blur(6px); z-index:2000; align-items:center; justify-content:center; padding:1rem; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff; border-radius:20px; box-shadow:0 25px 60px -10px rgba(13,20,40,.25); width:100%; overflow:hidden; animation:modalIn .22s ease; }
@keyframes modalIn { from { opacity:0; transform:scale(.96) translateY(10px); } to { opacity:1; transform:scale(1) translateY(0); } }
.modal-box-header { padding:1.25rem 1.5rem; display:flex; align-items:flex-start; justify-content:space-between; }
.modal-close-btn { width:32px; height:32px; border-radius:50%; border:none; background:rgba(255,255,255,.15); color:rgba(255,255,255,.8); font-size:1.2rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .15s; flex-shrink:0; line-height:1; }
.modal-close-btn:hover { background:rgba(255,255,255,.25); color:#fff; }

.modal-label { display:block; font-size:.78rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.4rem; }
.modal-input { width:100%; height:42px; padding:0 .85rem; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafd; color:#0f172a; font-size:.9rem; outline:none; transition:border-color .18s, box-shadow .18s; appearance:none; }
.modal-input:focus { border-color:#2563eb; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
.modal-input.is-invalid { border-color:#e11d48; }

.edit-submit-btn { padding:.6rem 1.5rem; border-radius:10px; border:none; background:linear-gradient(135deg,#16a34a,#14532d); color:#fff; font-weight:600; font-size:.88rem; cursor:pointer; box-shadow:0 4px 12px rgba(22,163,74,.3); transition:opacity .15s,transform .15s; display:inline-flex; align-items:center; justify-content:center; }
.edit-submit-btn:hover { opacity:.9; transform:translateY(-1px); }
.edit-cancel-btn { padding:.6rem 1.3rem; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafd; color:#475569; font-weight:600; font-size:.88rem; cursor:pointer; transition:background .15s, border-color .15s, color .15s; display:inline-flex; align-items:center; justify-content:center; }
.edit-cancel-btn:hover { background:#fee2e2; color:#dc2626; border-color:#fca5a5; }
.edit-cancel-btn:active { background:#dc2626; color:#fff; border-color:#dc2626; }

/* Modal Action Buttons */
.od-btn { padding:.4rem .9rem; border-radius:9px; font-weight:600; font-size:.8rem; cursor:pointer; display:inline-flex; align-items:center; gap:.3rem; transition:background .15s, border-color .15s, color .15s, transform .12s; }
.od-btn:active { transform:scale(.96); }
.od-btn-green  { border:1.5px solid #86efac; background:#f0fdf4; color:#15803d; }
.od-btn-green:hover  { background:#dcfce7; border-color:#4ade80; color:#166534; }
.od-btn-green:active { background:#bbf7d0; border-color:#22c55e; }
.od-btn-red    { border:1.5px solid #fca5a5; background:#fff1f2; color:#be123c; }
.od-btn-red:hover    { background:#ffe4e6; border-color:#f87171; color:#9f1239; }
.od-btn-red:active   { background:#fecaca; border-color:#ef4444; }
.od-btn-amber  { border:1.5px solid #fde68a; background:#fffbeb; color:#b45309; }
.od-btn-amber:hover  { background:#fef9c3; border-color:#fbbf24; color:#92400e; }
.od-btn-amber:active { background:#fde68a; border-color:#f59e0b; }
.od-btn-blue   { border:1.5px solid #bfdbfe; background:#eff6ff; color:#1d4ed8; }
.od-btn-blue:hover   { background:#dbeafe; border-color:#93c5fd; color:#1e40af; }
.od-btn-blue:active  { background:#bfdbfe; border-color:#60a5fa; }
.od-btn-neutral { border:1.5px solid #e2e8f0; background:#fff; color:#475569; text-decoration:none; }
.od-btn-neutral:hover  { background:#f1f5f9; border-color:#cbd5e1; color:#334155; text-decoration:none; }
.od-btn-neutral:active { background:#e2e8f0; border-color:#94a3b8; }

/* Pagination */
.custom-pagination { display:flex; align-items:center; gap:.3rem; }
.page-btn { min-width:34px; height:34px; border-radius:9px; border:1.5px solid #e2e8f0; background:#fff; color:#475569; font-size:.82rem; font-weight:600; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; transition:all .15s; padding:0 .5rem; }
.page-btn:hover:not(:disabled):not(.active) { border-color:#2563eb; color:#2563eb; background:#eff6ff; }
.page-btn.active { background:linear-gradient(135deg,#2563eb,#1e3a8a); border-color:#2563eb; color:#fff; }
.page-btn:disabled { opacity:.4; cursor:not-allowed; }
</style>

@push('scripts')
<script>
function openModal(id)  { var el=document.getElementById(id); if(el){ el.classList.add('open'); document.body.style.overflow='hidden'; } }
function closeModal(id) { var el=document.getElementById(id); if(el){ el.classList.remove('open'); document.body.style.overflow=''; } }
document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ document.querySelectorAll('.modal-overlay.open').forEach(function(m){ m.classList.remove('open'); }); document.body.style.overflow=''; } });
</script>
@endpush

@endsection
