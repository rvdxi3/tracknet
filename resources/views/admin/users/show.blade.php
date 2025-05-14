@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>User Details: {{ $user->name }}</span>
                    <div>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <div class="mb-3">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem; margin: 0 auto;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-sm">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Role:</th>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $user->role == 'admin' ? 'danger' : 
                                            ($user->role == 'inventory' ? 'primary' : 
                                            ($user->role == 'sales' ? 'info' : 'secondary')) 
                                        }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Department:</th>
                                    <td>{{ $user->department->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Registered:</th>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($user->orders->isNotEmpty())
                        <hr>
                        <h5 class="mb-3">Recent Orders</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->orders->take(5) as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->created_at->format('m/d/Y') }}</td>
                                        <td>${{ number_format($order->total, 2) }}</td>
                                        <td>
                                            @if($order->sale)
                                                <span class="badge bg-{{ $order->sale->payment_status == 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($order->sale->payment_status) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Processing</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection