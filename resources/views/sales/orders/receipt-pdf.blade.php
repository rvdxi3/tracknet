<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $order->order_number }} | TrackNet</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; background: #fff; }

        .header { background: #212529; color: #fff; padding: 20px 28px 16px; }
        .brand { font-size: 20px; font-weight: bold; }
        .brand-sub { font-size: 9px; opacity: 0.6; text-transform: uppercase; letter-spacing: 1px; margin-top: 2px; }
        .order-num { float: right; text-align: right; }
        .order-num .label { font-size: 9px; opacity: 0.5; text-transform: uppercase; }
        .order-num .num { font-size: 18px; font-weight: bold; }
        .meta-row { margin-top: 14px; clear: both; }
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

        .info-table { width: 100%; margin-bottom: 18px; }
        .info-table td { vertical-align: top; width: 33.3%; font-size: 11px; padding-right: 12px; }
        .info-table td strong { display: block; font-size: 9px; color: #475569; margin-bottom: 3px; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 11px; }
        table.items th { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; padding: 6px 4px; border-bottom: 1.5px solid #e2e8f0; text-align: left; background: #f8fafc; }
        table.items th:last-child, table.items th:nth-child(2), table.items th:nth-child(3) { text-align: right; }
        table.items td { padding: 8px 4px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        table.items td:nth-child(2), table.items td:nth-child(3), table.items td:last-child { text-align: right; }
        table.items td:last-child { font-weight: bold; }
        table.items .pname { font-weight: bold; color: #0f172a; }
        table.items .pcat  { font-size: 9px; color: #94a3b8; }

        .totals { float: right; width: 260px; background: #f8fafc; border-radius: 6px; padding: 12px 14px; margin-bottom: 18px; }
        .t-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 11px; color: #64748b; }
        .t-row.grand { border-top: 1.5px solid #e2e8f0; margin-top: 6px; padding-top: 6px; font-size: 13px; font-weight: bold; color: #0f172a; }
        .t-row.grand .amount { color: #0d6efd; }
        .clearfix::after { content: ''; display: table; clear: both; }

        .processed { background: #f8fafc; border-radius: 6px; padding: 8px 12px; font-size: 10px; color: #64748b; margin-bottom: 14px; clear: both; }

        .footer { text-align: center; padding: 12px 28px 16px; border-top: 1px dashed #e2e8f0; color: #94a3b8; font-size: 10px; line-height: 1.7; }
    </style>
</head>
<body>
    <div class="header">
        <div class="order-num">
            <div class="label">Order Number</div>
            <div class="num">#{{ $order->order_number }}</div>
        </div>
        <div class="brand">TrackNet</div>
        <div class="brand-sub">Sales Receipt — Internal Copy</div>
        <div class="meta-row">
            <div class="meta-item">
                <span class="ml">Date</span>
                <span class="mv">{{ $order->created_at->format('M d, Y') }}</span>
            </div>
            <div class="meta-item">
                <span class="ml">Payment Method</span>
                <span class="mv">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
            </div>
            @if($order->sale)
            <div class="meta-item">
                <span class="ml">Payment Status</span>
                <span class="mv"><span class="badge badge-{{ $order->sale->payment_status }}">{{ ucfirst($order->sale->payment_status) }}</span></span>
            </div>
            <div class="meta-item">
                <span class="ml">Fulfillment</span>
                <span class="mv"><span class="badge badge-{{ $order->sale->fulfillment_status }}">{{ ucfirst($order->sale->fulfillment_status) }}</span></span>
            </div>
            @endif
        </div>
    </div>

    <div class="body">
        <div class="section-title">Customer &amp; Delivery Information</div>
        <table class="info-table">
            <tr>
                <td><strong>Customer</strong>{{ $order->user->name }}<br>{{ $order->user->email }}</td>
                <td><strong>Shipping Address</strong>{{ $order->shipping_address ?? 'N/A' }}</td>
                <td><strong>Billing Address</strong>{{ $order->billing_address ?? $order->shipping_address ?? 'N/A' }}</td>
            </tr>
        </table>

        @if($order->notes)
        <div style="margin-bottom:14px;">
            <div class="section-title">Order Notes</div>
            <p style="font-size:11px;color:#475569;">{{ $order->notes }}</p>
        </div>
        @endif

        <div class="section-title">Items ({{ $order->items->count() }})</div>
        <table class="items">
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
                        <div class="pname">{{ $item->product->name }}</div>
                        <div class="pcat">{{ $item->product->category->name ?? '' }}@if($item->product->sku) · SKU: {{ $item->product->sku }}@endif</div>
                    </td>
                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₱{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="clearfix">
            <div class="totals">
                <div class="t-row"><span>Subtotal</span><span>₱{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="t-row"><span>Tax</span><span>₱{{ number_format($order->tax, 2) }}</span></div>
                <div class="t-row"><span>Shipping</span><span>{{ $order->shipping > 0 ? '₱'.number_format($order->shipping, 2) : 'Free' }}</span></div>
                <div class="t-row grand"><span>Order Total</span><span class="amount">₱{{ number_format($order->total, 2) }}</span></div>
            </div>
        </div>

        @if($order->sale && $order->sale->user)
        <div class="processed">
            <strong>Processed by:</strong> {{ $order->sale->user->name }} ({{ $order->sale->user->email }})
            &nbsp;·&nbsp; <strong>Last updated:</strong> {{ $order->sale->updated_at->format('M d, Y g:i A') }}
        </div>
        @endif
    </div>

    <div class="footer">
        <strong>TrackNet Internal Document — Sales Copy</strong><br>
        Order #{{ $order->order_number }} · Generated on {{ now()->format('F d, Y \a\t g:i A') }}<br>
        This receipt is for internal record-keeping purposes.
    </div>
</body>
</html>
