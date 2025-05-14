<!-- resources/views/website/account/orders/index.blade.php -->

@extends('website.layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="{{ route('account.index') }}" class="list-group-item list-group-item-action">Dashboard</a>
            <a href="{{ route('account.orders') }}" class="list-group-item list-group-item-action active">My Orders</a>
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
                My Orders
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($order->sale)
                                                @if($order->sale->fulfillment_status == 'delivered')
                                                    <span class="badge badge-success">Delivered</span>
                                                @elseif($order->sale->fulfillment_status == 'shipped')
                                                    <span class="badge badge-info">Shipped</span>
                                                @elseif($order->sale->fulfillment_status == 'processing')
                                                    <span class="badge badge-warning">Processing</span>
                                                @elseif($order->sale->fulfillment_status == 'cancelled')
                                                    <span class="badge badge-danger">Cancelled</span>
                                                @else
                                                    <span class="badge badge-secondary">Pending</span>
                                                @endif
                                            @else
                                                <span class="badge badge-secondary">Processing</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($order->total, 2) }}</td>
                                        <td>
                                            <a href="{{ route('account.orders.show', $order) }}" class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        You haven't placed any orders yet. <a href="{{ route('products.index') }}">Start shopping</a>.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection