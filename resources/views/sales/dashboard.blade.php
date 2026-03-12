@extends('layouts.app')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Revenue Last 7 Days (Line) ────────────────────────────────────
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($weekLabels) !!},
            datasets: [{
                label: 'Revenue (₱)',
                data: {!! json_encode($weekRevenue) !!},
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.07)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ' ₱' + ctx.parsed.y.toLocaleString() } } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { callback: v => '₱' + v.toLocaleString(), font: { size: 11 } } },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });

    // ── Order Status Breakdown (Doughnut) ─────────────────────────────
    const statusData   = {!! json_encode($statusBreakdown) !!};
    const statusLabels = Object.keys(statusData).map(k => k.charAt(0).toUpperCase() + k.slice(1));
    const statusColors = { pending:'#f59e0b', processing:'#0ea5e9', shipped:'#2563eb', delivered:'#16a34a', cancelled:'#e11d48' };
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: statusLabels.map(l => statusColors[l.toLowerCase()] || '#94a3b8'),
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 14, font: { size: 12 }, usePointStyle: true, pointStyleWidth: 10 } } },
            cutout: '68%',
        }
    });
});
</script>
@endpush

@section('content')
<div class="container-fluid px-4">

    {{-- ── Page Header ────────────────────────────────────────────────── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%); margin-top: -1rem; position: relative; overflow: hidden;">
                {{-- Background glow --}}
                <div style="position:absolute; top:-40px; right:-40px; width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,0.04); pointer-events:none;"></div>
                <div style="position:absolute; bottom:-60px; right:120px; width:160px; height:160px; border-radius:50%; background:rgba(255,255,255,0.03); pointer-events:none;"></div>
                <div class="position-relative" style="z-index:1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2); color:rgba(255,255,255,0.9); padding:.35rem 1rem; border-radius:20px; font-size:.7rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase;">
                            <i class="fas fa-chart-line me-1"></i> Sales Panel
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">Sales Dashboard</h2>
                            <p class="text-white-50 mb-0" style="font-size:.9rem;">
                                <i class="fas fa-calendar-alt me-1"></i> Performance overview — {{ now()->format('F d, Y') }}
                            </p>
                        </div>
                        <a href="{{ route('sales.reports.index') }}" class="btn" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:white; padding:.5rem 1.2rem; border-radius:12px; font-weight:600; font-size:.85rem;">
                            <i class="fas fa-chart-bar me-2"></i>Full Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Total Revenue --}}
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="sd-stat-card">
                <div class="sd-stat-icon-wrap">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="sd-stat-label">Total Revenue</div>
                <div class="sd-stat-value">₱{{ number_format($totalRevenue, 0) }}</div>
                <div class="sd-stat-bar"></div>
            </div>
        </div>

        {{-- Monthly Sales --}}
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="sd-stat-card">
                <div class="sd-stat-icon-wrap">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="sd-stat-label">Monthly Sales</div>
                <div class="sd-stat-value">₱{{ number_format($monthlySales, 0) }}</div>
                <div class="sd-stat-bar"></div>
            </div>
        </div>

        {{-- Today's Sales --}}
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="sd-stat-card">
                <div class="sd-stat-icon-wrap">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="sd-stat-label">Today's Sales</div>
                <div class="sd-stat-value">₱{{ number_format($todaySales, 0) }}</div>
                <div class="sd-stat-bar"></div>
            </div>
        </div>

        {{-- Pending Orders --}}
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="sd-stat-card">
                <div class="sd-stat-icon-wrap">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="sd-stat-label">Pending Orders</div>
                <div class="sd-stat-value">{{ $pendingOrders }}</div>
                <div class="sd-stat-bar"></div>
            </div>
        </div>

        {{-- Total Orders --}}
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="sd-stat-card">
                <div class="sd-stat-icon-wrap">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="sd-stat-label">Total Orders</div>
                <div class="sd-stat-value">{{ $totalOrders }}</div>
                <div class="sd-stat-bar"></div>
            </div>
        </div>

        {{-- Customers --}}
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="sd-stat-card">
                <div class="sd-stat-icon-wrap">
                    <i class="fas fa-users"></i>
                </div>
                <div class="sd-stat-label">Customers</div>
                <div class="sd-stat-value">{{ $totalCustomers }}</div>
                <div class="sd-stat-bar"></div>
            </div>
        </div>

    </div>

    {{-- ── Charts ──────────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Revenue Line Chart --}}
        <div class="col-lg-8">
            <div class="card border-0 h-100" style="border-radius:20px; box-shadow:0 10px 40px -5px rgba(13,20,40,.1);">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #eef2f6;">
                    <div style="width:38px; height:38px; border-radius:11px; background:linear-gradient(135deg,#2563eb15,#1e3a8a15); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-chart-line" style="color:#2563eb; font-size:.9rem;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:.95rem;">Revenue — Last 7 Days</h6>
                        <small class="text-muted" style="font-size:.75rem;">Daily sales trend</small>
                    </div>
                </div>
                <div class="card-body p-4" style="position:relative; height:280px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Status Doughnut --}}
        <div class="col-lg-4">
            <div class="card border-0 h-100" style="border-radius:20px; box-shadow:0 10px 40px -5px rgba(13,20,40,.1);">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center gap-3" style="border-bottom:1px solid #eef2f6;">
                    <div style="width:38px; height:38px; border-radius:11px; background:linear-gradient(135deg,#8b5cf615,#7c3aed15); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-circle-half-stroke" style="color:#8b5cf6; font-size:.9rem;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:.95rem;">Order Status</h6>
                        <small class="text-muted" style="font-size:.75rem;">Breakdown by status</small>
                    </div>
                </div>
                <div class="card-body p-4" style="position:relative; height:280px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Top Products + Recent Orders ────────────────────────────────── --}}
    <div class="row g-3">

        {{-- Top 5 Products --}}
        <div class="col-lg-4">
            <div class="card border-0 h-100" style="border-radius:20px; box-shadow:0 10px 40px -5px rgba(13,20,40,.1); overflow:hidden;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between" style="border-bottom:1px solid #eef2f6;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:38px; height:38px; border-radius:11px; background:linear-gradient(135deg,#16a34a15,#14532d15); display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-trophy" style="color:#16a34a; font-size:.9rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:.95rem;">Top Products</h6>
                            <small class="text-muted" style="font-size:.75rem;">By units sold</small>
                        </div>
                    </div>
                    <span style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; padding:.25rem .75rem; border-radius:20px; font-size:.7rem; font-weight:700;">Top 5</span>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0" style="font-size:.87rem;">
                        <thead>
                            <tr style="background:#f8fafd;">
                                <th class="px-4 py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">#</th>
                                <th class="py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Product</th>
                                <th class="text-end px-4 py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Units</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $i => $product)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td class="px-4 py-3" style="width:48px;">
                                    @if($i === 0)
                                        <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; font-size:.72rem; font-weight:800;">1</span>
                                    @elseif($i === 1)
                                        <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:linear-gradient(135deg,#94a3b8,#64748b); color:#fff; font-size:.72rem; font-weight:800;">2</span>
                                    @elseif($i === 2)
                                        <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:linear-gradient(135deg,#f97316,#ea580c); color:#fff; font-size:.72rem; font-weight:800;">3</span>
                                    @else
                                        <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:8px; background:#f1f5f9; color:#64748b; font-size:.72rem; font-weight:700;">{{ $i+1 }}</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="fw-semibold" style="color:#0f172a; font-size:.875rem;">{{ $product->name }}</div>
                                    <div style="font-size:.75rem; color:#64748b;">₱{{ number_format($product->total_revenue, 2) }} revenue</div>
                                </td>
                                <td class="text-end px-4 py-3">
                                    <span style="font-weight:800; color:#0f172a; font-size:.9rem;">{{ $product->total_qty }}</span>
                                    <span style="font-size:.7rem; color:#94a3b8; display:block;">units</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5" style="color:#94a3b8;">
                                    <i class="fas fa-box-open mb-2" style="font-size:1.5rem; display:block; opacity:.4;"></i>
                                    No sales data yet
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="col-lg-8">
            <div class="card border-0 h-100" style="border-radius:20px; box-shadow:0 10px 40px -5px rgba(13,20,40,.1); overflow:hidden;">
                <div class="card-header bg-transparent px-4 py-3 d-flex align-items-center justify-content-between" style="border-bottom:1px solid #eef2f6;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:38px; height:38px; border-radius:11px; background:linear-gradient(135deg,#2563eb15,#1e3a8a15); display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-receipt" style="color:#2563eb; font-size:.9rem;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:.95rem;">Recent Orders</h6>
                            <small class="text-muted" style="font-size:.75rem;">Latest transactions</small>
                        </div>
                    </div>
                    <a href="{{ route('sales.orders.index') }}" class="btn btn-sm" style="background:linear-gradient(135deg,#2563eb,#1e3a8a); color:#fff; border:none; border-radius:10px; font-size:.8rem; font-weight:600; padding:.4rem 1rem;">
                        <i class="fas fa-arrow-right me-1"></i>View All
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0" style="font-size:.87rem;">
                            <thead>
                                <tr style="background:#f8fafd;">
                                    <th class="px-4 py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Order</th>
                                    <th class="py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Customer</th>
                                    <th class="py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Amount</th>
                                    <th class="py-3" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Status</th>
                                    <th class="py-3 col-hide-xs" style="color:#94a3b8; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; border:none;">Date</th>
                                    <th class="px-4 py-3" style="border:none;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr style="border-bottom:1px solid #f1f5f9; transition:background .15s;" onmouseover="this.style.background='#f8fafd'" onmouseout="this.style.background=''">
                                    <td class="px-4 py-3">
                                        <span style="font-weight:700; color:#0f172a; font-size:.85rem;">{{ $order->order_number }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,#2563eb,#1e3a8a); color:#fff; font-weight:800; font-size:.75rem; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                {{ strtoupper(substr($order->user->name, 0, 1)) }}
                                            </div>
                                            <span style="font-weight:600; color:#0f172a;">{{ $order->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span style="font-weight:700; color:#0f172a;">₱{{ number_format($order->total, 2) }}</span>
                                    </td>
                                    <td class="py-3">
                                        @if($order->sale)
                                            @php
                                                $fs = $order->sale->fulfillment_status;
                                                $statusStyle = match($fs) {
                                                    'delivered'  => 'background:#dcfce7; color:#15803d; border:1px solid #bbf7d0;',
                                                    'shipped'    => 'background:#dbeafe; color:#1d4ed8; border:1px solid #bfdbfe;',
                                                    'processing' => 'background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;',
                                                    'cancelled'  => 'background:#fee2e2; color:#b91c1c; border:1px solid #fecaca;',
                                                    default      => 'background:#fef9c3; color:#a16207; border:1px solid #fef08a;',
                                                };
                                            @endphp
                                            <span style="padding:.25rem .65rem; border-radius:20px; font-size:.72rem; font-weight:700; {{ $statusStyle }}">
                                                {{ ucfirst($fs) }}
                                            </span>
                                        @else
                                            <span style="padding:.25rem .65rem; border-radius:20px; font-size:.72rem; font-weight:700; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 col-hide-xs" style="color:#64748b; font-size:.82rem;">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('sales.orders.show', $order) }}"
                                           style="width:32px; height:32px; border-radius:8px; background:#f1f5f9; border:1.5px solid #e2e8f0; color:#475569; display:inline-flex; align-items:center; justify-content:center; font-size:.78rem; text-decoration:none; transition:background .15s, color .15s;"
                                           onmouseover="this.style.background='#dbeafe'; this.style.color='#1d4ed8'; this.style.borderColor='#bfdbfe';"
                                           onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569'; this.style.borderColor='#e2e8f0';"
                                           title="View Order">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
.container-fluid { background: #f8fafd; min-height: 100vh; }

/* ── Stat Cards ── */
.sd-stat-card {
    background: #fff;
    border-radius: 18px;
    padding: 1.2rem 1.1rem 1rem;
    box-shadow: 0 4px 20px -4px rgba(13,20,40,.09);
    border: 1px solid #dbeafe;
    position: relative;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.sd-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px -6px rgba(37,99,235,.18);
    border-color: #bfdbfe;
}
.sd-stat-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: .75rem;
    box-shadow: 0 4px 14px rgba(37,99,235,.35);
    color: #fff;
    font-size: .95rem;
}
.sd-stat-label {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #0f172a;
    margin-bottom: .2rem;
}
.sd-stat-value {
    font-size: 1.55rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
    margin-bottom: .85rem;
}
.sd-stat-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 0 0 18px 18px;
    background: linear-gradient(90deg, #2563eb, #60a5fa);
    opacity: .8;
}

/* ── Card shared ── */
.card { transition: box-shadow .2s; }
</style>
@endsection
