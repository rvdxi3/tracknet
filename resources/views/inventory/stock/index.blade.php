@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- ── Page Header ── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#2563eb 100%); margin-top:-1rem; position:relative; overflow:hidden;">
                <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>
                <div style="position:absolute;bottom:-60px;left:40%;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.02);pointer-events:none;"></div>
                <div class="position-relative d-flex align-items-center justify-content-between flex-wrap gap-3" style="z-index:1;">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.9);padding:.35rem 1rem;border-radius:20px;font-size:.7rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">
                                <i class="fas fa-warehouse me-1"></i> Inventory
                            </span>
                        </div>
                        <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">Stock Overview</h2>
                        <p class="text-white-50 mb-0" style="font-size:.9rem;">
                            <i class="fas fa-layer-group me-1"></i> Monitor and manage stock levels
                        </p>
                    </div>
                    <a href="{{ route('inventory.stock.alerts') }}" class="stk-hdr-btn amber">
                        <i class="fas fa-bell me-2"></i> View Alerts
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stock Table ── --}}
    <div class="stk-card">
        <div class="stk-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="stk-card-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <div class="stk-card-title">Inventory Stock Levels</div>
                    <div class="stk-card-sub">{{ $products->total() }} total products</div>
                </div>
            </div>
        </div>
        <div style="padding:0;">
            <div class="table-responsive">
                <table class="stk-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Threshold</th>
                            <th>Status</th>
                            <th style="width:200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        @php
                            $qty       = $product->inventory->quantity ?? 0;
                            $threshold = $product->inventory->low_stock_threshold ?? 5;
                            $outOfStock = $qty == 0;
                            $lowStock   = !$outOfStock && $qty <= $threshold;
                        @endphp
                        <tr>
                            <td>
                                <div class="stk-prod-name">{{ $product->name }}</div>
                            </td>
                            <td><span class="stk-sku-pill">{{ $product->sku }}</span></td>
                            <td><span class="stk-cat-pill">{{ $product->category->name ?? '—' }}</span></td>
                            <td>
                                <span class="stk-qty {{ $outOfStock ? 'out' : ($lowStock ? 'low' : '') }}">{{ $qty }}</span>
                            </td>
                            <td><span style="font-size:.875rem;color:#475569;font-weight:600;">{{ $threshold }}</span></td>
                            <td>
                                @if($outOfStock)
                                    <span class="stk-status-pill out">Out of Stock</span>
                                @elseif($lowStock)
                                    <span class="stk-status-pill low">Low Stock</span>
                                @else
                                    <span class="stk-status-pill in">In Stock</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button onclick="openStkModal('stockin-{{ $product->id }}')" class="stk-in-btn" title="Stock In">
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                    <button onclick="openStkModal('stockout-{{ $product->id }}')" class="stk-out-btn" title="Stock Out" {{ $outOfStock ? 'disabled' : '' }}>
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                    <button onclick="openStkModal('reorder-{{ $product->id }}')" class="stk-reorder-btn" title="Reorder Stock">
                                        <i class="fas fa-truck"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="stk-empty-state">
                                    <div class="stk-empty-icon"><i class="fas fa-layer-group"></i></div>
                                    <div class="stk-empty-text">No products found</div>
                                    <div class="stk-empty-sub">Add products to start tracking stock</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3" style="border-top:1px solid #f1f5f9;">
                <div style="font-size:.78rem;color:#64748b;">
                    Showing <strong>{{ $products->firstItem() }}–{{ $products->lastItem() }}</strong>
                    of <strong>{{ $products->total() }}</strong> products
                </div>
                <div class="d-flex gap-1 align-items-center">
                    @if($products->onFirstPage())
                        <span class="stk-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" class="stk-page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($products->getUrlRange(max(1, $products->currentPage()-2), min($products->lastPage(), $products->currentPage()+2)) as $page => $url)
                        <a href="{{ $url }}" class="stk-page-btn {{ $page == $products->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" class="stk-page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="stk-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════
     REORDER MODALS (per row)
