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
                                <i class="fas fa-exchange-alt me-1"></i> Inventory
                            </span>
                        </div>
                        <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">Movement History</h2>
                        <p class="text-white-50 mb-0" style="font-size:.9rem;">
                            <i class="fas fa-history me-1"></i> Track all stock in and stock out movements
                        </p>
                    </div>
                    <a href="{{ route('inventory.stock.index') }}" class="mv-hdr-btn">
                        <i class="fas fa-arrow-left me-2"></i> Back to Stock
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filters ── --}}
    <div class="mv-card mb-4">
        <div class="mv-card-body">
            <form method="GET" action="{{ route('inventory.stock.movements') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="mv-form-label">Product</label>
                        <select name="product_id" class="mv-form-input">
                            <option value="">All Products</option>
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}" {{ request('product_id') == $prod->id ? 'selected' : '' }}>{{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="mv-form-label">Type</label>
                        <select name="type" class="mv-form-input">
                            <option value="">All</option>
                            <option value="stock_in" {{ request('type') == 'stock_in' ? 'selected' : '' }}>Stock In</option>
                            <option value="stock_out" {{ request('type') == 'stock_out' ? 'selected' : '' }}>Stock Out</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="mv-form-label">Reason</label>
                        <select name="reason" class="mv-form-input">
                            <option value="">All</option>
                            <option value="purchase_order" {{ request('reason') == 'purchase_order' ? 'selected' : '' }}>Purchase Order</option>
                            <option value="customer_return" {{ request('reason') == 'customer_return' ? 'selected' : '' }}>Customer Return</option>
                            <option value="manual_adjustment" {{ request('reason') == 'manual_adjustment' ? 'selected' : '' }}>Manual Adjustment</option>
                            <option value="damaged" {{ request('reason') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                            <option value="lost_stolen" {{ request('reason') == 'lost_stolen' ? 'selected' : '' }}>Lost / Stolen</option>
                            <option value="expired" {{ request('reason') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="customer_order" {{ request('reason') == 'customer_order' ? 'selected' : '' }}>Customer Order</option>
                            <option value="order_cancelled" {{ request('reason') == 'order_cancelled' ? 'selected' : '' }}>Order Cancelled</option>
                            <option value="order_refunded" {{ request('reason') == 'order_refunded' ? 'selected' : '' }}>Order Refunded</option>
                            <option value="payment_failed" {{ request('reason') == 'payment_failed' ? 'selected' : '' }}>Payment Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="mv-form-label">From</label>
                        <input type="date" name="date_from" class="mv-form-input" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="mv-form-label">To</label>
                        <input type="date" name="date_to" class="mv-form-input" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="mv-filter-btn">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('inventory.stock.movements') }}" class="mv-clear-btn" title="Clear filters">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Movements Table ── --}}
    <div class="mv-card">
        <div class="mv-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="mv-card-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <div class="mv-card-title">Stock Movements</div>
                    <div class="mv-card-sub">{{ $movements->total() }} total movements</div>
                </div>
            </div>
        </div>
        <div style="padding:0;">
            <div class="table-responsive">
                <table class="mv-table">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Qty</th>
                            <th>Balance</th>
                            <th>User</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>
                            <td>
                                <div style="font-weight:600;color:#0f172a;font-size:.82rem;">{{ $movement->created_at->format('M d, Y') }}</div>
                                <div style="font-size:.72rem;color:#94a3b8;">{{ $movement->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div style="font-weight:700;color:#0f172a;font-size:.85rem;">{{ $movement->product->name ?? '—' }}</div>
                                <span class="mv-sku-pill">{{ $movement->product->sku ?? '—' }}</span>
                            </td>
                            <td>
                                @if($movement->type === 'stock_in')
                                    <span class="mv-type-pill in"><i class="fas fa-arrow-down me-1"></i> Stock In</span>
                                @else
                                    <span class="mv-type-pill out"><i class="fas fa-arrow-up me-1"></i> Stock Out</span>
                                @endif
                            </td>
                            <td>
                                <span class="mv-reason-pill">{{ \App\Models\StockMovement::reasonLabel($movement->reason) }}</span>
                            </td>
                            <td>
                                @if($movement->type === 'stock_in')
                                    <span style="font-weight:800;color:#059669;font-size:.9rem;">+{{ $movement->quantity }}</span>
                                @else
                                    <span style="font-weight:800;color:#dc2626;font-size:.9rem;">-{{ $movement->quantity }}</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight:700;color:#0f172a;font-size:.88rem;">{{ $movement->balance_after }}</span>
                            </td>
                            <td>
                                <span style="font-size:.82rem;color:#475569;">{{ $movement->user->name ?? 'System' }}</span>
                            </td>
                            <td>
                                <span style="font-size:.8rem;color:#64748b;max-width:200px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $movement->notes }}">
                                    {{ $movement->notes ?? '—' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="mv-empty-state">
                                    <div class="mv-empty-icon"><i class="fas fa-exchange-alt"></i></div>
                                    <div class="mv-empty-text">No movements recorded yet</div>
                                    <div class="mv-empty-sub">Stock in/out movements will appear here</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($movements->hasPages())
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3" style="border-top:1px solid #f1f5f9;">
                <div style="font-size:.78rem;color:#64748b;">
                    Showing <strong>{{ $movements->firstItem() }}–{{ $movements->lastItem() }}</strong>
                    of <strong>{{ $movements->total() }}</strong> movements
                </div>
                <div class="d-flex gap-1 align-items-center">
                    @if($movements->onFirstPage())
                        <span class="mv-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $movements->previousPageUrl() }}" class="mv-page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($movements->getUrlRange(max(1, $movements->currentPage()-2), min($movements->lastPage(), $movements->currentPage()+2)) as $page => $url)
                        <a href="{{ $url }}" class="mv-page-btn {{ $page == $movements->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($movements->hasMorePages())
                        <a href="{{ $movements->nextPageUrl() }}" class="mv-page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="mv-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

<style>
.container-fluid { background:#f8fafd; min-height:100vh; }

/* ── Header Buttons ── */
.mv-hdr-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.28);
    color: #fff; padding: .6rem 1.3rem; border-radius: 12px;
    font-weight: 600; font-size: .88rem; cursor: pointer; text-decoration: none;
    transition: background .15s, border-color .15s, transform .12s;
    backdrop-filter: blur(4px);
}
.mv-hdr-btn:hover { background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.45); transform: translateY(-1px); color: #fff; text-decoration: none; }

/* ── Card ── */
.mv-card {
    background: #fff; border-radius: 20px;
    box-shadow: 0 4px 20px rgba(13,20,40,.07);
    border: 1.5px solid #f1f5f9; overflow: hidden; margin-bottom: 1.5rem;
}
.mv-card-body { padding: 1.2rem 1.4rem; }
.mv-card-header {
    padding: 1rem 1.4rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.mv-card-icon {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .9rem;
    background: linear-gradient(135deg,#2563eb1a,#1e3a8a1a); color: #2563eb;
}
.mv-card-title { font-weight: 700; font-size: .9rem; color: #0f172a; }
.mv-card-sub   { font-size: .72rem; color: #94a3b8; }

/* ── Filter Form ── */
.mv-form-label {
    display: block; font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #475569; margin-bottom: .3rem;
}
.mv-form-input {
    width: 100%; border-radius: 10px; border: 1.5px solid #e2e8f0;
    padding: .45rem .7rem; font-size: .82rem; color: #0f172a;
    background: #f8fafc; outline: none;
    transition: border-color .15s, background .15s, box-shadow .15s;
}
.mv-form-input:focus { border-color: #93c5fd; background: #fff; box-shadow: 0 0 0 3px rgba(147,197,253,.2); }
select.mv-form-input { appearance: auto; cursor: pointer; }
.mv-filter-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 38px; height: 38px; border-radius: 10px;
    background: linear-gradient(135deg, #2563eb, #1e40af); border: none; color: #fff;
    font-size: .85rem; cursor: pointer;
    transition: background .15s, transform .12s;
}
.mv-filter-btn:hover { background: linear-gradient(135deg, #1d4ed8, #1e3a8a); transform: translateY(-1px); }
.mv-clear-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 38px; height: 38px; border-radius: 10px;
    background: #f1f5f9; border: 1.5px solid #e2e8f0; color: #64748b;
    font-size: .85rem; cursor: pointer; text-decoration: none;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.mv-clear-btn:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; transform: translateY(-1px); text-decoration: none; }

/* ── Table ── */
.mv-table { width: 100%; border-collapse: collapse; font-size: .865rem; }
.mv-table thead tr { background: linear-gradient(135deg, #0f172a, #1e3a8a); }
.mv-table th {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #fff; padding: .7rem 1rem; border: none;
}
.mv-table tbody tr { transition: background .12s; }
.mv-table tbody tr:nth-child(even) { background: #f8fafc; }
.mv-table tbody tr:nth-child(odd)  { background: #fff; }
.mv-table tbody tr:hover { background: #eff6ff; }
.mv-table td { padding: .7rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.mv-table tbody tr:last-child td { border-bottom: none; }

/* ── Pills ── */
.mv-sku-pill {
    font-family: monospace; font-size: .72rem; font-weight: 700;
    background: #f1f5f9; color: #475569;
    padding: .1rem .45rem; border-radius: 5px; border: 1px solid #e2e8f0;
}
.mv-type-pill {
    display: inline-flex; align-items: center; padding: .2rem .65rem;
    border-radius: 20px; font-size: .7rem; font-weight: 700; white-space: nowrap;
}
.mv-type-pill.in  { background: #d1fae5; color: #065f46; }
.mv-type-pill.out { background: #fee2e2; color: #991b1b; }
.mv-reason-pill {
    font-size: .75rem; font-weight: 600;
    background: #f1f5f9; color: #475569;
    padding: .2rem .6rem; border-radius: 8px;
}

/* ── Pagination ── */
.mv-page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; border-radius: 8px; padding: 0 .5rem;
    font-size: .8rem; font-weight: 600;
    background: #fff; border: 1.5px solid #e2e8f0; color: #374151;
    text-decoration: none;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.mv-page-btn:hover:not(.disabled):not(.active) { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; transform: translateY(-1px); text-decoration: none; }
.mv-page-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
.mv-page-btn.disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }

/* ── Empty State ── */
.mv-empty-state { text-align: center; padding: 3rem 1rem; }
.mv-empty-icon {
    width: 56px; height: 56px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.4rem; margin-bottom: .75rem;
    background: #dbeafe; color: #2563eb;
}
.mv-empty-text { font-weight: 700; color: #374151; font-size: .95rem; }
.mv-empty-sub  { font-size: .8rem; color: #94a3b8; margin-top: .3rem; }
</style>
@endsection
