<!-- resources/views/website/account/index.blade.php -->

@extends('website.layouts.app')

@section('title', 'My Account')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="list-group">
            <a href="{{ route('account.index') }}" class="list-group-item list-group-item-action active">Dashboard</a>
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
                Account Dashboard
            </div>
            <div class="card-body">
                <h5 class="card-title">Welcome, {{ auth()->user()->name }}</h5>
                <p class="card-text">From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.</p>
                
                <div class="row mt-4">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Recent Orders</h6>
                                @if($recentOrders->count() > 0)
                                    <ul class="list-unstyled">
                                        @foreach($recentOrders as $order)
                                            <li>
                                                <a href="{{ route('account.orders.show', $order) }}">Order #{{ $order->order_number }}</a>
                                                <small class="text-muted d-block">Placed on {{ $order->created_at->format('M d, Y') }}</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>You haven't placed any orders yet.</p>
                                @endif
                                <a href="{{ route('account.orders') }}" class="btn btn-sm btn-outline-primary">View All Orders</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-2 text-muted">Account Details</h6>
                                <p class="card-text">
                                    {{ auth()->user()->name }}<br>
                                    {{ auth()->user()->email }}
                                </p>
                                <a href="{{ route('account.edit') }}" class="btn btn-sm btn-outline-primary">Edit Account</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection