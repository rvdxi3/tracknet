@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- ── Page Header ── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#2563eb 100%); margin-top:-1rem; position:relative; overflow:hidden;">
                <div style="position:absolute;top:-40px;right:-40px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>
                <div style="position:absolute;bottom:-60px;left:40%;width:280px;height:280px;border-radius:50%;background:rgba(255,255,255,.02);pointer-events:none;"></div>
                <div class="position-relative" style="z-index:1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.9);padding:.35rem 1rem;border-radius:20px;font-size:.7rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">
                            <i class="fas fa-receipt me-1"></i> Order Detail
                        </span>
                    </div>
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold text-white mb-1" style="font-size:2rem;letter-spacing:-.01em;">{{ $order->order_number }}</h2>
                            <p class="text-white-50 mb-0" style="font-size:.9rem;">
                                <i class="fas fa-clock me-1"></i> Placed {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                            </p>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            {{-- ── Action Buttons ── --}}
                            @if($order->sale)
                                @if(!in_array($order->sale->fulfillment_status, ['delivered','cancelled']))
                                    <form action="{{ route('sales.orders.fulfill', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Mark this order as delivered and paid?')" class="hdr-btn hdr-btn-green">
                                            <i class="fas fa-check-circle"></i><span>Mark Delivered</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('sales.orders.cancel', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Cancel this order and restore inventory?')" class="hdr-btn hdr-btn-red">
                                            <i class="fas fa-ban"></i><span>Cancel Order</span>
                                        </button>
                                    </form>
                                @endif
                                @if($order->sale->payment_status !== 'refunded')
                                    <form action="{{ route('sales.orders.refund', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Issue a refund for this order?')" class="hdr-btn hdr-btn-amber">
                                            <i class="fas fa-undo"></i><span>Refund</span>
                                        </button>
                                    </form>
                                @endif
                            @endif

                            {{-- ── Divider ── --}}
                            @php $hasActions = $order->sale && (!in_array($order->sale->fulfillment_status, ['delivered','cancelled']) || $order->sale->payment_status !== 'refunded'); @endphp
                            @if($hasActions)
                                <div style="width:1px;height:22px;background:rgba(255,255,255,.2);flex-shrink:0;"></div>
                            @endif

                            {{-- ── Utility Buttons ── --}}
                            @if(!($order->sale && $order->sale->fulfillment_status === 'delivered'))
                                <a href="{{ route('sales.orders.edit', $order) }}" class="hdr-btn hdr-btn-glass">
                                    <i class="fas fa-edit"></i><span>Edit Status</span>
                                </a>
                            @endif
                            <a href="{{ route('sales.orders.receipt', $order) }}" target="_blank" class="hdr-btn hdr-btn-glass">
                                <i class="fas fa-print"></i><span>Print Receipt</span>
                            </a>
                            <a href="{{ route('sales.orders.index') }}" class="hdr-btn hdr-btn-ghost">
                                <i class="fas fa-arrow-left"></i><span>Back</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Left: Items + Addresses ── --}}
        <div class="col-lg-8">

            {{-- Items Card --}}
            <div class="od-card mb-4">
                <div class="od-card-header">
                    <div class="od-card-icon" style="background:linear-gradient(135deg,#2563eb18,#1e3a8a18);color:#2563eb;">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <div class="od-card-title">Items Ordered</div>
                        <div class="od-card-sub">{{ $order->items->count() }} item(s) in this order</div>
                    </div>
                </div>
                <div class="od-card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr class="od-thead">
                                    <th class="px-4 py-3">Product</th>
                                    <th class="py-3 text-center" style="width:80px;">Qty</th>
                                    <th class="py-3 text-end" style="width:120px;">Unit Price</th>
                                    <th class="px-4 py-3 text-end" style="width:130px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr class="od-item-row">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ ($item->product && $item->product->image) ? asset('storage/'.$item->product->image) : 'https://placehold.co/52x52/eff6ff/2563eb?text=?' }}"
                                                 class="od-product-img"
                                                 alt="{{ $item->product->name ?? 'Product' }}">
                                            <div>
                                                <div class="od-product-name">{{ $item->product->name ?? '[Deleted product]' }}</div>
                                                <div class="od-product-meta">
                                                    @if($item->product->category->name ?? null)
                                                        <span>{{ $item->product->category->name }}</span>
                                                    @endif
                                                    @if($item->product->sku ?? null)
                                                        <span class="od-meta-sep">·</span>
                                                        <span>SKU: {{ $item->product->sku }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="od-qty-badge">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="py-3 text-end od-unit-price">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 text-end od-line-total">₱{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="od-tfoot-row">
                                    <td colspan="3" class="px-4 py-2 text-end od-tfoot-label">Subtotal</td>
                                    <td class="px-4 py-2 text-end od-tfoot-value">₱{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr class="od-tfoot-row">
                                    <td colspan="3" class="px-4 py-2 text-end od-tfoot-label">Tax</td>
                                    <td class="px-4 py-2 text-end od-tfoot-value">₱{{ number_format($order->tax, 2) }}</td>
                                </tr>
                                <tr class="od-tfoot-row">
                                    <td colspan="3" class="px-4 py-2 text-end od-tfoot-label">Shipping</td>
                                    <td class="px-4 py-2 text-end od-tfoot-value">
                                        @if($order->shipping == 0)
                                            <span style="color:#16a34a;font-weight:700;">Free</span>
                                        @else
                                            ₱{{ number_format($order->shipping, 2) }}
                                        @endif
                                    </td>
                                </tr>
                                <tr class="od-total-row">
                                    <td colspan="3" class="px-4 py-3 text-end od-total-label">
                                        <i class="fas fa-receipt me-2" style="font-size:.8rem;opacity:.7;"></i>Order Total
                                    </td>
                                    <td class="px-4 py-3 text-end od-total-value">₱{{ number_format($order->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Shipping & Billing Card --}}
            <div class="od-card">
                <div class="od-card-header">
                    <div class="od-card-icon" style="background:linear-gradient(135deg,#16a34a18,#14532d18);color:#16a34a;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <div class="od-card-title">Shipping &amp; Billing</div>
                        <div class="od-card-sub">Delivery information</div>
                    </div>
                </div>
                <div class="od-card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="od-address-block">
                                <div class="od-address-label">
                                    <i class="fas fa-truck me-1"></i> Shipping Address
                                </div>
                                <div class="od-address-text">{{ $order->shipping_address ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="od-address-block">
                                <div class="od-address-label">
                                    <i class="fas fa-file-invoice me-1"></i> Billing Address
                                </div>
                                <div class="od-address-text">
                                    @if($order->billing_address && $order->billing_address !== $order->shipping_address)
                                        {{ $order->billing_address }}
                                    @else
                                        <span style="color:#94a3b8;font-style:italic;">Same as shipping</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if($order->notes)
                        <div class="col-12">
                            <div class="od-notes-block">
                                <div class="od-address-label"><i class="fas fa-sticky-note me-1" style="color:#f59e0b;"></i> Order Notes</div>
                                <div class="od-notes-text">{{ $order->notes }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Right: Status + Customer ── --}}
        <div class="col-lg-4">

            {{-- Order Status Card --}}
            <div class="od-card mb-4">
                <div class="od-card-header">
                    <div class="od-card-icon" style="background:linear-gradient(135deg,#8b5cf618,#7c3aed18);color:#8b5cf6;">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <div class="od-card-title">Order Status</div>
                        <div class="od-card-sub">Current state</div>
                    </div>
                </div>
                <div class="od-card-body">
                    @if($order->sale)
                        @php
                            $ps = $order->sale->payment_status;
                            $fs = $order->sale->fulfillment_status;
                            $psStyle = match($ps) {
                                'paid'     => 'background:#dcfce7;color:#15803d;border:1.5px solid #bbf7d0;',
                                'refunded' => 'background:#e0f2fe;color:#0369a1;border:1.5px solid #bae6fd;',
                                'failed'   => 'background:#fee2e2;color:#b91c1c;border:1.5px solid #fecaca;',
                                default    => 'background:#fef9c3;color:#a16207;border:1.5px solid #fef08a;',
                            };
                            $fsStyle = match($fs) {
                                'delivered'  => 'background:#dcfce7;color:#15803d;border:1.5px solid #bbf7d0;',
                                'shipped'    => 'background:#dbeafe;color:#1d4ed8;border:1.5px solid #bfdbfe;',
                                'processing' => 'background:#e0f2fe;color:#0369a1;border:1.5px solid #bae6fd;',
                                'cancelled'  => 'background:#fee2e2;color:#b91c1c;border:1.5px solid #fecaca;',
                                default      => 'background:#fef9c3;color:#a16207;border:1.5px solid #fef08a;',
                            };
                            $psIcon = match($ps) {
                                'paid'     => 'fa-check-circle',
                                'refunded' => 'fa-undo',
                                'failed'   => 'fa-times-circle',
                                default    => 'fa-clock',
                            };
                            $fsIcon = match($fs) {
                                'delivered'  => 'fa-check-double',
                                'shipped'    => 'fa-shipping-fast',
                                'processing' => 'fa-cog',
                                'cancelled'  => 'fa-ban',
                                default      => 'fa-hourglass-half',
                            };
                        @endphp

                        <div class="od-status-row mb-3">
                            <div class="od-status-key">Payment</div>
                            <span class="od-status-badge" style="{{ $psStyle }}">
                                <i class="fas {{ $psIcon }}"></i> {{ ucfirst($ps) }}
                            </span>
                        </div>
                        <div class="od-status-row mb-4">
                            <div class="od-status-key">Fulfillment</div>
                            <span class="od-status-badge" style="{{ $fsStyle }}">
                                <i class="fas {{ $fsIcon }}"></i> {{ ucfirst($fs) }}
                            </span>
                        </div>
                    @else
                        <div class="od-status-row mb-4">
                            <div class="od-status-key">Status</div>
                            <span class="od-status-badge" style="background:#f1f5f9;color:#475569;border:1.5px solid #e2e8f0;">
                                <i class="fas fa-clock"></i> Awaiting Processing
                            </span>
                        </div>
                    @endif

                    <div style="height:1px;background:#f1f5f9;margin-bottom:1rem;"></div>

                    <div class="od-status-key mb-2">Payment Method</div>
                    <div class="od-payment-method">
                        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#2563eb18,#1e3a8a18);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas fa-credit-card" style="color:#2563eb;font-size:.85rem;"></i>
                        </div>
                        <div>
                            @php
                                $methodLabels = [
                                    'cod' => 'Cash on Delivery',
                                    'gcash' => 'GCash',
                                    'maya' => 'Maya',
                                    'card' => 'Credit / Debit Card',
                                    'grabpay' => 'GrabPay',
                                ];
                            @endphp
                            <span class="od-payment-text">{{ $methodLabels[$order->payment_method] ?? ucfirst(str_replace('_', ' ', $order->payment_method ?? '—')) }}</span>
                            @if($order->paymongo_payment_intent_id)
                                <div style="font-size:.72rem;color:#94a3b8;margin-top:.15rem;">Ref: {{ $order->paymongo_payment_intent_id }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer Card --}}
            <div class="od-card mb-4">
                <div class="od-card-header">
                    <div class="od-card-icon" style="background:linear-gradient(135deg,#0ea5e918,#0284c718);color:#0ea5e9;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="od-card-title">Customer</div>
                        <div class="od-card-sub">Order placed by</div>
                    </div>
                </div>
                <div class="od-card-body">
                    @if($order->user)
                        <div class="od-customer-strip mb-4">
                            <div class="od-customer-avatar">
                                {{ strtoupper(substr($order->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="od-customer-name">{{ $order->user->name }}</div>
                                <div class="od-customer-email">{{ $order->user->email }}</div>
                            </div>
                        </div>
                        <div class="od-stat-row">
                            <div class="od-status-key">Total Orders</div>
                            <div class="od-stat-big">{{ $order->user->orders()->count() }}</div>
                        </div>
                    @else
                        <span style="color:#94a3b8;font-size:.875rem;">Customer data unavailable</span>
                    @endif
                </div>
            </div>

            {{-- Order Meta Card --}}
            <div class="od-card">
                <div class="od-card-header">
                    <div class="od-card-icon" style="background:linear-gradient(135deg,#d9770618,#92400e18);color:#d97706;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="od-card-title">Order Info</div>
                        <div class="od-card-sub">Timeline &amp; reference</div>
                    </div>
                </div>
                <div class="od-card-body">
                    <div class="od-meta-grid">
                        <div class="od-status-key">Order Number</div>
                        <div class="od-meta-val" style="color:#2563eb;font-family:monospace;font-size:.88rem;">{{ $order->order_number }}</div>

                        <div class="od-status-key">Placed On</div>
                        <div class="od-meta-val">{{ $order->created_at->format('M d, Y') }}</div>

                        <div class="od-status-key">Time</div>
                        <div class="od-meta-val">{{ $order->created_at->format('g:i A') }}</div>

                        <div class="od-status-key">Items</div>
                        <div class="od-meta-val">{{ $order->items->count() }} product(s)</div>

                        <div class="od-status-key">Order Total</div>
                        <div class="od-meta-val" style="color:#2563eb;font-weight:800;font-size:1rem;">₱{{ number_format($order->total, 2) }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
.container-fluid { background:#f8fafd; min-height:100vh; }

/* ── Header Buttons ── */
.hdr-btn {
    padding: .42rem .95rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: .82rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    text-decoration: none;
    transition: opacity .15s, transform .12s, box-shadow .15s;
    border: none;
    white-space: nowrap;
}
.hdr-btn:hover { opacity: .88; transform: translateY(-1px); text-decoration: none; }
.hdr-btn:active { transform: translateY(0); }
.hdr-btn-green { background: rgba(34,197,94,.18); border: 1px solid rgba(74,222,128,.45) !important; color: #86efac; }
.hdr-btn-green:hover { background: rgba(34,197,94,.28); border-color: rgba(74,222,128,.7) !important; color: #bbf7d0; }
.hdr-btn-red { background: rgba(239,68,68,.18); border: 1px solid rgba(252,165,165,.45) !important; color: #fca5a5; }
.hdr-btn-red:hover { background: rgba(239,68,68,.28); border-color: rgba(252,165,165,.7) !important; color: #fecaca; }
.hdr-btn-amber { background: rgba(245,158,11,.18); border: 1px solid rgba(252,211,77,.45) !important; color: #fcd34d; }
.hdr-btn-amber:hover { background: rgba(245,158,11,.28); border-color: rgba(252,211,77,.7) !important; color: #fde68a; }
.hdr-btn-glass { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3) !important; color: #fff; }
.hdr-btn-glass:hover { background: rgba(255,255,255,.25); color: #fff; }
.hdr-btn-ghost { background: transparent; border: 1px solid rgba(255,255,255,.2) !important; color: rgba(255,255,255,.72); }
.hdr-btn-ghost:hover { background: rgba(255,255,255,.1); color: #fff; }

/* ── Card Base ── */
.od-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(13,20,40,.09);
    border: 1.5px solid #f1f5f9;
    overflow: hidden;
}
.od-card-header {
    padding: 1rem 1.4rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: .85rem;
}
.od-card-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.od-card-title {
    font-weight: 700;
    font-size: .95rem;
    color: #0f172a;
    line-height: 1.2;
}
.od-card-sub {
    font-size: .75rem;
    color: #94a3b8;
    line-height: 1.2;
}
.od-card-body { padding: 1.25rem 1.4rem; }

/* ── Table ── */
.od-thead th {
    background: #f8fafc;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #94a3b8;
    border: none;
    border-bottom: 1px solid #f1f5f9;
}
.od-item-row { border-bottom: 1px solid #f8fafd; transition: background .12s; }
.od-item-row:last-child { border-bottom: none; }
.od-item-row:hover { background: #f8fafd; }

.od-product-img {
    width: 52px; height: 52px;
    object-fit: contain;
    background: #f8fafc;
    border-radius: 12px;
    padding: 5px;
    border: 1.5px solid #eef2f6;
}
.od-product-name {
    font-weight: 700;
    font-size: .93rem;
    color: #0f172a;
    line-height: 1.3;
}
.od-product-meta {
    font-size: .75rem;
    color: #64748b;
    margin-top: .2rem;
}
.od-meta-sep { margin: 0 .25rem; }

.od-qty-badge {
    display: inline-flex;
    align-items: center; justify-content: center;
    width: 30px; height: 30px;
    border-radius: 8px;
    background: #eff6ff;
    color: #2563eb;
    font-weight: 800;
    font-size: .82rem;
}
.od-unit-price { color: #64748b; font-size: .88rem; vertical-align: middle; }
.od-line-total { font-weight: 700; color: #0f172a; font-size: .93rem; vertical-align: middle; }

/* ── Table Footer ── */
.od-tfoot-row td { background: #f8fafc; border: none; padding-top: .45rem; padding-bottom: .45rem; }
.od-tfoot-label {
    font-size: .75rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.od-tfoot-value { font-size: .88rem; font-weight: 600; color: #374151; }
.od-total-row td {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border-top: 2px solid #bfdbfe !important;
    border-bottom: none;
}
.od-total-label {
    font-size: .92rem;
    font-weight: 800;
    color: #1e3a8a;
}
.od-total-value {
    font-size: 1.25rem;
    font-weight: 900;
    color: #2563eb;
    letter-spacing: -.01em;
}

/* ── Address Blocks ── */
.od-address-block {
    height: 100%;
}
.od-address-label {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #374151;
    margin-bottom: .5rem;
}
.od-address-text {
    padding: .9rem 1rem;
    background: #f8fafd;
    border: 1.5px solid #eef2f6;
    border-radius: 12px;
    font-size: .875rem;
    font-weight: 500;
    color: #0f172a;
    line-height: 1.65;
    min-height: 60px;
}
.od-notes-block { margin-top: .25rem; }
.od-notes-text {
    padding: .9rem 1rem;
    background: #fffbeb;
    border: 1.5px solid #fde68a;
    border-radius: 12px;
    font-size: .875rem;
    font-weight: 500;
    color: #78350f;
    line-height: 1.65;
}

/* ── Status Badges ── */
.od-status-key {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #64748b;
    margin-bottom: .4rem;
}
.od-status-row { }
.od-status-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .45rem 1rem;
    border-radius: 20px;
    font-size: .82rem;
    font-weight: 700;
}
.od-payment-method {
    display: flex;
    align-items: center;
    gap: .75rem;
}
.od-payment-text {
    font-size: .9rem;
    font-weight: 700;
    color: #0f172a;
}

/* ── Customer ── */
.od-customer-strip {
    display: flex;
    align-items: center;
    gap: .85rem;
    padding: .9rem 1rem;
    background: #f8fafd;
    border: 1.5px solid #eef2f6;
    border-radius: 14px;
}
.od-customer-avatar {
    width: 48px; height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb, #1e3a8a);
    color: #fff;
    font-weight: 800;
    font-size: 1.15rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.od-customer-name  { font-weight: 700; color: #0f172a; font-size: .95rem; }
.od-customer-email { font-size: .78rem; color: #64748b; margin-top: .1rem; }

.od-stat-row { }
.od-stat-big {
    font-size: 2rem;
    font-weight: 900;
    color: #0f172a;
    line-height: 1.1;
    letter-spacing: -.02em;
}

/* ── Order Meta Grid ── */
.od-meta-grid {
    display: grid;
    grid-template-columns: auto 1fr;
    row-gap: .7rem;
    column-gap: 1rem;
    align-items: center;
}
.od-meta-val {
    font-size: .875rem;
    font-weight: 600;
    color: #374151;
}
</style>

@endsection
