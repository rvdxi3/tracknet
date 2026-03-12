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
                        <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">Stock Alerts</h2>
                        <p class="text-white-50 mb-0" style="font-size:.9rem;">
                            <i class="fas fa-bell me-1"></i> Active stock notifications
                        </p>
                    </div>
                    <a href="{{ route('inventory.stock.index') }}" class="alt-hdr-btn">
                        <i class="fas fa-arrow-left me-2"></i> Back to Stock
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Alerts Table ── --}}
    <div class="alt-card">
        <div class="alt-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="alt-card-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div>
                    <div class="alt-card-title">Active Alerts</div>
                    <div class="alt-card-sub">{{ $alerts->total() }} total alerts</div>
                </div>
            </div>
            @php $unread = $alerts->where('is_read', false)->count(); @endphp
            @if($unread > 0)
            <span class="alt-unread-count">{{ $unread }} unread</span>
            @endif
        </div>
        <div style="padding:0;">
            @if($alerts->isEmpty())
            <div class="alt-empty-state">
                <div class="alt-empty-icon"><i class="fas fa-check-circle"></i></div>
                <div class="alt-empty-text">No alerts at this time</div>
                <div class="alt-empty-sub">Everything looks good — no stock issues detected</div>
            </div>
            @else
            <div class="table-responsive">
                <table class="alt-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts as $alert)
                        <tr class="{{ !$alert->is_read ? 'unread-row' : '' }}">
                            <td>
                                <div class="alt-prod-name">{{ $alert->product->name ?? '—' }}</div>
                                @if($alert->product)
                                    <div class="alt-prod-sku">{{ $alert->product->sku ?? '' }}</div>
                                @endif
                            </td>
                            <td>
                                @php
                                    $typeLabel = ucfirst(str_replace('_', ' ', $alert->type));
                                    $typeClass = match($alert->type) {
                                        'low_stock'    => 'amber',
                                        'out_of_stock' => 'red',
                                        default        => 'blue',
                                    };
                                @endphp
                                <span class="alt-type-pill {{ $typeClass }}">{{ $typeLabel }}</span>
                            </td>
                            <td>
                                <div class="alt-message">{{ $alert->message }}</div>
                            </td>
                            <td>
                                <div style="font-size:.84rem;color:#475569;white-space:nowrap;">
                                    {{ $alert->created_at->format('M d, Y') }}
                                </div>
                                <div style="font-size:.72rem;color:#94a3b8;">
                                    {{ $alert->created_at->format('g:i A') }}
                                </div>
                            </td>
                            <td>
                                @if($alert->is_read)
                                    <span class="alt-status-pill read">
                                        <i class="fas fa-check me-1"></i> Read
                                    </span>
                                @else
                                    <span class="alt-status-pill unread">
                                        <span class="alt-dot"></span> Unread
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($alerts->hasPages())
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3" style="border-top:1px solid #f1f5f9;">
                <div style="font-size:.78rem;color:#64748b;">
                    Showing <strong>{{ $alerts->firstItem() }}–{{ $alerts->lastItem() }}</strong>
                    of <strong>{{ $alerts->total() }}</strong> alerts
                </div>
                <div class="d-flex gap-1 align-items-center">
                    @if($alerts->onFirstPage())
                        <span class="alt-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $alerts->previousPageUrl() }}" class="alt-page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($alerts->getUrlRange(max(1, $alerts->currentPage()-2), min($alerts->lastPage(), $alerts->currentPage()+2)) as $page => $url)
                        <a href="{{ $url }}" class="alt-page-btn {{ $page == $alerts->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($alerts->hasMorePages())
                        <a href="{{ $alerts->nextPageUrl() }}" class="alt-page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="alt-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>

</div>

<style>
.container-fluid { background:#f8fafd; min-height:100vh; }

/* ── Header Button ── */
.alt-hdr-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.28);
    color: #fff; padding: .6rem 1.3rem; border-radius: 12px;
    font-weight: 600; font-size: .88rem; cursor: pointer; text-decoration: none;
    transition: background .15s, border-color .15s, transform .12s;
    backdrop-filter: blur(4px);
}
.alt-hdr-btn:hover  { background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.45); transform: translateY(-1px); color: #fff; text-decoration: none; }
.alt-hdr-btn:active { transform: scale(.95); color: #fff; }

/* ── Card ── */
.alt-card {
    background: #fff; border-radius: 20px;
    box-shadow: 0 4px 20px rgba(13,20,40,.07);
    border: 1.5px solid #f1f5f9; overflow: hidden; margin-bottom: 1.5rem;
}
.alt-card-header {
    padding: 1rem 1.4rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between; gap: .75rem;
}
.alt-card-icon {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .9rem;
    background: linear-gradient(135deg,#dc26261a,#9f1d1d1a); color: #dc2626;
}
.alt-card-title { font-weight: 700; font-size: .9rem; color: #0f172a; }
.alt-card-sub   { font-size: .72rem; color: #94a3b8; }
.alt-unread-count {
    font-size: .72rem; font-weight: 700;
    background: #fee2e2; color: #991b1b;
    padding: .2rem .65rem; border-radius: 20px; border: 1px solid #fecaca;
    white-space: nowrap;
}

/* ── Table ── */
.alt-table { width: 100%; border-collapse: collapse; font-size: .865rem; }
.alt-table thead tr { background: linear-gradient(135deg, #0f172a, #1e3a8a); }
.alt-table th {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #fff; padding: .7rem 1rem; border: none;
}
.alt-table tbody tr { transition: background .12s; }
.alt-table tbody tr:nth-child(even) { background: #f8fafc; }
.alt-table tbody tr:nth-child(odd)  { background: #fff; }
.alt-table tbody tr:hover { background: #eff6ff; }
.alt-table tbody tr.unread-row { background: #fffbeb !important; border-left: 3px solid #fbbf24; }
.alt-table tbody tr.unread-row:hover { background: #fef9c3 !important; }
.alt-table td { padding: .7rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.alt-table tbody tr:last-child td { border-bottom: none; }

/* ── Product Info ── */
.alt-prod-name { font-weight: 700; color: #0f172a; font-size: .875rem; }
.alt-prod-sku  { font-family: monospace; font-size: .72rem; color: #94a3b8; margin-top: .1rem; }

/* ── Type Pills ── */
.alt-type-pill { display: inline-flex; align-items: center; padding: .22rem .7rem; border-radius: 20px; font-size: .7rem; font-weight: 700; white-space: nowrap; }
.alt-type-pill.amber { background: #fef3c7; color: #92400e; }
.alt-type-pill.red   { background: #fee2e2; color: #991b1b; }
.alt-type-pill.blue  { background: #dbeafe; color: #1e40af; }

/* ── Message ── */
.alt-message { font-size: .84rem; color: #374151; line-height: 1.4; }

/* ── Status Pills ── */
.alt-status-pill { display: inline-flex; align-items: center; gap: .3rem; padding: .22rem .7rem; border-radius: 20px; font-size: .7rem; font-weight: 700; white-space: nowrap; }
.alt-status-pill.read   { background: #d1fae5; color: #065f46; }
.alt-status-pill.unread { background: #fee2e2; color: #991b1b; }
.alt-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #dc2626; flex-shrink: 0;
    animation: altPulse 1.5s ease-in-out infinite;
}
@keyframes altPulse { 0%,100% { opacity:1; } 50% { opacity:.4; } }

/* ── Pagination ── */
.alt-page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; border-radius: 8px; padding: 0 .5rem;
    font-size: .8rem; font-weight: 600;
    background: #fff; border: 1.5px solid #e2e8f0; color: #374151;
    text-decoration: none;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.alt-page-btn:hover:not(.disabled):not(.active) { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; transform: translateY(-1px); text-decoration: none; }
.alt-page-btn:active:not(.disabled) { transform: scale(.94); }
.alt-page-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
.alt-page-btn.disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }

/* ── Empty State ── */
.alt-empty-state { text-align: center; padding: 4rem 1rem; }
.alt-empty-icon {
    width: 60px; height: 60px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.5rem; margin-bottom: .75rem;
    background: #d1fae5; color: #16a34a;
}
.alt-empty-text { font-weight: 700; color: #374151; font-size: .95rem; }
.alt-empty-sub  { font-size: .8rem; color: #94a3b8; margin-top: .3rem; }
</style>
@endsection
