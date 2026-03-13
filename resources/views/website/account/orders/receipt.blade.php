<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt — {{ $order->order_number }} | TrackNet</title>
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700,800" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            background: #f0f4f8;
            color: #1e293b;
            padding: 2rem 1rem;
        }

        .receipt-wrapper {
            max-width: 680px;
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

        .btn-print  { background: #2563eb; color: #fff; }
        .btn-back   { background: #fff; color: #64748b; border: 1.5px solid #e2e8f0; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-back:hover  { background: #f8fafc; }

        /* Receipt card */
        .receipt {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            overflow: hidden;
        }

        /* Header */
        .receipt-header {
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            color: #fff;
            padding: 2rem 2.5rem 1.5rem;
        }

        .brand {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }

        .brand-sub {
            font-size: 0.78rem;
            opacity: 0.6;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .receipt-title {
            margin-top: 1.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            opacity: 0.6;
        }

        .order-number {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-top: 0.2rem;
        }

        .receipt-meta {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .receipt-meta-item label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            opacity: 0.55;
            display: block;
            margin-bottom: 0.15rem;
        }

        .receipt-meta-item span {
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
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
        .receipt-body {
            padding: 2rem 2.5rem;
        }

        /* Section titles */
        .section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Addresses */
        .addr-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .addr-block p {
            font-size: 0.875rem;
            line-height: 1.6;
            color: #475569;
        }

        .addr-block strong {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.2rem;
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
            padding: 0.6rem 0;
            border-bottom: 1.5px solid #e2e8f0;
            text-align: left;
        }

        .items-table thead th:last-child { text-align: right; }

        .items-table tbody td {
            padding: 0.85rem 0;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #334155;
        }

        .items-table tbody tr:last-child td { border-bottom: none; }

        .items-table .product-name { font-weight: 600; color: #0f172a; }
        .items-table .product-sku  { font-size: 0.75rem; color: #94a3b8; margin-top: 0.1rem; }

        .items-table td:last-child {
            text-align: right;
            font-weight: 700;
            color: #0f172a;
        }

        /* Totals */
        .totals-block {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.35rem 0;
            font-size: 0.875rem;
            color: #64748b;
        }

        .total-row.grand {
            padding-top: 0.75rem;
            margin-top: 0.5rem;
            border-top: 2px solid #e2e8f0;
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .total-row.grand .amount { color: #2563eb; }

        /* Footer */
        .receipt-footer {
            text-align: center;
            padding: 1.5rem 2.5rem 2rem;
            border-top: 1px dashed #e2e8f0;
            color: #94a3b8;
            font-size: 0.78rem;
            line-height: 1.7;
        }

        .receipt-footer strong { color: #64748b; }

        /* ── Print styles ── */
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .receipt { box-shadow: none; border-radius: 0; }
            .receipt-wrapper { max-width: 100%; }
            .receipt-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { margin: 1cm; }
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
            <a href="{{ route('account.orders.receipt.pdf', $order) }}" class="btn btn-print" style="background:#198754;">
                ⬇ Download PDF
            </a>
            <a href="{{ route('account.orders.show', $order) }}" class="btn btn-back">
                ← Back to Order
            </a>
        </div>

        <div class="receipt">

            {{-- Header --}}
            <div class="receipt-header">
                <div class="brand">TrackNet</div>
                <div class="brand-sub">Official Order Receipt</div>

                <div class="receipt-title">Order Number</div>
                <div class="order-number">#{{ $order->order_number }}</div>

                <div class="receipt-meta">
                    <div class="receipt-meta-item">
                        <label>Date</label>
                        <span>{{ $order->created_at->format('F d, Y') }}</span>
                    </div>
                    <div class="receipt-meta-item">
                        <label>Time</label>
                        <span>{{ $order->created_at->format('g:i A') }}</span>
                    </div>
                    <div class="receipt-meta-item">
                        <label>Payment</label>
                        <span>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                    </div>
                    @if($order->sale)
                    <div class="receipt-meta-item">
                        <label>Payment Status</label>
                        <span>
                            <span class="badge badge-{{ $order->sale->payment_status }}">
                                {{ ucfirst($order->sale->payment_status) }}
                            </span>
                        </span>
                    </div>
                    <div class="receipt-meta-item">
                        <label>Fulfillment</label>
                        <span>
                            <span class="badge badge-{{ $order->sale->fulfillment_status }}">
                                {{ ucfirst($order->sale->fulfillment_status) }}
                            </span>
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Body --}}
            <div class="receipt-body">

                {{-- Customer & Address --}}
                <div class="section-title">Delivery Information</div>
                <div class="addr-grid">
                    <div class="addr-block">
                        <strong>Customer</strong>
                        <p>
                            {{ $order->user->name }}<br>
                            {{ $order->user->masked_email }}
                        </p>
                    </div>
                    <div class="addr-block">
                        <strong>Shipping Address</strong>
                        <p>{{ $order->shipping_address }}</p>
                    </div>
                    @if($order->billing_address && $order->billing_address !== $order->shipping_address)
                    <div class="addr-block">
                        <strong>Billing Address</strong>
                        <p>{{ $order->billing_address }}</p>
                    </div>
                    @endif
                    @if($order->notes)
                    <div class="addr-block">
                        <strong>Order Notes</strong>
                        <p>{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>

                {{-- Items --}}
                <div class="section-title">Items Ordered ({{ $order->items->count() }})</div>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="text-align:center;">Qty</th>
                            <th style="text-align:right;">Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="product-name">{{ $item->product->name }}</div>
                                @if($item->product->sku)
                                <div class="product-sku">SKU: {{ $item->product->sku }}</div>
                                @endif
                            </td>
                            <td style="text-align:center;">{{ $item->quantity }}</td>
                            <td style="text-align:right;">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td>₱{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totals --}}
                <div class="section-title">Order Summary</div>
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
                        <span>Total Paid</span>
                        <span class="amount">₱{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="receipt-footer">
                <strong>Thank you for shopping with TrackNet!</strong><br>
                This is your official receipt. Please keep it for your records.<br>
                For support, contact us at support@tracknet.com<br>
                <small>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</small>
            </div>

        </div>
    </div>
</body>
</html>
