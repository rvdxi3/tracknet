<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sales Report | TrackNet</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, Helvetica, sans-serif; font-size: 10px; color: #1e293b; background: #fff; }

        /* ── Page Header ── */
        .page-header  { background: #0f172a; }
        .header-main  { padding: 18px 28px 14px; }
        .header-accent { background: #1e3a8a; padding: 6px 28px; }

        .brand-name    { font-size: 20px; font-weight: bold; color: #ffffff; letter-spacing: 0.5px; }
        .brand-tagline { font-size: 7.5px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1.5px; margin-top: 2px; }

        .header-right  { float: right; text-align: right; }
        .report-label  { font-size: 7.5px; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 1px; }
        .report-period { font-size: 12px; font-weight: bold; color: #ffffff; margin-top: 2px; }
        .clearfix      { clear: both; }
        .header-meta   { font-size: 8px; color: rgba(255,255,255,0.5); }

        /* ── Summary Bar ── */
        .summary-bar { background: #1e293b; padding: 11px 16px; margin: 14px 0 12px; border-radius: 5px; }
        .sum-row { width: 100%; border-collapse: collapse; }
        .sum-row td {
            text-align: center; padding: 0 8px;
            border-right: 1px solid rgba(255,255,255,0.1);
        }
        .sum-row td:first-child { text-align: left;  padding-left: 0; }
        .sum-row td:last-child  { text-align: right; padding-right: 0; border-right: none; }
        .sum-label { font-size: 7px; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
        .sum-value          { font-size: 13px; font-weight: bold; color: #fff; line-height: 1; }
        .sum-value.blue     { color: #93c5fd; }
        .sum-value.green    { color: #86efac; }
        .sum-value.amber    { color: #fcd34d; }
        .sum-value.red      { color: #fca5a5; }

        /* ── Body ── */
        .body { padding: 0 28px 16px; }

        /* ── Section Label ── */
        .section-label {
            display: inline-block;
            font-size: 7.5px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 1px; color: #ffffff;
            background: #1e3a8a; padding: 3px 10px;
            border-radius: 3px; margin-bottom: 7px; margin-top: 2px;
        }

        /* ── Data Tables ── */
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 9.5px; border: 1px solid #e2e8f0; }
        table.data thead { background: #1e3a8a; }
        table.data th {
            font-size: 7.5px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.6px; color: #ffffff;
            padding: 7px 8px; text-align: left; border: none;
        }
        table.data th.num { text-align: right; }
        table.data tbody tr:nth-child(even) { background: #f8fafc; }
        table.data tbody tr:nth-child(odd)  { background: #ffffff; }
        table.data td {
            padding: 6px 8px; border-bottom: 1px solid #f1f5f9; color: #334155;
        }
        table.data tr:last-child td { border-bottom: none; }
        table.data .num   { text-align: right; }
        table.data .bold  { font-weight: bold; color: #0f172a; }
        table.data .money { font-weight: bold; color: #1e40af; }

        /* ── Rank Badge ── */
        .rank { display: inline-block; width: 18px; height: 18px; border-radius: 50%; font-size: 8px; font-weight: bold; text-align: center; line-height: 18px; background: #dbeafe; color: #1e40af; }
        .rank.gold   { background: #fef3c7; color: #92400e; }
        .rank.silver { background: #f1f5f9; color: #475569; }
        .rank.bronze { background: #fef3c7; color: #b45309; }

        /* ── Status Badges ── */
        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; }
        .badge-paid       { background: #d1fae5; color: #065f46; }
        .badge-pending    { background: #fef3c7; color: #92400e; }
        .badge-refunded   { background: #ede9fe; color: #4c1d95; }
        .badge-failed     { background: #fee2e2; color: #991b1b; }
        .badge-delivered  { background: #d1fae5; color: #065f46; }
        .badge-shipped    { background: #dbeafe; color: #1e40af; }
        .badge-processing { background: #cffafe; color: #164e63; }
        .badge-cancelled  { background: #fee2e2; color: #991b1b; }

        /* ── Two-column layout ── */
        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .two-col > tbody > tr > td { vertical-align: top; width: 50%; padding-right: 9px; }
        .two-col > tbody > tr > td:last-child { padding-right: 0; padding-left: 9px; }

        /* ── Divider ── */
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 12px 0; }

        /* ── Footer ── */
        .footer {
            text-align: center; padding: 9px 28px 14px;
            border-top: 2px solid #e2e8f0; color: #94a3b8;
            font-size: 8px; line-height: 1.8; margin-top: 8px;
        }
        .footer-brand { font-size: 9.5px; font-weight: bold; color: #1e3a8a; }
    </style>
</head>
<body>

    {{-- ── Header ── --}}
    <div class="page-header">
        <div class="header-main">
            <div class="header-right">
                <div class="report-label">Report Period</div>
                <div class="report-period">{{ $startDate->format('M d, Y') }} &ndash; {{ $endDate->format('M d, Y') }}</div>
            </div>
            <div>
                <div class="brand-name">TrackNet</div>
                <div class="brand-tagline">Sales Report &amp; Analytics</div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="header-accent">
            <div class="header-meta">Generated on {{ now()->format('F d, Y \a\t g:i A') }}</div>
        </div>
    </div>

    <div class="body">

        {{-- ── Summary Bar ── --}}
        <div class="summary-bar">
            <table class="sum-row">
                <tr>
                    <td>
                        <div class="sum-label">Total Revenue</div>
                        <div class="sum-value blue">₱{{ number_format($totalRevenue, 2) }}</div>
                    </td>
                    <td>
                        <div class="sum-label">Total Orders</div>
                        <div class="sum-value">{{ $totalOrders }}</div>
                    </td>
                    <td>
                        <div class="sum-label">Paid Orders</div>
                        <div class="sum-value green">{{ $paidOrders }}</div>
                    </td>
                    <td>
                        <div class="sum-label">Conversion Rate</div>
                        <div class="sum-value">{{ $totalOrders > 0 ? round($paidOrders / $totalOrders * 100) : 0 }}%</div>
                    </td>
                    <td>
                        <div class="sum-label">Avg Order Value</div>
                        <div class="sum-value">₱{{ number_format($avgOrderValue, 2) }}</div>
                    </td>
                    <td>
                        <div class="sum-label">New Customers</div>
                        <div class="sum-value amber">{{ $totalCustomers }}</div>
                    </td>
                    <td>
                        <div class="sum-label">Cancelled</div>
                        <div class="sum-value red">{{ $cancelledOrders }} <span style="font-size:9px;opacity:.7;">({{ $totalOrders > 0 ? round($cancelledOrders / $totalOrders * 100) : 0 }}%)</span></div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ── Top Products + Category Revenue ── --}}
        <table class="two-col">
            <tr>
                <td>
                    <div class="section-label">Top 10 Products by Units Sold</div>
                    <table class="data">
                        <thead>
                            <tr>
                                <th style="width:26px;">#</th>
                                <th>Product</th>
                                <th class="num" style="width:38px;">Units</th>
                                <th class="num" style="width:64px;">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $i => $product)
                            <tr>
                                <td>
                                    <span class="rank {{ $i === 0 ? 'gold' : ($i === 1 ? 'silver' : ($i === 2 ? 'bronze' : '')) }}">{{ $i + 1 }}</span>
                                </td>
                                <td class="bold">{{ $product->name }}</td>
                                <td class="num bold">{{ $product->total_qty }}</td>
                                <td class="num money">₱{{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:10px;">No data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="section-label">Revenue by Category</div>
                    <table class="data">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="num" style="width:38px;">Units</th>
                                <th class="num" style="width:70px;">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categoryRevenue as $cat)
                            <tr>
                                <td class="bold">{{ $cat->name }}</td>
                                <td class="num">{{ $cat->qty }}</td>
                                <td class="num money">₱{{ number_format($cat->revenue, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:10px;">No data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <hr class="divider">

        {{-- ── Recent Transactions ── --}}
        <div class="section-label">Recent Transactions (Last 20)</div>
        <table class="data" style="margin-top:7px;">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Payment Method</th>
                    <th class="num">Total</th>
                    <th>Payment Status</th>
                    <th>Fulfillment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransactions as $order)
                <tr>
                    <td class="bold">{{ $order->order_number }}</td>
                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                    <td style="white-space:nowrap;">{{ $order->created_at->format('M d, Y') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? '—')) }}</td>
                    <td class="num money">₱{{ number_format($order->total, 2) }}</td>
                    <td>
                        @if($order->sale)
                            <span class="badge badge-{{ $order->sale->payment_status }}">{{ ucfirst($order->sale->payment_status) }}</span>
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($order->sale)
                            <span class="badge badge-{{ $order->sale->fulfillment_status }}">{{ ucfirst($order->sale->fulfillment_status) }}</span>
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:#94a3b8;padding:12px;">No transactions found for this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    {{-- ── Footer ── --}}
    <div class="footer">
        <div class="footer-brand">TrackNet &mdash; Sales Report &amp; Analytics</div>
        Period: {{ $startDate->format('F d, Y') }} to {{ $endDate->format('F d, Y') }}
        &middot; Generated on {{ now()->format('F d, Y \a\t g:i A') }}
    </div>

</body>
</html>
