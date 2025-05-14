<!-- resources/views/website/account/orders/show.blade.php -->

@extends('website.layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="{{ route('account.index') }}" class="list-group-item list-group-item-action">Dashboard</a>
            <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action">My Orders</a>
            <a href="{{ route('account.edit') }}" class="list-group-item list-group-item-action">Account Details</a>
            <a href="{{ route('logout') }}" class="list-group-item list-group-item-action" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                Order #{{ $order->order_number }}
                <span class="float-right">
                    Placed on {{ $order->created_at->format('M d, Y') }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Shipping Information</h5>
                        <address>
                            {{ $order->shipping_address }}
                        </address>
                        
                        @if($order->billing_address)
                            <h5 class="mt-3">Billing Information</h5>
                            <address>
                                {{ $order->billing_address }}
                            </address>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h5>Order Status</h5>
                        @if($order->sale)
                            <div class="mb-3">
                                <strong>Payment:</strong> 
                                <span class="badge 
                                    {{ $order->sale->payment_status == 'paid' ? 'badge-success' : 
                                       ($order->sale->payment_status == 'failed' ? 'badge-danger' : 'badge-warning') }}">
                                    {{ ucfirst($order->sale->payment_status) }}
                                </span>
                            </div>
                            <div>
                                <strong>Fulfillment:</strong> 
                                <span class="badge 
                                    {{ $order->sale->fulfillment_status == 'delivered' ? 'badge-success' : 
                                       ($order->sale->fulfillment_status == 'shipped' ? 'badge-info' : 
                                       ($order->sale->fulfillment_status == 'processing' ? 'badge-warning' : 
                                       ($order->sale->fulfillment_status == 'cancelled' ? 'badge-danger' : 'badge-secondary'))) }}">
                                    {{ ucfirst($order->sale->fulfillment_status) }}
                                </span>
                            </div>
                        @else
                            <div class="alert alert-info">
                                Your order is being processed.
                            </div>
                        @endif
                        
                        <h5 class="mt-3">Payment Method</h5>
                        <p>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                    </div>
                </div>
                
                <h5>Order Items</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $item->product->image ? asset('storage/'.$item->product->image) : asset('images/placeholder.png') }}" width="50" class="mr-3" alt="{{ $item->product->name }}">
                                            <div>
                                                <h6 class="mb-0">{{ $item->product->name }}</h6>
                                                <small class="text-muted">{{ $item->product->category->name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                <td>${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Tax:</strong></td>
                                <td>${{ number_format($order->tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Shipping:</strong></td>
                                <td>${{ number_format($order->shipping, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td>${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                @if($order->notes)
                    <h5 class="mt-4">Order Notes</h5>
                    <p>{{ $order->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection