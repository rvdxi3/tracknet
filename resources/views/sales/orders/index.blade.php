@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Order Management</h1>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-filter"></i> Filter Orders
            </button>
            <div class="dropdown-menu" aria-labelledby="filterDropdown">
                <a class="dropdown-item" href="{{ route('sales.orders.index') }}">All Orders</a>
                <a class="dropdown-item" href="{{ route('sales.orders.index', ['status' => 'pending']) }}">Pending</a>
                <a class="dropdown-item" href="{{ route('sales.orders.index', ['status' => 'paid']) }}">Paid</a>
                <a class="dropdown-item" href="{{ route('sales.orders.index', ['status' => 'shipped']) }}">Shipped</a>
                <a class="dropdown-item" href="{{ route('sales.orders.index', ['status' => 'delivered']) }}">Delivered</a>
                <a class="dropdown-item" href="{{ route('sales.orders.index', ['status' => 'cancelled']) }}">Cancelled</a>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="ordersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Fulfillment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ $order->created_at->format('m/d/Y') }}</td>
                            <td>${{ number_format($order->total, 2) }}</td>
                            <td>
                                @if($order->sale)
                                <span class="badge badge-{{ 
                                    $order->sale->payment_status == 'paid' ? 'success' : 
                                    ($order->sale->payment_status == 'pending' ? 'warning' : 'danger') 
                                }}">
                                    {{ ucfirst($order->sale->payment_status) }}
                                </span>
                                @else
                                <span class="badge badge-secondary">Processing</span>
                                @endif
                            </td>
                            <td>
                                @if($order->sale)
                                <span class="badge badge-{{ 
                                    $order->sale->fulfillment_status == 'delivered' ? 'success' : 
                                    ($order->sale->fulfillment_status == 'shipped' ? 'info' : 
                                    ($order->sale->fulfillment_status == 'processing' ? 'warning' : 
                                    ($order->sale->fulfillment_status == 'cancelled' ? 'danger' : 'secondary'))) 
                                }}">
                                    {{ ucfirst($order->sale->fulfillment_status) }}
                                </span>
                                @else
                                <span class="badge badge-secondary">Processing</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('sales.orders.show', $order) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('sales.orders.edit', $order) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
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
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            "order": [[2, "desc"]]
        });
    });
</script>
@endsection