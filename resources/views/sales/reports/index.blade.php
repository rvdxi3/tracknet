@extends('layouts.app')

@push('styles')
<style>
/* ── Metric Cards ── */
.rp-metric-card {
    background: #fff;
    border-radius: 20px;
    padding: 1.35rem;
    box-shadow: 0 4px 20px rgba(13,20,40,.08);
    border: 1.5px solid #dbeafe;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    height: 100%;
}
.rp-metric-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(37,99,235,.15); }
.rp-metric-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #2563eb, #60a5fa);
}
.rp-metric-card.cancel { border-color: #fee2e2; }
.rp-metric-card.cancel::before { background: linear-gradient(90deg, #dc2626, #f87171); }
.rp-metric-icon {
    width: 46px; height: 46px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
    margin-bottom: .8rem;
}
.rp-metric-label {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #64748b;
    margin-bottom: .3rem;
}
.rp-metric-value {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
}
.rp-metric-sub {
    font-size: .73rem;
    color: #94a3b8;
    margin-top: .25rem;
}

/* ── Chart Cards ── */
.chart-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 20px rgba(13,20,40,.08);
    margin-bottom: 1.5rem;
    overflow: hidden;
    border: 1.5px solid #f1f5f9;
}
.chart-card-header {
    padding: 1rem 1.4rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .5rem;
}
.chart-hdr-left { display: flex; align-items: center; gap: .75rem; }
.chart-hdr-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem;
    flex-shrink: 0;
}
.chart-hdr-title { font-weight: 700; font-size: .9rem; color: #0f172a; margin: 0; line-height: 1.2; }
.chart-hdr-sub   { font-size: .72rem; color: #94a3b8; line-height: 1.2; }
.chart-card-body { padding: 1.2rem 1.4rem; }
.chart-canvas-wrapper    { position: relative; height: 280px; }
.chart-canvas-wrapper-sm { position: relative; height: 230px; }

/* ── Period Filter Pills ── */
.period-pills { display: flex; flex-wrap: wrap; gap: .4rem; }
.period-pill {
    padding: .28rem .82rem;
    border-radius: 20px;
    font-size: .77rem;
    font-weight: 600;
    text-decoration: none;
    border: 1.5px solid #e2e8f0;
    color: #64748b;
    background: #f8fafd;
    transition: all .15s;
    white-space: nowrap;
}
.period-pill:hover { border-color: #93c5fd; color: #2563eb; background: #eff6ff; text-decoration: none; }
.period-pill.active {
    background: linear-gradient(135deg, #2563eb, #1e3a8a);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 3px 10px rgba(37,99,235,.3);
}

/* ── Report Table ── */
.report-table th {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #94a3b8;
    border-top: none;
    padding: .6rem .9rem;
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
}
.report-table td {
    font-size: .855rem;
    padding: .6rem .9rem;
    vertical-align: middle;
    color: #374151;
    border-bottom: 1px solid #f8fafd;
}
.report-table tbody tr:last-child td { border-bottom: none; }
.report-table tbody tr:hover { background: #f8fafc; }

/* ── Status Pill ── */
.status-pill {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .2rem .65rem;
    border-radius: 20px;
    font-size: .7rem;
    font-weight: 700;
    white-space: nowrap;
}

/* ── Rank Badge ── */
.rank-badge {
    width: 26px; height: 26px;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .7rem;
    font-weight: 800;
}

/* ── Recommendation Card ── */
.recommendation-card {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
    color: #fff;
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 0 4px 24px rgba(37,99,235,.25);
}
.recommendation-card h6 {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .09em;
    opacity: .65;
    margin-bottom: 1rem;
}
.rec-item { display: flex; align-items: flex-start; gap: .75rem; margin-bottom: .85rem; }
.rec-item:last-child { margin-bottom: 0; }
.rec-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.rec-text strong { display: block; font-size: .875rem; margin-bottom: .12rem; }
.rec-text span   { font-size: .77rem; opacity: .75; line-height: 1.4; }

/* ── Modal ── */
.modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(15,23,42,.55);
    backdrop-filter: blur(6px);
    z-index: 1055;
    align-items: center; justify-content: center;
    padding: 1rem;
}
.modal-overlay.active { display: flex; }
.modal-box {
    background: #fff;
    border-radius: 20px;
    width: 100%;
    max-width: 660px;
    max-height: 88vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 25px 60px rgba(15,23,42,.28);
    overflow: hidden;
    animation: modalIn .2s ease;
}
@keyframes modalIn {
    from { opacity: 0; transform: translateY(-18px) scale(.97); }
    to   { opacity: 1; transform: none; }
}
.modal-hdr {
    padding: 1.1rem 1.4rem;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
}
.modal-hdr-title { font-size: 1rem; font-weight: 700; color: #fff; }
.modal-hdr-sub   { font-size: .77rem; color: rgba(255,255,255,.6); margin-top: .1rem; }
.modal-close-btn {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: .85rem;
    transition: background .15s; flex-shrink: 0;
}
.modal-close-btn:hover { background: rgba(255,255,255,.28); }
.modal-body   { overflow-y: auto; flex: 1; padding: 1.4rem; }
.modal-footer {
    padding: .85rem 1.4rem;
    border-top: 1px solid #f1f5f9;
    display: flex; gap: .5rem; justify-content: flex-end;
    flex-shrink: 0; background: #f8fafd;
}
.modal-section-label {
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em;
    color: #374151; margin-bottom: .55rem;
}
.modal-info-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: .5rem .75rem; font-size: .855rem;
}
.modal-info-key   { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #64748b; }
.modal-info-value { font-weight: 600; color: #0f172a; }
.modal-items-table th {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #94a3b8;
    padding: .45rem .7rem; background: #f8fafc;
    border-bottom: 1px solid #f1f5f9; border-top: none;
}
.modal-items-table td {
    font-size: .83rem; padding: .5rem .7rem;
    vertical-align: middle; color: #374151;
    border-bottom: 1px solid #f8fafd;
}
.modal-items-table tbody tr:last-child td { border-bottom: none; }
.modal-total-row td { font-size: .88rem; font-weight: 700; color: #0f172a; border-top: 2px solid #e2e8f0 !important; }
.modal-divider { border: none; border-top: 1px solid #f1f5f9; margin: 1rem 0; }

/* ── Export / Action Buttons ── */
.btn-export-csv {
    padding: .38rem .95rem; border-radius: 10px;
    background: #f0fdf4; border: 1.5px solid #86efac;
    color: #15803d; font-weight: 600; font-size: .82rem;
    text-decoration: none; display: inline-flex; align-items: center; gap: .4rem;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
    white-space: nowrap;
}
.btn-export-csv:hover { background: #dcfce7; border-color: #4ade80; color: #166534; transform: translateY(-1px); text-decoration: none; }
.btn-export-pdf {
    padding: .38rem .95rem; border-radius: 10px;
    background: #fff1f2; border: 1.5px solid #fca5a5;
    color: #be123c; font-weight: 600; font-size: .82rem;
    text-decoration: none; display: inline-flex; align-items: center; gap: .4rem;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
    white-space: nowrap;
}
.btn-export-pdf:hover { background: #ffe4e6; border-color: #f87171; color: #9f1239; transform: translateY(-1px); text-decoration: none; }
.btn-view-all {
    padding: .32rem .85rem; border-radius: 8px;
    border: 1.5px solid #2563eb; background: transparent;
    color: #2563eb; font-weight: 600; font-size: .78rem;
    text-decoration: none; transition: all .15s;
    display: inline-flex; align-items: center; gap: .35rem;
}
.btn-view-all:hover { background: #2563eb; color: #fff; text-decoration: none; }
.btn-tx-view {
    padding: .22rem .65rem; border-radius: 7px;
    border: 1.5px solid #2563eb; background: transparent;
    color: #2563eb; font-weight: 600; font-size: .72rem;
    cursor: pointer; transition: all .15s;
    display: inline-flex; align-items: center; gap: .3rem;
}
.btn-tx-view:hover { background: #2563eb; color: #fff; }
.btn-full-detail {
    padding: .42rem 1rem; border-radius: 10px; border: none;
    background: linear-gradient(135deg, #2563eb, #1e3a8a);
    color: #fff; font-weight: 600; font-size: .83rem;
    text-decoration: none; display: inline-flex; align-items: center; gap: .4rem;
    box-shadow: 0 3px 10px rgba(37,99,235,.25);
    transition: opacity .15s, transform .15s;
}
.btn-full-detail:hover { opacity: .9; transform: translateY(-1px); color: #fff; text-decoration: none; }

/* ── Pagination ── */
.rp-pagination { display: flex; gap: .3rem; flex-wrap: wrap; }
.rp-page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; padding: 0 .5rem;
    border-radius: 8px; font-size: .78rem; font-weight: 600;
    text-decoration: none; border: 1.5px solid #e2e8f0;
    color: #64748b; background: #f8fafd; transition: all .15s;
}
.rp-page-btn:hover { border-color: #93c5fd; color: #2563eb; background: #eff6ff; text-decoration: none; }
.rp-page-btn.active { background: linear-gradient(135deg, #2563eb, #1e3a8a); border-color: transparent; color: #fff; box-shadow: 0 2px 8px rgba(37,99,235,.3); }
.rp-page-btn.disabled { opacity: .45; pointer-events: none; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">

    {{-- ── Page Header ── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#2563eb 100%); margin-top:-1rem; position:relative; overflow:hidden;">
                <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>
                <div style="position:absolute;bottom:-60px;left:35%;width:280px;height:280px;border-radius:50%;background:rgba(255,255,255,.02);pointer-events:none;"></div>
                <div class="position-relative" style="z-index:1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.9);padding:.35rem 1rem;border-radius:20px;font-size:.7rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">
                            <i class="fas fa-chart-bar me-1"></i> Analytics
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">Sales Reports &amp; Analytics</h2>
                        <p class="text-white-50 mb-0" style="font-size:.9rem;">
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Period Filter Bar ── --}}
    <div class="card border-0 mb-4" style="border-radius:20px;box-shadow:0 4px 20px rgba(13,20,40,.08);overflow:hidden;">
        <div class="card-body py-3 px-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span style="font-size:.78rem;font-weight:700;color:#374151;white-space:nowrap;flex-shrink:0;">
                    <i class="fas fa-filter me-1" style="color:#2563eb;"></i> Filter Period:
                </span>
                <div class="period-pills">
                    @foreach([
                        '7days'      => '7 Days',
                        '30days'     => '30 Days',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        '3months'    => '3 Months',
                        '6months'    => '6 Months',
                        'this_year'  => 'This Year',
                        '12months'   => '12 Months',
                    ] as $key => $label)
                        <a href="{{ route('sales.reports.index', ['period' => $key]) }}"
                           class="period-pill {{ $period === $key ? 'active' : '' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                <div class="d-flex gap-2 ms-auto flex-shrink-0">
                    <a href="{{ route('sales.reports.export', ['period' => $period]) }}" class="btn-export-csv">
                        <i class="fas fa-file-csv"></i> Export CSV
                    </a>
                    <a href="{{ route('sales.reports.export-pdf', ['period' => $period]) }}" class="btn-export-pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Key Metrics ── --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rp-metric-card">
                <div class="rp-metric-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="rp-metric-label">Total Revenue</div>
                <div class="rp-metric-value">₱{{ number_format($totalRevenue, 0) }}</div>
                <div class="rp-metric-sub">from paid orders</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rp-metric-card">
                <div class="rp-metric-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="rp-metric-label">Total Orders</div>
                <div class="rp-metric-value">{{ number_format($totalOrders) }}</div>
                <div class="rp-metric-sub">all statuses</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rp-metric-card">
                <div class="rp-metric-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="rp-metric-label">Paid Orders</div>
                <div class="rp-metric-value">{{ number_format($paidOrders) }}</div>
                <div class="rp-metric-sub">
                    @if($totalOrders > 0){{ round($paidOrders / $totalOrders * 100) }}% conversion
                    @else N/A @endif
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rp-metric-card">
                <div class="rp-metric-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="rp-metric-label">Avg Order Value</div>
                <div class="rp-metric-value">₱{{ number_format($avgOrderValue, 2) }}</div>
                <div class="rp-metric-sub">per paid order</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rp-metric-card">
                <div class="rp-metric-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="rp-metric-label">New Customers</div>
                <div class="rp-metric-value">{{ number_format($totalCustomers) }}</div>
                <div class="rp-metric-sub">in period</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="rp-metric-card cancel">
                <div class="rp-metric-icon" style="background:linear-gradient(135deg,#dc26261a,#7f1d1d1a);color:#dc2626;">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="rp-metric-label">Cancelled</div>
                <div class="rp-metric-value">{{ number_format($cancelledOrders) }}</div>
                <div class="rp-metric-sub">
                    @if($totalOrders > 0){{ round($cancelledOrders / $totalOrders * 100) }}% cancellation
                    @else N/A @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── Revenue Trend + Order Status ── --}}
    <div class="row g-3 mb-1">
        <div class="col-xl-8">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-hdr-left">
                        <div class="chart-hdr-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <div class="chart-hdr-title">Monthly Revenue Trend</div>
                            <div class="chart-hdr-sub">Last 12 months · Paid orders only</div>
                        </div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div class="chart-canvas-wrapper"><canvas id="monthlyRevenueChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-hdr-left">
                        <div class="chart-hdr-icon" style="background:linear-gradient(135deg,#16a34a1a,#14532d1a);color:#16a34a;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <div class="chart-hdr-title">Order Status</div>
                            <div class="chart-hdr-sub">By fulfillment</div>
                        </div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div class="chart-canvas-wrapper"><canvas id="statusChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Daily Orders + Payment Methods ── --}}
    <div class="row g-3 mb-1">
        <div class="col-xl-8">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-hdr-left">
                        <div class="chart-hdr-icon" style="background:linear-gradient(135deg,#0891b21a,#0e74911a);color:#0891b2;">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <div class="chart-hdr-title">Daily Orders — Last 30 Days</div>
                            <div class="chart-hdr-sub">Order count per day</div>
                        </div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div class="chart-canvas-wrapper"><canvas id="dailyOrdersChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-hdr-left">
                        <div class="chart-hdr-icon" style="background:linear-gradient(135deg,#d977061a,#92400e1a);color:#d97706;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <div class="chart-hdr-title">Payment Methods</div>
                            <div class="chart-hdr-sub">Distribution</div>
                        </div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div class="chart-canvas-wrapper"><canvas id="paymentMethodChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Top Products Chart + Category Revenue ── --}}
    <div class="row g-3 mb-1">
        <div class="col-xl-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-hdr-left">
                        <div class="chart-hdr-icon" style="background:linear-gradient(135deg,#dc26261a,#7f1d1d1a);color:#dc2626;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div>
                            <div class="chart-hdr-title">Top 10 Products by Units Sold</div>
                        </div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div class="chart-canvas-wrapper"><canvas id="topProductsChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-hdr-left">
                        <div class="chart-hdr-icon" style="background:linear-gradient(135deg,#7c3aed1a,#4c1d951a);color:#7c3aed;">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div>
                            <div class="chart-hdr-title">Revenue by Category</div>
                        </div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div class="chart-canvas-wrapper"><canvas id="categoryChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Customer Growth + Payment Status ── --}}
    <div class="row g-3 mb-1">
        <div class="col-xl-8">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-hdr-left">
                        <div class="chart-hdr-icon" style="background:linear-gradient(135deg,#7c3aed1a,#4c1d951a);color:#7c3aed;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="chart-hdr-title">New Customer Growth</div>
                            <div class="chart-hdr-sub">Last 6 months</div>
                        </div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div class="chart-canvas-wrapper-sm"><canvas id="customerGrowthChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <div class="chart-hdr-left">
                        <div class="chart-hdr-icon" style="background:linear-gradient(135deg,#0891b21a,#0e74911a);color:#0891b2;">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div>
                            <div class="chart-hdr-title">Payment Status</div>
                        </div>
                    </div>
                </div>
                <div class="chart-card-body">
                    <div class="chart-canvas-wrapper-sm"><canvas id="paymentStatusChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Recommendations + Top Products Table ── --}}
    <div class="row g-3 mb-1">
        <div class="col-xl-4">
            <div class="recommendation-card h-100">
                <h6><i class="fas fa-lightbulb me-2"></i>Recommendations &amp; Insights</h6>

                @php
                    $convRate    = $totalOrders > 0 ? round($paidOrders / $totalOrders * 100) : 0;
                    $cancelRate  = $totalOrders > 0 ? round($cancelledOrders / $totalOrders * 100) : 0;
                    $topProduct  = $topProducts->first();
                    $topCategory = $categoryRevenue->first();
                @endphp

                <div class="rec-item">
                    <div class="rec-icon">📈</div>
                    <div class="rec-text">
                        <strong>Conversion Rate</strong>
                        <span>
                            @if($convRate >= 70) Your {{ $convRate }}% conversion rate is strong. Maintain by ensuring fast fulfillment.
                            @elseif($convRate >= 50) Your {{ $convRate }}% conversion rate is moderate. Consider following up on pending orders promptly.
                            @else Your {{ $convRate }}% conversion rate needs attention. Review payment failure reasons and offer retry options.
                            @endif
                        </span>
                    </div>
                </div>

                <div class="rec-item">
                    <div class="rec-icon">⚠️</div>
                    <div class="rec-text">
                        <strong>Cancellation Rate</strong>
                        <span>
                            @if($cancelRate >= 20) High cancellation rate ({{ $cancelRate }}%). Investigate causes — stockouts, slow fulfillment, or payment issues.
                            @elseif($cancelRate >= 10) Moderate cancellation rate ({{ $cancelRate }}%). Monitor and identify patterns in cancelled order items.
                            @else Cancellation rate is healthy ({{ $cancelRate }}%). Keep up good fulfillment practices.
                            @endif
                        </span>
                    </div>
                </div>

                @if($topProduct)
                <div class="rec-item">
                    <div class="rec-icon">🏆</div>
                    <div class="rec-text">
                        <strong>Best Seller</strong>
                        <span>"{{ $topProduct->name }}" leads with {{ number_format($topProduct->total_qty) }} units. Ensure sufficient stock and consider promotions on related products.</span>
                    </div>
                </div>
                @endif

                @if($topCategory)
                <div class="rec-item">
                    <div class="rec-icon">🗂️</div>
                    <div class="rec-text">
                        <strong>Top Category</strong>
                        <span>"{{ $topCategory->name }}" generates the most revenue (₱{{ number_format($topCategory->revenue, 0) }}). Expand your product range in this category.</span>
                    </div>
                </div>
                @endif

                <div class="rec-item">
                    <div class="rec-icon">📊</div>
                    <div class="rec-text">
                        <strong>Average Order Value</strong>
                        <span>
                            @if($avgOrderValue > 0)
                                At ₱{{ number_format($avgOrderValue, 2) }} per order, consider upsell strategies — bundles and related items can boost this figure.
                            @else
                                No paid orders yet. Focus on converting pending orders to build this metric.
                            @endif
                        </span>
                    </div>
                </div>

                <div class="rec-item">
                    <div class="rec-icon">👥</div>
                    <div class="rec-text">
                        <strong>Customer Acquisition</strong>
                        <span>
                            @if($totalCustomers > 0)
                                {{ $totalCustomers }} new customer(s) this period. Implement a loyalty program and re-engagement campaigns to boost repeat purchases.
                            @else
                                No new customers this period. Review marketing campaigns and onboarding flow.
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Top Products Table ── --}}
        <div class="col-xl-8">
            <div class="chart-card h-100">
                <div class="chart-card-header">
                    <div class="chart-hdr-left">
                        <div class="chart-hdr-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                            <i class="fas fa-table"></i>
                        </div>
                        <div>
                            <div class="chart-hdr-title">Top Products — Detail</div>
                            <div class="chart-hdr-sub">Ranked by units sold</div>
                        </div>
                    </div>
                </div>
                <div class="chart-card-body" style="padding:0;">
                    <div class="table-responsive">
                        <table class="table report-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width:44px;">#</th>
                                    <th>Product</th>
                                    <th class="text-end">Units Sold</th>
                                    <th class="text-end">Revenue</th>
                                    <th>Revenue Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $i => $product)
                                @php $share = $totalRevenue > 0 ? round($product->total_revenue / $totalRevenue * 100, 1) : 0; @endphp
                                <tr>
                                    <td>
                                        @if($i === 0)
                                            <span class="rank-badge" style="background:#fef3c7;color:#92400e;">1</span>
                                        @elseif($i === 1)
                                            <span class="rank-badge" style="background:#f1f5f9;color:#475569;">2</span>
                                        @elseif($i === 2)
                                            <span class="rank-badge" style="background:#fed7aa;color:#9a3412;">3</span>
                                        @else
                                            <span class="rank-badge" style="background:#f1f5f9;color:#94a3b8;">{{ $i+1 }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold" style="color:#0f172a;">{{ $product->name }}</td>
                                    <td class="text-end">{{ number_format($product->total_qty) }}</td>
                                    <td class="text-end fw-semibold" style="color:#2563eb;">₱{{ number_format($product->total_revenue, 2) }}</td>
                                    <td style="width:180px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="flex:1;height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                                                <div style="height:100%;width:{{ min($share*2, 100) }}%;background:linear-gradient(90deg,#2563eb,#60a5fa);border-radius:3px;"></div>
                                            </div>
                                            <span style="font-size:.72rem;white-space:nowrap;color:#64748b;font-weight:600;">{{ $share }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5" style="color:#94a3b8;">
                                        <i class="fas fa-box-open mb-2 d-block" style="font-size:1.5rem;"></i>
                                        No sales data for this period.
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

    {{-- ── Recent Transactions ── --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div class="chart-hdr-left">
                <div class="chart-hdr-icon" style="background:linear-gradient(135deg,#2563eb1a,#1e3a8a1a);color:#2563eb;">
                    <i class="fas fa-list-alt"></i>
                </div>
                <div>
                    <div class="chart-hdr-title">Recent Transactions</div>
                    <div class="chart-hdr-sub">Latest 10 orders in selected period</div>
                </div>
            </div>
            <a href="{{ route('sales.orders.index') }}" class="btn-view-all">
                <i class="fas fa-arrow-right"></i> View All Orders
            </a>
        </div>
        <div class="chart-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="table report-table mb-0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th>Payment</th>
                            <th>Fulfillment</th>
                            <th style="width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $order)
                        @php
                            $ps = $order->sale?->payment_status;
                            $fs = $order->sale?->fulfillment_status;
                            $payStyle = match($ps) {
                                'paid'     => 'background:#d1fae5;color:#065f46;',
                                'failed'   => 'background:#fee2e2;color:#991b1b;',
                                'refunded' => 'background:#ede9fe;color:#4c1d95;',
                                default    => 'background:#fef3c7;color:#92400e;',
                            };
                            $fulStyle = match($fs) {
                                'delivered'  => 'background:#d1fae5;color:#065f46;',
                                'shipped'    => 'background:#cffafe;color:#164e63;',
                                'processing' => 'background:#dbeafe;color:#1e40af;',
                                'cancelled'  => 'background:#fee2e2;color:#991b1b;',
                                default      => 'background:#f1f5f9;color:#475569;',
                            };
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-bold" style="color:#0f172a;">{{ $order->order_number }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#1e3a8a);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.72rem;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($order->user?->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span style="font-weight:600;color:#0f172a;">{{ $order->user?->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td style="color:#64748b;">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="text-end fw-bold" style="color:#2563eb;">₱{{ number_format($order->total, 2) }}</td>
                            <td>
                                @if($order->sale)
                                    <span class="status-pill" style="{{ $payStyle }}">{{ ucfirst($ps) }}</span>
                                @else
                                    <span class="status-pill" style="background:#f1f5f9;color:#94a3b8;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($order->sale)
                                    <span class="status-pill" style="{{ $fulStyle }}">{{ ucfirst($fs) }}</span>
                                @else
                                    <span class="status-pill" style="background:#f1f5f9;color:#94a3b8;">—</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn-tx-view" onclick="openModal('viewTxOverlay-{{ $order->id }}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5" style="color:#94a3b8;">
                                <i class="fas fa-inbox mb-2 d-block" style="font-size:1.5rem;"></i>
                                No transactions found for this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            @if($recentTransactions->hasPages())
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3" style="border-top:1px solid #f1f5f9;">
                <div style="font-size:.78rem;color:#64748b;">
                    Showing <strong style="color:#0f172a;">{{ $recentTransactions->firstItem() }}–{{ $recentTransactions->lastItem() }}</strong>
                    of <strong style="color:#0f172a;">{{ $recentTransactions->total() }}</strong> transactions
                </div>
                <div class="rp-pagination">
                    @if($recentTransactions->onFirstPage())
                        <span class="rp-page-btn disabled"><i class="fas fa-chevron-left" style="font-size:.65rem;"></i></span>
                    @else
                        <a href="{{ $recentTransactions->previousPageUrl() }}" class="rp-page-btn"><i class="fas fa-chevron-left" style="font-size:.65rem;"></i></a>
                    @endif
                    @foreach($recentTransactions->getUrlRange(1, $recentTransactions->lastPage()) as $page => $url)
                        <a href="{{ $url }}" class="rp-page-btn {{ $recentTransactions->currentPage() === $page ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($recentTransactions->hasMorePages())
                        <a href="{{ $recentTransactions->nextPageUrl() }}" class="rp-page-btn"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i></a>
                    @else
                        <span class="rp-page-btn disabled"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i></span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

</div>{{-- /container-fluid --}}

{{-- ════════════════════════════════════════════════════════
     Per-Row Transaction View Modals
     ════════════════════════════════════════════════════════ --}}
@foreach($recentTransactions as $order)
@php
    $mps = $order->sale?->payment_status;
    $mfs = $order->sale?->fulfillment_status;
    $mPayStyle = match($mps) {
        'paid'     => 'background:#d1fae5;color:#065f46;',
        'failed'   => 'background:#fee2e2;color:#991b1b;',
        'refunded' => 'background:#ede9fe;color:#4c1d95;',
        default    => 'background:#fef3c7;color:#92400e;',
    };
    $mFulStyle = match($mfs) {
        'delivered'  => 'background:#d1fae5;color:#065f46;',
        'shipped'    => 'background:#cffafe;color:#164e63;',
        'processing' => 'background:#dbeafe;color:#1e40af;',
        'cancelled'  => 'background:#fee2e2;color:#991b1b;',
        default      => 'background:#f1f5f9;color:#475569;',
    };
@endphp

<div id="viewTxOverlay-{{ $order->id }}" class="modal-overlay"
     onclick="if(event.target===this)closeModal('viewTxOverlay-{{ $order->id }}')">
    <div class="modal-box">

        {{-- Header --}}
        <div class="modal-hdr">
            <div>
                <div class="modal-hdr-title"><i class="fas fa-receipt me-2"></i>{{ $order->order_number }}</div>
                <div class="modal-hdr-sub">{{ $order->created_at->format('M d, Y · h:i A') }} · {{ $order->items->count() }} item(s)</div>
            </div>
            <button class="modal-close-btn" onclick="closeModal('viewTxOverlay-{{ $order->id }}')">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="modal-body">

            {{-- Customer + Status Strip --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 p-3 rounded-3" style="background:#f8fafd;border:1.5px solid #f1f5f9;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#1e3a8a);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr($order->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:700;color:#0f172a;font-size:.95rem;">{{ $order->user?->name ?? '—' }}</div>
                        <div style="font-size:.78rem;color:#64748b;">{{ $order->user?->masked_email ?? '—' }}</div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if($order->sale)
                        <span class="status-pill" style="{{ $mPayStyle }}"><i class="fas fa-circle" style="font-size:.45rem;"></i>{{ ucfirst($mps) }}</span>
                        <span class="status-pill" style="{{ $mFulStyle }}"><i class="fas fa-circle" style="font-size:.45rem;"></i>{{ ucfirst($mfs) }}</span>
                    @else
                        <span class="status-pill" style="background:#f1f5f9;color:#94a3b8;">No sale record</span>
                    @endif
                </div>
            </div>

            {{-- Order Info Grid --}}
            <div class="modal-section-label"><i class="fas fa-info-circle me-1"></i>Order Details</div>
            <div class="modal-info-grid mb-4">
                <div>
                    <div class="modal-info-key">Order Number</div>
                    <div class="modal-info-value">{{ $order->order_number }}</div>
                </div>
                <div>
                    <div class="modal-info-key">Date Placed</div>
                    <div class="modal-info-value">{{ $order->created_at->format('M d, Y') }}</div>
                </div>
                <div>
                    <div class="modal-info-key">Payment Method</div>
                    <div class="modal-info-value">{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? '—')) }}</div>
                </div>
                <div>
                    <div class="modal-info-key">Order Total</div>
                    <div class="modal-info-value" style="color:#2563eb;">₱{{ number_format($order->total, 2) }}</div>
                </div>
            </div>

            <hr class="modal-divider">

            {{-- Items Table --}}
            <div class="modal-section-label"><i class="fas fa-box me-1"></i>Items Ordered</div>
            <div class="table-responsive mb-4">
                <table class="table modal-items-table mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->items as $item)
                        <tr>
                            <td style="font-weight:600;color:#0f172a;">{{ $item->product?->name ?? 'Deleted Product' }}</td>
                            <td class="text-center">
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;background:#dbeafe;color:#1e40af;border-radius:6px;font-size:.78rem;font-weight:700;">{{ $item->quantity }}</span>
                            </td>
                            <td class="text-end" style="color:#64748b;">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end fw-semibold" style="color:#0f172a;">₱{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-3" style="color:#94a3b8;">No items found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end" style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;padding-top:.85rem;">Subtotal</td>
                            <td class="text-end" style="color:#374151;font-weight:600;padding-top:.85rem;">₱{{ number_format($order->subtotal ?? $order->total, 2) }}</td>
                        </tr>
                        @if(isset($order->tax) && $order->tax > 0)
                        <tr>
                            <td colspan="3" class="text-end" style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Tax</td>
                            <td class="text-end" style="color:#374151;font-weight:600;">₱{{ number_format($order->tax, 2) }}</td>
                        </tr>
                        @endif
                        @if(isset($order->shipping) && $order->shipping > 0)
                        <tr>
                            <td colspan="3" class="text-end" style="font-size:.78rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Shipping</td>
                            <td class="text-end" style="color:#374151;font-weight:600;">₱{{ number_format($order->shipping, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="modal-total-row">
                            <td colspan="3" class="text-end" style="font-size:.82rem;">Order Total</td>
                            <td class="text-end" style="font-size:1rem;color:#2563eb;">₱{{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Notes --}}
            @if($order->notes)
            <hr class="modal-divider">
            <div class="modal-section-label"><i class="fas fa-sticky-note me-1"></i>Notes</div>
            <div class="p-3 rounded-3" style="background:#fffbeb;border:1.5px solid #fde68a;color:#92400e;font-size:.855rem;">
                {{ $order->notes }}
            </div>
            @endif

        </div>{{-- /modal-body --}}

        {{-- Footer --}}
        <div class="modal-footer">
            <a href="{{ route('sales.orders.show', $order) }}" class="btn-full-detail">
                <i class="fas fa-external-link-alt"></i> Full Order Detail
            </a>
        </div>

    </div>{{-- /modal-box --}}
</div>{{-- /modal-overlay --}}
@endforeach

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
Chart.defaults.font.family = "'Nunito', sans-serif";
Chart.defaults.color = '#6c757d';

// ── Palette ──
const palette = {
    blue:   '#0d6efd',
    green:  '#198754',
    cyan:   '#0dcaf0',
    yellow: '#ffc107',
    red:    '#dc3545',
    purple: '#6f42c1',
    indigo: '#4361ee',
    orange: '#fd7e14',
    teal:   '#20c997',
    pink:   '#d63384',
};

const statusColors = {
    pending:    '#ffc107',
    processing: '#0dcaf0',
    shipped:    '#0d6efd',
    delivered:  '#198754',
    cancelled:  '#dc3545',
};

const paymentColors = {
    paid:     '#198754',
    pending:  '#ffc107',
    failed:   '#dc3545',
    refunded: '#6f42c1',
};

// ── 1. Monthly Revenue Trend ──
const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart');
if (monthlyRevenueCtx) {
    new Chart(monthlyRevenueCtx, {
        type: 'line',
        data: {
            labels: @json($monthlyLabels),
            datasets: [
                {
                    label: 'Revenue (₱)',
                    data: @json($monthlyData),
                    borderColor: palette.blue,
                    backgroundColor: 'rgba(13,110,253,0.08)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: palette.blue,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y',
                },
                {
                    label: 'Orders',
                    data: @json($monthlyOrders),
                    borderColor: palette.green,
                    backgroundColor: 'rgba(25,135,84,0.06)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: palette.green,
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1',
                    borderDash: [4, 4],
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            if (ctx.datasetIndex === 0) return ' ₱' + ctx.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2});
                            return ' ' + ctx.parsed.y + ' orders';
                        }
                    }
                }
            },
            scales: {
                y:  { type: 'linear', display: true, position: 'left',  ticks: { callback: v => '₱' + (v/1000).toFixed(0) + 'k' }, grid: { drawBorder: false } },
                y1: { type: 'linear', display: true, position: 'right', grid: { drawOnChartArea: false } },
                x:  { grid: { display: false } }
            }
        }
    });
}

// ── 2. Fulfillment Status Donut ──
const statusCtx = document.getElementById('statusChart');
if (statusCtx) {
    const statusData = @json($statusBreakdown);
    const statusKeys = Object.keys(statusData);
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusKeys.map(k => k.charAt(0).toUpperCase() + k.slice(1)),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: statusKeys.map(k => statusColors[k] || palette.blue),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 14 } },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' orders' } }
            }
        }
    });
}

// ── 3. Daily Orders (30d) Bar ──
const dailyCtx = document.getElementById('dailyOrdersChart');
if (dailyCtx) {
    new Chart(dailyCtx, {
        type: 'bar',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: 'Orders',
                data: @json($dailyCounts),
                backgroundColor: 'rgba(13,202,240,0.7)',
                borderColor: palette.cyan,
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.parsed.y + ' orders' } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { drawBorder: false } },
                x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 0, maxTicksLimit: 15 } }
            }
        }
    });
}

// ── 4. Payment Method Donut ──
const pmCtx = document.getElementById('paymentMethodChart');
if (pmCtx) {
    const pmData = @json($paymentMethods);
    const pmColors = { cod: palette.green, credit_card: palette.blue, paypal: palette.indigo };
    const pmKeys = Object.keys(pmData);
    new Chart(pmCtx, {
        type: 'doughnut',
        data: {
            labels: pmKeys.map(k => {
                const labels = { cod: 'Cash on Delivery', credit_card: 'Credit Card', paypal: 'PayPal' };
                return labels[k] || k;
            }),
            datasets: [{
                data: Object.values(pmData),
                backgroundColor: pmKeys.map(k => pmColors[k] || palette.purple),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 14 } },
            }
        }
    });
}

// ── 5. Top Products Horizontal Bar ──
const tpCtx = document.getElementById('topProductsChart');
if (tpCtx) {
    const tpData = @json($topProducts->map(fn($p) => ['name' => $p->name, 'qty' => $p->total_qty, 'rev' => $p->total_revenue]));
    new Chart(tpCtx, {
        type: 'bar',
        data: {
            labels: tpData.map(d => d.name.length > 22 ? d.name.slice(0, 22) + '…' : d.name),
            datasets: [{
                label: 'Units Sold',
                data: tpData.map(d => d.qty),
                backgroundColor: [
                    palette.blue, palette.green, palette.cyan, palette.yellow, palette.red,
                    palette.purple, palette.orange, palette.teal, palette.pink, palette.indigo,
                ],
                borderWidth: 0,
                borderRadius: 5,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const d = tpData[ctx.dataIndex];
                            return [' Units: ' + d.qty, ' Revenue: $' + parseFloat(d.rev).toFixed(2)];
                        }
                    }
                }
            },
            scales: {
                x: { beginAtZero: true, grid: { drawBorder: false } },
                y: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
}

// ── 6. Category Revenue Bar ──
const catCtx = document.getElementById('categoryChart');
if (catCtx) {
    const catData = @json($categoryRevenue->map(fn($c) => ['name' => $c->name, 'rev' => $c->revenue]));
    new Chart(catCtx, {
        type: 'bar',
        data: {
            labels: catData.map(d => d.name),
            datasets: [{
                label: 'Revenue (₱)',
                data: catData.map(d => parseFloat(d.rev)),
                backgroundColor: [
                    palette.purple, palette.blue, palette.green, palette.orange,
                    palette.teal, palette.pink, palette.cyan, palette.indigo,
                ],
                borderWidth: 0,
                borderRadius: 5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' ₱' + ctx.parsed.y.toLocaleString() } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => '₱' + v.toLocaleString() }, grid: { drawBorder: false } },
                x: { grid: { display: false } }
            }
        }
    });
}

// ── 7. Customer Growth Line ──
const cgCtx = document.getElementById('customerGrowthChart');
if (cgCtx) {
    new Chart(cgCtx, {
        type: 'line',
        data: {
            labels: @json($customerLabels),
            datasets: [{
                label: 'New Customers',
                data: @json($customerCounts),
                borderColor: palette.purple,
                backgroundColor: 'rgba(111,66,193,0.08)',
                borderWidth: 2.5,
                pointRadius: 5,
                pointBackgroundColor: palette.purple,
                fill: true,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ' ' + ctx.parsed.y + ' new customers' } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { drawBorder: false } },
                x: { grid: { display: false } }
            }
        }
    });
}

// ── 8. Payment Status Donut ──
const psCtx = document.getElementById('paymentStatusChart');
if (psCtx) {
    const psData = @json($paymentBreakdown);
    const psKeys = Object.keys(psData);
    new Chart(psCtx, {
        type: 'doughnut',
        data: {
            labels: psKeys.map(k => k.charAt(0).toUpperCase() + k.slice(1)),
            datasets: [{
                data: Object.values(psData),
                backgroundColor: psKeys.map(k => paymentColors[k] || palette.blue),
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 12 } },
            }
        }
    });
}

// ── Modal helpers ──
function openModal(id)  { document.getElementById(id).classList.add('active');    document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.remove('active'); document.body.style.overflow = '';       }
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active')
                .forEach(el => { el.classList.remove('active'); document.body.style.overflow = ''; });
    }
});
</script>
@endpush
