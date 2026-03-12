<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt — {{ $order->order_number }} | TrackNet Sales</title>
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700,800" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            background: #f4f6f9;
            color: #1e293b;
            padding: 2rem 1rem;
        }

        .receipt-wrapper {
            max-width: 720px;
            margin: 0 auto;
        }

        /* Print button — hidden on print */
        .no-print {
            text-align: center;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 1.4rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }
        .btn-print   { background: #2563eb; color: #fff; }
        .btn-back    { background: #fff; color: #64748b; border: 1.5px solid #e2e8f0; }
        .btn-primary { background: #0d6efd; color: #fff; }
        .btn-print:hover  { background: #1d4ed8; }
        .btn-back:hover   { background: #f8fafc; }

        /* Receipt card */
        .receipt {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            overflow: hidden;
        }

        /* Header */
        .receipt-header {
            background: #212529;
            color: #fff;
            padding: 2rem 2.5rem 1.5rem;
        }

        .brand { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px; }
        .brand-sub { font-size: 0.72rem; opacity: 0.55; text-transform: uppercase; letter-spacing: 0.1em; }

        .header-right {
            float: right;
            text-align: right;
        }

        .order-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            opacity: 0.5;
            margin-bottom: 0.15rem;
        }

        .order-number {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.3px;
        }

        .receipt-meta {
            display: flex;
            gap: 2rem;
            margin-top: 1.25rem;
            clear: both;
            flex-wrap: wrap;
        }

        .receipt-meta-item label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            opacity: 0.5;
            display: block;
            margin-bottom: 0.15rem;
        }

        .receipt-meta-item span { font-size: 0.85rem; font-weight: 600; }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 0.2rem 0.65rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-paid       { background: #d1fae5; color: #065f46; }
        .badge-pending    { background: #fef9c3; color: #713f12; }
        .badge-refunded   { background: #fee2e2; color: #991b1b; }
        .badge-failed     { background: #fee2e2; color: #991b1b; }
        .badge-delivered  { background: #d1fae5; color: #065f46; }
        .badge-shipped    { background: #dbeafe; color: #1e40af; }
        .badge-processing { background: #fef9c3; color: #713f12; }
        .badge-cancelled  { background: #fee2e2; color: #991b1b; }

        /* Body */
        .receipt-body { padding: 2rem 2.5rem; }

        .section-title {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Info grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .info-block strong {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 0.25rem;
        }

        .info-block p {
            font-size: 0.875rem;
            color: #1e293b;
            line-height: 1.5;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            font-size: 0.875rem;
        }

        .items-table thead th {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #94a3b8;
            padding: 0.6rem 0.5rem;
            border-bottom: 1.5px solid #e2e8f0;
            text-align: left;
            background: #f8fafc;
        }

        .items-table thead th:last-child,
        .items-table thead th:nth-child(2),
        .items-table thead th:nth-child(3) { text-align: right; }

        .items-table tbody td {
            padding: 0.85rem 0.5rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        .items-table tbody tr:last-child td { border-bottom: none; }
        .items-table .product-name { font-weight: 600; color: #0f172a; }
        .items-table .product-cat  { font-size: 0.75rem; color: #94a3b8; }

        .items-table td:nth-child(2),
        .items-table td:nth-child(3),
        .items-table td:last-child { text-align: right; }
        .items-table td:last-child { font-weight: 700; color: #0f172a; }

        /* Totals */
        .totals-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 2rem;
        }

        .totals-block {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            min-width: 280px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.3rem 0;
            font-size: 0.875rem;
            color: #64748b;
        }

        .total-row.grand {
            padding-top: 0.7rem;
            margin-top: 0.5rem;
            border-top: 2px solid #e2e8f0;
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
        }

        .total-row.grand .amount { color: #0d6efd; }

        /* Processed by */
        .processed-block {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            font-size: 0.82rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .processed-block strong { color: #1e293b; }

        /* Footer */
        .receipt-footer {
            text-align: center;
            padding: 1.25rem 2.5rem 2rem;
            border-top: 1px dashed #e2e8f0;
            color: #94a3b8;
            font-size: 0.75rem;
            line-height: 1.8;
        }

        .receipt-footer strong { color: #64748b; }

        /* ── Print styles ── */
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .receipt { box-shadow: none; border-radius: 0; }
            .receipt-wrapper { max-width: 100%; }
            .receipt-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>
    <div class="receipt-wrapper">

        {{-- Actions --}}
        <div class="no-print">
            <button class="btn btn-print" onclick="window.print()">
                🖨 Print Receipt
            </button>
            <a href="{{ route('sales.orders.receipt.pdf', $order) }}" class="btn btn-primary">
                ⬇ Download PDF
            </a>
            <a href="{{ route('sales.orders.show', $order) }}" class="btn btn-back">
                ← Back to Order
            </a>
        </div>

        <div class="receipt">

            {{-- Header --}}
            <div class="receipt-header">
                <div class="header-right">
                    <div class="order-label">Order Number</div>
                    <div class="order-number">#{{ $order->order_number }}</div>
                </div>
                <div class="brand">TrackNet</div>
                <div class="brand-sub">Sales Receipt — Internal Copy</div>

                <div class="receipt-meta">
                    <div class="receipt-meta-item">
                        <label>Date</label>
                        <span>{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="receipt-meta-item">
                        <label>Payment Method</label>
                        <span>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                    </div>
                    @if($order->sale)
                    <div class="receipt-meta-item">
                        <label>Payment Status</label>
                        <span><span class="badge badge-{{ $order->sale->payment_status }}">{{ ucfirst($order->sale->payment_status) }}</span></span>
                    </div>
                    <div class="receipt-meta-item">
                        <label>Fulfillment</label>
                        <span><span class="badge badge-{{ $order->sale->fulfillment_status }}">{{ ucfirst($order->sale->fulfillment_status) }}</span></span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Body --}}
            <div class="receipt-body">

                {{-- Customer & Addresses --}}
                <div class="section-title">Customer & Delivery Information</div>
                <div class="info-grid">
                    <div class="info-block">
                        <strong>Customer</strong>
                        <p>
                            {{ $order->user->name }}<br>
                            {{ $order->user->email }}
                        </p>
                    </div>
                    <div class="info-block">
                        <strong>Shipping Address</strong>
                        <p>{{ $order->shipping_address ?? 'N/A' }}</p>
                    </div>
                    <div class="info-block">
                        <strong>Billing Address</strong>
                        <p>{{ $order->billing_address ?? $order->shipping_address ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($order->notes)
                <div style="margin-bottom:2rem;">
                    <div class="section-title">Order Notes</div>
                    <p style="font-size:0.875rem;color:#475569;">{{ $order->notes }}</p>
                </div>
                @endif

                {{-- Items --}}
                <div class="section-title">Items ({{ $order->items->count() }})</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="product-name">{{ $item->product->name }}</div>
                                <div class="product-cat">
                                    {{ $item->product->category->name ?? '' }}
                                    @if($item->product->sku) · SKU: {{ $item->product->sku }} @endif
                                </div>
                            </td>
                            <td>₱{{ number_format($item->unit_price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>₱{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totals --}}
                <div class="totals-wrapper">
                    <div class="totals-block">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span>₱{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="total-row">
                            <span>Tax</span>
                            <span>₱{{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="total-row">
                            <span>Shipping</span>
                            <span>{{ $order->shipping > 0 ? '₱'.number_format($order->shipping, 2) : 'Free' }}</span>
                        </div>
                        <div class="total-row grand">
                            <span>Order Total</span>
                            <span class="amount">₱{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Processed by --}}
                @if($order->sale && $order->sale->user)
                <div class="processed-block">
                    <strong>Processed by:</strong> {{ $order->sale->user->name }} ({{ $order->sale->user->email }})
                    &nbsp;·&nbsp; <strong>Last updated:</strong> {{ $order->sale->updated_at->format('M d, Y g:i A') }}
                </div>
                @endif

            </div>

            {{-- Footer --}}
            <div class="receipt-footer">
                <strong>TrackNet Internal Document — Sales Copy</strong><br>
                Order #{{ $order->order_number }} · Generated on {{ now()->format('F d, Y \a\t g:i A') }}<br>
                This receipt is for internal record-keeping purposes.
            </div>

        </div>
    </div>
</body>
</html>
