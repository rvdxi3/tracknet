<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }} | TrackNet</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; background: #fff; }

        .header { background: #0f172a; color: #fff; padding: 20px 28px 16px; }
        .brand { font-size: 20px; font-weight: bold; }
        .brand-sub { font-size: 9px; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
        .order-label { font-size: 9px; opacity: 0.55; text-transform: uppercase; letter-spacing: 1px; margin-top: 14px; }
        .order-num { font-size: 20px; font-weight: bold; margin-top: 2px; }
        .meta-row { margin-top: 10px; }
        .meta-item { display: inline-block; margin-right: 24px; }
        .meta-item .ml { font-size: 8px; opacity: 0.5; text-transform: uppercase; display: block; margin-bottom: 2px; }
        .meta-item .mv { font-size: 11px; font-weight: bold; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .badge-paid       { background: #d1fae5; color: #065f46; }
        .badge-pending    { background: #fef9c3; color: #713f12; }
        .badge-refunded   { background: #fee2e2; color: #991b1b; }
        .badge-failed     { background: #fee2e2; color: #991b1b; }
        .badge-delivered  { background: #d1fae5; color: #065f46; }
        .badge-shipped    { background: #dbeafe; color: #1e40af; }
        .badge-processing { background: #fef9c3; color: #713f12; }
        .badge-cancelled  { background: #fee2e2; color: #991b1b; }

        .body { padding: 20px 28px; }

        .section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid #f1f5f9; }

        .addr-table { width: 100%; margin-bottom: 18px; }
        .addr-table td { vertical-align: top; width: 50%; font-size: 11px; padding-right: 12px; }
        .addr-table td strong { display: block; font-size: 9px; color: #1e293b; font-weight: bold; margin-bottom: 3px; }
        .addr-table td p { color: #475569; line-height: 1.5; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 11px; }
        table.items th { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; padding: 6px 4px; border-bottom: 1.5px solid #e2e8f0; text-align: left; }
        table.items th:last-child { text-align: right; }
        table.items td { padding: 8px 4px; border-bottom: 1px solid #f1f5f9; color: #334155; vertical-align: middle; }
        table.items td:last-child { text-align: right; font-weight: bold; color: #0f172a; }
        table.items td:nth-child(2), table.items td:nth-child(3) { text-align: center; }
        table.items .pname { font-weight: bold; color: #0f172a; }
        table.items .psku  { font-size: 9px; color: #94a3b8; }

        .totals { background: #f8fafc; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .t-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 11px; color: #64748b; }
        .t-row.grand { border-top: 1.5px solid #e2e8f0; margin-top: 6px; padding-top: 6px; font-size: 13px; font-weight: bold; color: #0f172a; }
        .t-row.grand .amount { color: #2563eb; }

        .footer { text-align: center; padding: 12px 28px 16px; border-top: 1px dashed #e2e8f0; color: #94a3b8; font-size: 10px; line-height: 1.7; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">TrackNet</div>
        <div class="brand-sub">Official Invoice</div>
        <div class="order-label">Order Number</div>
        <div class="order-num">#{{ $order->order_number }}</div>
        <div class="meta-row">
            <div class="meta-item">
                <span class="ml">Date</span>
                <span class="mv">{{ $order->created_at->format('F d, Y') }}</span>
            </div>
            <div class="meta-item">
                <span class="ml">Time</span>
                <span class="mv">{{ $order->created_at->format('g:i A') }}</span>
            </div>
            <div class="meta-item">
                <span class="ml">Payment</span>
                <span class="mv">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
            </div>
            @if($order->sale)
            <div class="meta-item">
                <span class="ml">Status</span>
                <span class="mv"><span class="badge badge-{{ $order->sale->fulfillment_status }}">{{ ucfirst($order->sale->fulfillment_status) }}</span></span>
            </div>
            @endif
        </div>
    </div>

    <div class="body">
        <div class="section-title">Delivery Information</div>
        <table class="addr-table">
            <tr>
                <td>
                    <strong>Customer</strong>
                    <p>{{ $order->user->name }}<br>{{ $order->user->email }}</p>
                </td>
                <td>
                    <strong>Shipping Address</strong>
                    <p>{{ $order->shipping_address }}</p>
                </td>
            </tr>
        </table>

        <div class="section-title">Items Ordered ({{ $order->items->count() }})</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:center;">Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="pname">{{ $item->product->name }}</div>
                        @if($item->product->sku)<div class="psku">SKU: {{ $item->product->sku }}</div>@endif
                    </td>
                    <td style="text-align:center;">{{ $item->quantity }}</td>
                    <td style="text-align:center;">₱{{ number_format($item->unit_price, 2) }}</td>
                    <td>₱{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section-title">Order Summary</div>
        <div class="totals">
            <div class="t-row"><span>Subtotal</span><span>₱{{ number_format($order->subtotal, 2) }}</span></div>
            <div class="t-row"><span>Tax</span><span>₱{{ number_format($order->tax, 2) }}</span></div>
            <div class="t-row"><span>Shipping</span><span>{{ $order->shipping > 0 ? '₱'.number_format($order->shipping, 2) : 'Free' }}</span></div>
            <div class="t-row grand"><span>Total Paid</span><span class="amount">₱{{ number_format($order->total, 2) }}</span></div>
        </div>
    </div>

    <div class="footer">
        <strong>Thank you for shopping with TrackNet!</strong><br>
        This is your official invoice. Please keep it for your records.<br>
        For support, contact us at support@tracknet.com<br>
        Generated on {{ now()->format('F d, Y \a\t g:i A') }}
    </div>
</body>
</html>
