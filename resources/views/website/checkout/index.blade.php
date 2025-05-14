<!-- resources/views/website/checkout/index.blade.php -->

@extends('website.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2>Checkout</h2>
        
        <div class="card mb-4">
            <div class="card-header">
                Shipping Information
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('checkout.store') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Shipping Address</label>
                        <textarea class="form-control" id="address" name="shipping_address" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="same_billing" name="same_billing">
                        <label class="form-check-label" for="same_billing">Billing address same as shipping</label>
                    </div>
                    
                    <div id="billing_address_fields" style="display: none;">
                        <div class="form-group">
                            <label for="billing_address">Billing Address</label>
                            <textarea class="form-control" id="billing_address" name="billing_address" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Order Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                Payment Method
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                        <label class="form-check-label" for="cod">
                            Cash on Delivery
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card">
                        <label class="form-check-label" for="credit_card">
                            Credit Card
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                        <label class="form-check-label" for="paypal">
                            PayPal
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                Order Summary
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($cartItems as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $item->product->name }} (x{{ $item->quantity }})
                            <span>${{ number_format($item->product->price * $item->quantity, 2) }}</span>
                        </li>
                    @endforeach
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Subtotal
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Tax ({{ config('cart.tax_rate') }}%)
                        <span>${{ number_format($tax, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center font-weight-bold">
                        Total
                        <span>${{ number_format($total, 2) }}</span>
                    </li>
                </ul>
                
                <button type="submit" class="btn btn-primary btn-block mt-3">Place Order</button>
                </form>
                
                <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-block mt-2">Back to Cart</a>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        $('#same_billing').change(function() {
            if(this.checked) {
                $('#billing_address_fields').hide();
            } else {
                $('#billing_address_fields').show();
            }
        });
    });
</script>
@endsection
@endsection