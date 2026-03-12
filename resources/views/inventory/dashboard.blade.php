@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- ── Page Header ── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#2563eb 100%); margin-top:-1rem; position:relative; overflow:hidden;">
                <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>
                <div style="position:absolute;bottom:-60px;left:40%;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.02);pointer-events:none;"></div>
                <div class="position-relative" style="z-index:1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.9);padding:.35rem 1rem;border-radius:20px;font-size:.7rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">
                            <i class="fas fa-warehouse me-1"></i> Inventory Panel
                        </span>
                    </div>
                    <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">Inventory Dashboard</h2>
                    <p class="text-white-50 mb-0" style="font-size:.9rem;">
                        <i class="fas fa-chart-bar me-1"></i> Overview of your inventory status
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Metric Cards ── --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="inv-metric-card">
                <div class="inv-metric-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="inv-metric-label">Total Products</div>
                <div class="inv-metric-value">{{ number_format($totalProducts) }}</div>
                <div class="inv-metric-sub">in catalog</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="inv-metric-card">
                <div class="inv-metric-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="inv-metric-label">Low Stock Items</div>
                <div class="inv-metric-value">{{ number_format($lowStockProducts) }}</div>
                <div class="inv-metric-sub">need restock</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="inv-metric-card">
                <div class="inv-metric-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="inv-metric-label">Pending Orders</div>
                <div class="inv-metric-value">{{ number_format($pendingPurchaseOrders) }}</div>
                <div class="inv-metric-sub">awaiting processing</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="inv-metric-card">
                <div class="inv-metric-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="inv-metric-label">Active Alerts</div>
                <div class="inv-metric-value">{{ number_format($alerts) }}</div>
                <div class="inv-metric-sub">unread</div>
            </div>
        </div>

    </div>

    {{-- ── Recent Alerts & Purchase Orders ── --}}
    <div class="row g-3">

        {{-- Recent Alerts --}}
        <div class="col-md-6">
            <div class="inv-card h-100">
                <div class="inv-card-header">
                    <div class="inv-card-hdr-left">
                        <div class="inv-card-hdr-icon" style="background:linear-gradient(135deg,#dc26261a,#9f1d1d1a);color:#dc2626;">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <div class="inv-card-title">Recent Alerts</div>
                            <div class="inv-card-sub">Latest stock notifications</div>
                        </div>
                    </div>
                    <a href="{{ route('inventory.stock.alerts') }}" class="inv-view-all-btn">
                        <i class="fas fa-arrow-right me-1"></i> View All
                    </a>
                </div>
                <div class="inv-card-body">
                    @forelse($recentAlerts as $alert)
                    <div class="inv-alert-item {{ $alert->type === 'low_stock' ? 'amber' : 'red' }}">
                        <div class="inv-alert-icon {{ $alert->type === 'low_stock' ? 'amber' : 'red' }}">
                            <i class="fas fa-{{ $alert->type === 'low_stock' ? 'exclamation-triangle' : 'times-circle' }}"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div class="inv-alert-name">{{ $alert->product->name ?? '—' }}</div>
                            <div class="inv-alert-msg">{{ $alert->message }}</div>
                        </div>
                        @if(!$alert->is_read)
                        <span class="inv-unread-dot"></span>
                        @endif
                    </div>
                    @empty
                    <div class="inv-empty-state">
                        <div class="inv-empty-icon" style="background:#d1fae5;color:#16a34a;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="inv-empty-text">No recent alerts</div>
                        <div class="inv-empty-sub">Everything looks good!</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Purchase Orders --}}
        <div class="col-md-6">
            <div class="inv-card h-100">
                <div class="inv-card-header">
                    <div class="inv-card-hdr-left">
                        <div class="inv-card-hdr-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div>
                            <div class="inv-card-title">Recent Purchase Orders</div>
                            <div class="inv-card-sub">Latest procurement activity</div>
                        </div>
                    </div>
                    <a href="{{ route('inventory.purchase-orders.index') }}" class="inv-view-all-btn">
                        <i class="fas fa-arrow-right me-1"></i> View All
                    </a>
                </div>
                <div class="inv-card-body" style="padding:0;">
                    <div class="table-responsive">
                        <table class="inv-table">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Supplier</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPurchaseOrders as $po)
                                <tr>
                                    <td>
                                        <a href="{{ route('inventory.purchase-orders.show', $po) }}" class="inv-po-link">
                                            {{ $po->po_number }}
                                        </a>
                                    </td>
                                    <td style="color:#475569;">{{ $po->supplier->name ?? '—' }}</td>
                                    <td>
                                        @php
                                            $poStyle = match($po->status) {
                                                'delivered' => 'background:#d1fae5;color:#065f46;',
                                                'approved'  => 'background:#dbeafe;color:#1e40af;',
                                                'pending'   => 'background:#fef3c7;color:#92400e;',
                                                default     => 'background:#f1f5f9;color:#475569;',
                                            };
                                        @endphp
                                        <span class="inv-status-pill" style="{{ $poStyle }}">
                                            {{ ucfirst($po->status) }}
                                        </span>
                                    </td>
                                    <td style="color:#64748b;white-space:nowrap;">
                                        {{ $po->order_date instanceof \Carbon\Carbon ? $po->order_date->format('Y-m-d') : $po->order_date }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="inv-empty-state">
                                            <div class="inv-empty-icon" style="background:#dbeafe;color:#2563eb;">
                                                <i class="fas fa-clipboard-list"></i>
                                            </div>
                                            <div class="inv-empty-text">No purchase orders found</div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
.container-fluid { background:#f8fafd; min-height:100vh; }

/* ── Metric Cards ── */
.inv-metric-card {
    background: #fff;
    border-radius: 20px;
    padding: 1.35rem;
    box-shadow: 0 4px 20px rgba(13,20,40,.07);
    border: 1.5px solid #dbeafe;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    height: 100%;
}
.inv-metric-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(37,99,235,.14); }
.inv-metric-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #2563eb, #60a5fa);
}
.inv-metric-card.amber  { border-color: #fde68a; }
.inv-metric-card.amber::before  { background: linear-gradient(90deg, #d97706, #fbbf24); }
.inv-metric-card.cyan   { border-color: #a5f3fc; }
.inv-metric-card.cyan::before   { background: linear-gradient(90deg, #0891b2, #22d3ee); }
.inv-metric-card.red    { border-color: #fecaca; }
.inv-metric-card.red::before    { background: linear-gradient(90deg, #dc2626, #f87171); }

.inv-metric-icon {
    width: 46px; height: 46px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
    margin-bottom: .8rem;
}
.inv-metric-label {
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em;
    color: #64748b; margin-bottom: .3rem;
}
.inv-metric-value { font-size: 1.75rem; font-weight: 800; color: #0f172a; line-height: 1.1; }
.inv-metric-sub   { font-size: .73rem; color: #94a3b8; margin-top: .25rem; }

/* ── Cards ── */
.inv-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(13,20,40,.07);
    border: 1.5px solid #f1f5f9;
    overflow: hidden;
}
.inv-card-header {
    padding: 1rem 1.4rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between; gap: .75rem;
}
.inv-card-hdr-left { display: flex; align-items: center; gap: .75rem; }
.inv-card-hdr-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.inv-card-title { font-weight: 700; font-size: .9rem; color: #0f172a; }
.inv-card-sub   { font-size: .72rem; color: #94a3b8; }
.inv-card-body  { padding: 1rem 1.4rem; }

.inv-view-all-btn {
    padding: .3rem .85rem; border-radius: 8px;
    border: 1.5px solid #bfdbfe; background: #eff6ff;
    color: #2563eb; font-weight: 600; font-size: .78rem;
    text-decoration: none; display: inline-flex; align-items: center;
    transition: background .15s, border-color .15s, color .15s;
    white-space: nowrap;
}
.inv-view-all-btn:hover { background: #dbeafe; border-color: #93c5fd; color: #1e40af; text-decoration: none; }

/* ── Alert Items ── */
.inv-alert-item {
    display: flex; align-items: flex-start; gap: .75rem;
    padding: .75rem .9rem;
    border-radius: 12px;
    margin-bottom: .5rem;
    background: #f8fafd;
    border: 1.5px solid #f1f5f9;
    transition: border-color .15s, background .15s;
}
.inv-alert-item:last-child { margin-bottom: 0; }
.inv-alert-item.amber { background: #fffbeb; border-color: #fde68a; }
.inv-alert-item.red   { background: #fff1f2; border-color: #fecaca; }
.inv-alert-item:hover { filter: brightness(.98); }

.inv-alert-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: .8rem; flex-shrink: 0;
}
.inv-alert-icon.amber { background: #fef3c7; color: #d97706; }
.inv-alert-icon.red   { background: #fee2e2; color: #dc2626; }
.inv-alert-name { font-weight: 700; font-size: .855rem; color: #0f172a; margin-bottom: .1rem; }
.inv-alert-msg  { font-size: .78rem; color: #64748b; }
.inv-unread-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #2563eb;
    flex-shrink: 0;
    margin-top: .3rem;
}

/* ── Table ── */
.inv-table { width: 100%; border-collapse: collapse; font-size: .865rem; }
.inv-table thead tr { background: linear-gradient(135deg, #0f172a, #1e3a8a); }
.inv-table th {
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #fff; padding: .65rem 1rem; border: none;
}
.inv-table tbody tr { transition: background .12s; }
.inv-table tbody tr:nth-child(even) { background: #f8fafc; }
.inv-table tbody tr:nth-child(odd)  { background: #fff; }
.inv-table tbody tr:hover { background: #eff6ff; }
.inv-table td { padding: .65rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.inv-table tbody tr:last-child td { border-bottom: none; }

.inv-po-link { font-weight: 700; color: #2563eb; text-decoration: none; font-size: .84rem; }
.inv-po-link:hover { color: #1e40af; text-decoration: underline; }

.inv-status-pill {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .2rem .65rem; border-radius: 20px;
    font-size: .7rem; font-weight: 700; white-space: nowrap;
}

/* ── Empty State ── */
.inv-empty-state { text-align: center; padding: 2rem 1rem; }
.inv-empty-icon {
    width: 48px; height: 48px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.3rem; margin-bottom: .75rem;
}
.inv-empty-text { font-weight: 700; color: #374151; font-size: .9rem; }
.inv-empty-sub  { font-size: .78rem; color: #94a3b8; margin-top: .2rem; }
</style>
@endsection