══════════════════════════════════════════ --}}
@foreach($products as $product)
@php
    $rQty       = $product->inventory->quantity ?? 0;
    $rThreshold = $product->inventory->low_stock_threshold ?? 5;
    $rOutOfStock = $rQty == 0;
    $rLowStock   = !$rOutOfStock && $rQty <= $rThreshold;
@endphp
<div class="stk-overlay" id="reorder-{{ $product->id }}">
    <div class="stk-modal" style="max-width:520px;">
        {{-- Gradient header (like supplier view modal) --}}
        <div class="stk-reorder-hdr">
            <button onclick="closeStkModal('reorder-{{ $product->id }}')" class="stk-reorder-hdr-close">&times;</button>
            <div class="stk-reorder-hdr-tag"><i class="fas fa-truck me-1"></i> Reorder Stock</div>
            <div class="stk-reorder-hdr-title">{{ $product->name }}</div>
            <div class="stk-reorder-hdr-sub">{{ $product->sku }} &middot; {{ $product->category->name ?? '—' }}</div>
        </div>

        <div class="stk-modal-body">
            {{-- Product Info Card (matches screenshot layout) --}}
            <div class="stk-prod-card">
                <div class="stk-prod-card-left">
                    <img src="{{ $product->image_url }}"
                         alt="{{ $product->name }}"
                         class="stk-prod-card-img">
                    <div class="stk-prod-card-info">
                        <div class="stk-prod-card-name">{{ $product->name }}</div>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            <span class="stk-sku-pill">{{ $product->sku }}</span>
                            <span class="stk-cat-pill">{{ $product->category->name ?? '—' }}</span>
                        </div>
                    </div>
                </div>
                <div class="stk-prod-card-stats">
                    <div class="stk-stat-col">
                        <div class="stk-stat-label">Current Stock</div>
                        <div class="stk-stat-val {{ $rOutOfStock ? 'out' : ($rLowStock ? 'low' : 'ok') }}">{{ $rQty }}</div>
                    </div>
                    <div class="stk-stat-divider"></div>
                    <div class="stk-stat-col">
                        <div class="stk-stat-label">Threshold</div>
                        <div class="stk-stat-val">{{ $rThreshold }}</div>
                    </div>
                    <div class="stk-stat-divider"></div>
                    <div class="stk-stat-col">
                        <div class="stk-stat-label">Unit Price</div>
                        <div class="stk-stat-val green">₱{{ number_format($product->price, 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- Reorder Form --}}
            <form id="reorderForm-{{ $product->id }}" method="POST" action="{{ route('inventory.stock.processReorder', $product) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="stk-form-label">Supplier <span class="text-danger">*</span></label>
                        <select name="supplier_id" class="stk-form-input" required>
                            <option value="">Select a supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="stk-form-label">Quantity to Order <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="stk-form-input" value="10" min="1" required>
                    </div>
                    <div class="col-md-6">
                        <label class="stk-form-label">Unit Price (₱) <span class="text-danger">*</span></label>
                        <div class="stk-input-group">
                            <span class="stk-input-prefix">₱</span>
                            <input type="number" step="0.01" name="unit_price" class="stk-form-input"
                                   value="{{ $product->price }}" min="0" required>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="stk-modal-footer">
            <button onclick="closeStkModal('reorder-{{ $product->id }}')" class="stk-btn stk-btn-neutral">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button type="submit" form="reorderForm-{{ $product->id }}" class="stk-btn stk-btn-solid-blue">
                <i class="fas fa-truck me-1"></i> Place Reorder
            </button>
        </div>
    </div>
</div>
@endforeach

{{-- ══════════════════════════════════════════
     STOCK IN MODALS (per row)
══════════════════════════════════════════ --}}
@foreach($products as $product)
@php
    $siQty       = $product->inventory->quantity ?? 0;
    $siThreshold = $product->inventory->low_stock_threshold ?? 5;
    $siOutOfStock = $siQty == 0;
    $siLowStock   = !$siOutOfStock && $siQty <= $siThreshold;
@endphp
<div class="stk-overlay" id="stockin-{{ $product->id }}">
    <div class="stk-modal" style="max-width:520px;">
        <div class="stk-modal-hdr-green">
            <button onclick="closeStkModal('stockin-{{ $product->id }}')" class="stk-reorder-hdr-close">&times;</button>
            <div class="stk-reorder-hdr-tag"><i class="fas fa-arrow-down me-1"></i> Stock In</div>
            <div class="stk-reorder-hdr-title">{{ $product->name }}</div>
            <div class="stk-reorder-hdr-sub">{{ $product->sku }} &middot; {{ $product->category->name ?? '—' }}</div>
        </div>

        <div class="stk-modal-body">
            <div class="stk-prod-card">
                <div class="stk-prod-card-left">
                    <img src="{{ $product->image_url }}"
                         alt="{{ $product->name }}" class="stk-prod-card-img">
                    <div class="stk-prod-card-info">
                        <div class="stk-prod-card-name">{{ $product->name }}</div>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            <span class="stk-sku-pill">{{ $product->sku }}</span>
                            <span class="stk-cat-pill">{{ $product->category->name ?? '—' }}</span>
                        </div>
                    </div>
                </div>
                <div class="stk-prod-card-stats">
                    <div class="stk-stat-col">
                        <div class="stk-stat-label">Current Stock</div>
                        <div class="stk-stat-val {{ $siOutOfStock ? 'out' : ($siLowStock ? 'low' : 'ok') }}">{{ $siQty }}</div>
                    </div>
                    <div class="stk-stat-divider"></div>
                    <div class="stk-stat-col">
                        <div class="stk-stat-label">Threshold</div>
                        <div class="stk-stat-val">{{ $siThreshold }}</div>
                    </div>
                </div>
            </div>

            <form id="stockinForm-{{ $product->id }}" method="POST" action="{{ route('inventory.stock.in', $product) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="stk-form-label">Reason <span class="text-danger">*</span></label>
                        <select name="reason" class="stk-form-input" required>
                            <option value="">Select reason</option>
                            <option value="purchase_order">Purchase Order Received</option>
                            <option value="customer_return">Customer Return</option>
                            <option value="manual_adjustment">Manual Adjustment</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="stk-form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="stk-form-input" value="1" min="1" required>
                    </div>
                    <div class="col-12">
                        <label class="stk-form-label">Notes</label>
                        <textarea name="notes" class="stk-form-input" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
            </form>
        </div>

        <div class="stk-modal-footer">
            <button onclick="closeStkModal('stockin-{{ $product->id }}')" class="stk-btn stk-btn-neutral">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button type="submit" form="stockinForm-{{ $product->id }}" class="stk-btn stk-btn-solid-green">
                <i class="fas fa-arrow-down me-1"></i> Confirm Stock In
            </button>
        </div>
    </div>
</div>
@endforeach

{{-- ══════════════════════════════════════════
     STOCK OUT MODALS (per row)
══════════════════════════════════════════ --}}
@foreach($products as $product)
@php
    $soQty       = $product->inventory->quantity ?? 0;
    $soThreshold = $product->inventory->low_stock_threshold ?? 5;
    $soOutOfStock = $soQty == 0;
    $soLowStock   = !$soOutOfStock && $soQty <= $soThreshold;
@endphp
<div class="stk-overlay" id="stockout-{{ $product->id }}">
    <div class="stk-modal" style="max-width:520px;">
        <div class="stk-modal-hdr-red">
            <button onclick="closeStkModal('stockout-{{ $product->id }}')" class="stk-reorder-hdr-close">&times;</button>
            <div class="stk-reorder-hdr-tag"><i class="fas fa-arrow-up me-1"></i> Stock Out</div>
            <div class="stk-reorder-hdr-title">{{ $product->name }}</div>
            <div class="stk-reorder-hdr-sub">{{ $product->sku }} &middot; {{ $product->category->name ?? '—' }}</div>
        </div>

        <div class="stk-modal-body">
            <div class="stk-prod-card">
                <div class="stk-prod-card-left">
                    <img src="{{ $product->image_url }}"
                         alt="{{ $product->name }}" class="stk-prod-card-img">
                    <div class="stk-prod-card-info">
                        <div class="stk-prod-card-name">{{ $product->name }}</div>
                        <div class="d-flex flex-wrap gap-1 mt-1">
                            <span class="stk-sku-pill">{{ $product->sku }}</span>
                            <span class="stk-cat-pill">{{ $product->category->name ?? '—' }}</span>
                        </div>
                    </div>
                </div>
                <div class="stk-prod-card-stats">
                    <div class="stk-stat-col">
                        <div class="stk-stat-label">Current Stock</div>
                        <div class="stk-stat-val {{ $soOutOfStock ? 'out' : ($soLowStock ? 'low' : 'ok') }}">{{ $soQty }}</div>
                    </div>
                    <div class="stk-stat-divider"></div>
                    <div class="stk-stat-col">
                        <div class="stk-stat-label">Threshold</div>
                        <div class="stk-stat-val">{{ $soThreshold }}</div>
                    </div>
                </div>
            </div>

            @if($soQty > 0)
            <form id="stockoutForm-{{ $product->id }}" method="POST" action="{{ route('inventory.stock.out', $product) }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="stk-form-label">Reason <span class="text-danger">*</span></label>
                        <select name="reason" class="stk-form-input" required>
                            <option value="">Select reason</option>
                            <option value="damaged">Damaged</option>
                            <option value="lost_stolen">Lost / Stolen</option>
                            <option value="manual_adjustment">Manual Adjustment</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="stk-form-label">Quantity <span class="text-danger">*</span> <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#94a3b8;">(max: {{ $soQty }})</span></label>
                        <input type="number" name="quantity" class="stk-form-input" value="1" min="1" max="{{ $soQty }}" required>
                    </div>
                    <div class="col-12">
                        <label class="stk-form-label">Notes</label>
                        <textarea name="notes" class="stk-form-input" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
            </form>
            @else
            <div class="stk-empty-state" style="padding:2rem 1rem;">
                <div class="stk-empty-icon" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-box-open"></i></div>
                <div class="stk-empty-text">No stock available</div>
                <div class="stk-empty-sub">This product is out of stock</div>
            </div>
            @endif
        </div>

        <div class="stk-modal-footer">
            <button onclick="closeStkModal('stockout-{{ $product->id }}')" class="stk-btn stk-btn-neutral">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            @if($soQty > 0)
            <button type="submit" form="stockoutForm-{{ $product->id }}" class="stk-btn stk-btn-solid-red">
                <i class="fas fa-arrow-up me-1"></i> Confirm Stock Out
            </button>
            @endif
        </div>
    </div>
</div>
@endforeach

<style>
.container-fluid { background:#f8fafd; min-height:100vh; }

/* ── Header Buttons ── */
.stk-hdr-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.28);
    color: #fff; padding: .6rem 1.3rem; border-radius: 12px;
    font-weight: 600; font-size: .88rem; cursor: pointer; text-decoration: none;
    transition: background .15s, border-color .15s, transform .12s;
    backdrop-filter: blur(4px);
}
.stk-hdr-btn:hover { background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.45); transform: translateY(-1px); color: #fff; text-decoration: none; }
.stk-hdr-btn:active { transform: scale(.95); color: #fff; }
.stk-hdr-btn.amber { background: rgba(245,158,11,.22); border-color: rgba(252,211,77,.4); }
.stk-hdr-btn.amber:hover { background: rgba(245,158,11,.35); border-color: rgba(252,211,77,.65); }

/* ── Card ── */
.stk-card {
    background: #fff; border-radius: 20px;
    box-shadow: 0 4px 20px rgba(13,20,40,.07);
    border: 1.5px solid #f1f5f9; overflow: hidden; margin-bottom: 1.5rem;
}
.stk-card-header {
    padding: 1rem 1.4rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.stk-card-icon {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .9rem;
    background: linear-gradient(135deg,#2563eb1a,#1e3a8a1a); color: #2563eb;
}
.stk-card-title { font-weight: 700; font-size: .9rem; color: #0f172a; }
.stk-card-sub   { font-size: .72rem; color: #94a3b8; }

/* ── Table ── */
.stk-table { width: 100%; border-collapse: collapse; font-size: .865rem; }
.stk-table thead tr { background: linear-gradient(135deg, #0f172a, #1e3a8a); }
.stk-table th {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #fff; padding: .7rem 1rem; border: none;
}
.stk-table tbody tr { transition: background .12s; }
.stk-table tbody tr:nth-child(even) { background: #f8fafc; }
.stk-table tbody tr:nth-child(odd)  { background: #fff; }
.stk-table tbody tr:hover { background: #eff6ff; }
.stk-table td { padding: .7rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.stk-table tbody tr:last-child td { border-bottom: none; }

/* ── Product Name ── */
.stk-prod-name { font-weight: 700; color: #0f172a; font-size: .875rem; }

/* ── Pills ── */
.stk-sku-pill {
    font-family: monospace; font-size: .78rem; font-weight: 700;
    background: #f1f5f9; color: #475569;
    padding: .15rem .55rem; border-radius: 6px; border: 1px solid #e2e8f0;
}
.stk-cat-pill {
    font-size: .72rem; font-weight: 600;
    background: #dbeafe; color: #1e40af;
    padding: .2rem .6rem; border-radius: 20px;
}

/* ── Quantity ── */
.stk-qty { font-weight: 800; font-size: .95rem; color: #0f172a; }
.stk-qty.low { color: #b45309; }
.stk-qty.out { color: #dc2626; }

/* ── Status Pills ── */
.stk-status-pill { display: inline-flex; align-items: center; padding: .22rem .7rem; border-radius: 20px; font-size: .7rem; font-weight: 700; white-space: nowrap; }
.stk-status-pill.in  { background: #d1fae5; color: #065f46; }
.stk-status-pill.low { background: #fef3c7; color: #92400e; }
.stk-status-pill.out { background: #fee2e2; color: #991b1b; }

/* ── Reorder Button ── */
.stk-reorder-btn {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .38rem .85rem; border-radius: 9px;
    background: #eff6ff; border: 1.5px solid #bfdbfe; color: #2563eb;
    font-size: .78rem; font-weight: 600; cursor: pointer;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.stk-reorder-btn:hover  { background: #dbeafe; border-color: #93c5fd; color: #1e40af; transform: translateY(-1px); }
.stk-reorder-btn:active { background: #bfdbfe; border-color: #60a5fa; color: #1e3a8a; transform: scale(.94); }

/* ── Stock In Button (green) ── */
.stk-in-btn {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .38rem .85rem; border-radius: 9px;
    background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #059669;
    font-size: .78rem; font-weight: 600; cursor: pointer;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.stk-in-btn:hover  { background: #d1fae5; border-color: #6ee7b7; color: #047857; transform: translateY(-1px); }
.stk-in-btn:active { background: #a7f3d0; border-color: #34d399; color: #065f46; transform: scale(.94); }

/* ── Stock Out Button (red) ── */
.stk-out-btn {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .38rem .85rem; border-radius: 9px;
    background: #fef2f2; border: 1.5px solid #fecaca; color: #dc2626;
    font-size: .78rem; font-weight: 600; cursor: pointer;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.stk-out-btn:hover  { background: #fee2e2; border-color: #fca5a5; color: #b91c1c; transform: translateY(-1px); }
.stk-out-btn:active { background: #fecaca; border-color: #f87171; color: #991b1b; transform: scale(.94); }
.stk-out-btn:disabled { opacity: .4; cursor: not-allowed; pointer-events: none; }

/* ── Pagination ── */
.stk-page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; border-radius: 8px; padding: 0 .5rem;
    font-size: .8rem; font-weight: 600;
    background: #fff; border: 1.5px solid #e2e8f0; color: #374151;
    text-decoration: none;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.stk-page-btn:hover:not(.disabled):not(.active) { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; transform: translateY(-1px); text-decoration: none; }
.stk-page-btn:active:not(.disabled) { transform: scale(.94); }
.stk-page-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
.stk-page-btn.disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }

/* ── Empty State ── */
.stk-empty-state { text-align: center; padding: 3rem 1rem; }
.stk-empty-icon {
    width: 56px; height: 56px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.4rem; margin-bottom: .75rem;
    background: #dbeafe; color: #2563eb;
}
.stk-empty-text { font-weight: 700; color: #374151; font-size: .95rem; }
.stk-empty-sub  { font-size: .8rem; color: #94a3b8; margin-top: .3rem; }

/* ── Overlay / Modal ── */
.stk-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,.55); backdrop-filter: blur(3px);
    z-index: 1055; align-items: center; justify-content: center; padding: 1rem;
}
.stk-overlay.open { display: flex; }

.stk-modal {
    background: #fff; border-radius: 20px;
    box-shadow: 0 20px 60px rgba(15,23,42,.25);
    width: 100%; max-height: 90vh; overflow: hidden;
    display: flex; flex-direction: column;
    animation: stkModalIn .2s ease;
    margin: auto;
}
@keyframes stkModalIn { from { opacity:0; transform:translateY(-16px) scale(.97); } to { opacity:1; transform:none; } }

/* ── Reorder Modal Gradient Header ── */
.stk-reorder-hdr {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
    padding: 1.5rem 1.5rem 1.3rem;
    position: relative; border-radius: 20px 20px 0 0; flex-shrink: 0; overflow: hidden;
}
.stk-reorder-hdr::before {
    content:''; position:absolute; top:-30px; right:-30px;
    width:130px; height:130px; border-radius:50%;
    background:rgba(255,255,255,.05); pointer-events:none;
}
.stk-reorder-hdr::after {
    content:''; position:absolute; bottom:-40px; left:30%;
    width:160px; height:160px; border-radius:50%;
    background:rgba(255,255,255,.03); pointer-events:none;
}
.stk-reorder-hdr-tag {
    font-size:.65rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase;
    color:rgba(255,255,255,.55); margin-bottom:.5rem; position:relative; z-index:1;
}
.stk-reorder-hdr-title {
    font-size:1.4rem; font-weight:800; color:#fff;
    line-height:1.2; margin-bottom:.3rem; position:relative; z-index:1;
}
.stk-reorder-hdr-sub {
    font-size:.82rem; color:rgba(255,255,255,.55); position:relative; z-index:1;
}
.stk-reorder-hdr-close {
    position:absolute; top:1rem; right:1rem; z-index:2;
    width:34px; height:34px; border-radius:50%;
    display:flex; align-items:center; justify-content:center;
    font-size:1.2rem; line-height:1; cursor:pointer;
    background:rgba(255,255,255,.15); border:1.5px solid rgba(255,255,255,.25); color:#fff;
    transition:background .15s, border-color .15s, transform .12s;
}
.stk-reorder-hdr-close:hover  { background:rgba(239,68,68,.55); border-color:rgba(239,68,68,.75); }
.stk-reorder-hdr-close:active { background:rgba(239,68,68,.75); border-color:#ef4444; transform:scale(.88); }

.stk-modal-body { padding: 1.2rem 1.4rem; overflow-y: auto; flex: 1; }
.stk-modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: .6rem;
    padding: .9rem 1.4rem; border-top: 1px solid #f1f5f9; flex-shrink: 0;
    background: #f8fafc;
}

/* ── Product Info Card (matches screenshot) ── */
.stk-prod-card {
    background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px;
    padding: .9rem 1rem; margin-bottom: 1rem;
    display: flex; align-items: center; gap: .9rem;
}
.stk-prod-card-left {
    display: flex; align-items: center; gap: .75rem; flex: 1; min-width: 0;
}
.stk-prod-card-img {
    width: 52px; height: 52px; object-fit: cover;
    border-radius: 10px; border: 1.5px solid #dbeafe; flex-shrink: 0;
}
.stk-prod-card-info { min-width: 0; }
.stk-prod-card-name {
    font-weight: 700; color: #0f172a; font-size: .88rem;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* ── Stats section (right side of card) ── */
.stk-prod-card-stats {
    display: flex; align-items: center; gap: 0; flex-shrink: 0;
    border-left: 1.5px solid #e2e8f0; padding-left: .9rem; margin-left: .25rem;
}
.stk-stat-col { text-align: center; padding: 0 .65rem; }
.stk-stat-label {
    font-size: .6rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: #94a3b8; margin-bottom: .2rem;
    white-space: nowrap;
}
.stk-stat-val { font-size: 1rem; font-weight: 800; color: #0f172a; }
.stk-stat-val.ok    { color: #2563eb; }
.stk-stat-val.low   { color: #b45309; }
.stk-stat-val.out   { color: #dc2626; }
.stk-stat-val.green { color: #16a34a; }
.stk-stat-divider { width: 1px; height: 28px; background: #e2e8f0; flex-shrink: 0; }

/* ── Form Inputs ── */
.stk-form-label {
    display: block; font-size: .74rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #475569; margin-bottom: .35rem;
}
.stk-form-input {
    width: 100%; border-radius: 10px; border: 1.5px solid #e2e8f0;
    padding: .5rem .75rem; font-size: .875rem; color: #0f172a;
    background: #f8fafc; outline: none;
    transition: border-color .15s, background .15s, box-shadow .15s;
}
.stk-form-input:focus { border-color: #93c5fd; background: #fff; box-shadow: 0 0 0 3px rgba(147,197,253,.2); }
select.stk-form-input { appearance: auto; cursor: pointer; }

.stk-input-group { position: relative; display: flex; align-items: center; }
.stk-input-prefix {
    position: absolute; left: .75rem;
    font-weight: 700; color: #94a3b8; font-size: .875rem; pointer-events: none;
}
.stk-input-group .stk-form-input { padding-left: 1.6rem; }

/* ── Modal Buttons ── */
.stk-btn {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .45rem 1rem; border-radius: 10px;
    font-weight: 600; font-size: .82rem; cursor: pointer;
    border: 1.5px solid transparent;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.stk-btn:active { transform: scale(.95); }

.stk-btn-blue    { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
.stk-btn-blue:hover    { background: #dbeafe; border-color: #93c5fd; color: #1e3a8a; }
.stk-btn-blue:active   { background: #bfdbfe; border-color: #60a5fa; }

/* Solid blue CTA (Place Reorder — matches screenshot) */
.stk-btn-solid-blue {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    border-color: transparent; color: #fff;
    box-shadow: 0 2px 8px rgba(37,99,235,.35);
}
.stk-btn-solid-blue:hover  { background: linear-gradient(135deg, #1d4ed8, #1e3a8a); box-shadow: 0 4px 14px rgba(37,99,235,.45); transform: translateY(-1px); }
.stk-btn-solid-blue:active { background: linear-gradient(135deg, #1e40af, #1e3a8a); box-shadow: none; transform: scale(.96); }

.stk-btn-neutral { background: #f8fafc; border-color: #e2e8f0; color: #475569; }
.stk-btn-neutral:hover  { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
.stk-btn-neutral:active { background: #fecaca; border-color: #ef4444; color: #b91c1c; }

/* Solid green CTA (Stock In) */
.stk-btn-solid-green {
    background: linear-gradient(135deg, #059669, #047857);
    border-color: transparent; color: #fff;
    box-shadow: 0 2px 8px rgba(5,150,105,.35);
}
.stk-btn-solid-green:hover  { background: linear-gradient(135deg, #047857, #065f46); box-shadow: 0 4px 14px rgba(5,150,105,.45); transform: translateY(-1px); }
.stk-btn-solid-green:active { background: linear-gradient(135deg, #065f46, #064e3b); box-shadow: none; transform: scale(.96); }

/* Solid red CTA (Stock Out) */
.stk-btn-solid-red {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    border-color: transparent; color: #fff;
    box-shadow: 0 2px 8px rgba(220,38,38,.35);
}
.stk-btn-solid-red:hover  { background: linear-gradient(135deg, #b91c1c, #991b1b); box-shadow: 0 4px 14px rgba(220,38,38,.45); transform: translateY(-1px); }
.stk-btn-solid-red:active { background: linear-gradient(135deg, #991b1b, #7f1d1d); box-shadow: none; transform: scale(.96); }

/* ── Green Modal Header (Stock In) ── */
.stk-modal-hdr-green {
    background: linear-gradient(135deg, #064e3b 0%, #059669 60%, #10b981 100%);
    padding: 1.5rem 1.5rem 1.3rem;
    position: relative; border-radius: 20px 20px 0 0; flex-shrink: 0; overflow: hidden;
}
.stk-modal-hdr-green::before {
    content:''; position:absolute; top:-30px; right:-30px;
    width:130px; height:130px; border-radius:50%;
    background:rgba(255,255,255,.05); pointer-events:none;
}
.stk-modal-hdr-green::after {
    content:''; position:absolute; bottom:-40px; left:30%;
    width:160px; height:160px; border-radius:50%;
    background:rgba(255,255,255,.03); pointer-events:none;
}

/* ── Red Modal Header (Stock Out) ── */
.stk-modal-hdr-red {
    background: linear-gradient(135deg, #7f1d1d 0%, #dc2626 60%, #ef4444 100%);
    padding: 1.5rem 1.5rem 1.3rem;
    position: relative; border-radius: 20px 20px 0 0; flex-shrink: 0; overflow: hidden;
}
.stk-modal-hdr-red::before {
    content:''; position:absolute; top:-30px; right:-30px;
    width:130px; height:130px; border-radius:50%;
    background:rgba(255,255,255,.05); pointer-events:none;
}
.stk-modal-hdr-red::after {
    content:''; position:absolute; bottom:-40px; left:30%;
    width:160px; height:160px; border-radius:50%;
    background:rgba(255,255,255,.03); pointer-events:none;
}

/* Shared text styles for green/red headers */
.stk-modal-hdr-green .stk-reorder-hdr-tag,
.stk-modal-hdr-red .stk-reorder-hdr-tag {
    font-size:.65rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase;
    color:rgba(255,255,255,.55); margin-bottom:.5rem; position:relative; z-index:1;
}
.stk-modal-hdr-green .stk-reorder-hdr-title,
.stk-modal-hdr-red .stk-reorder-hdr-title {
    font-size:1.4rem; font-weight:800; color:#fff;
    line-height:1.2; margin-bottom:.3rem; position:relative; z-index:1;
}
.stk-modal-hdr-green .stk-reorder-hdr-sub,
.stk-modal-hdr-red .stk-reorder-hdr-sub {
    font-size:.82rem; color:rgba(255,255,255,.55); position:relative; z-index:1;
}
</style>

<script>
function openStkModal(id) {
    document.querySelectorAll('.stk-overlay.open').forEach(function(el) { el.classList.remove('open'); });
    var el = document.getElementById(id);
    if (el) el.classList.add('open');
}
function closeStkModal(id) {
    var el = document.getElementById(id);
    if (el) el.classList.remove('open');
}
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.stk-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('open');
        });
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.stk-overlay.open').forEach(function(el) { el.classList.remove('open'); });
        }
    });
});
</script>
@endsection
