<!-- resources/views/website/cart/index.blade.php -->

@extends('website.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<h2>Shopping Cart</h2>

@if($cartItems->count() > 0)
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
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
                        <td>${{ number_format($item->product->price, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.update', $item) }}" method="POST" class="form-inline">
                                @csrf
                                @method('PUT')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm" style="width: 60px;">
                                <button type="submit" class="btn btn-sm btn-link">Update</button>
                            </form>
                        </td>
                        <td>${{ number_format($item->product->price * $item->quantity, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.destroy', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                    <td colspan="2">${{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Tax ({{ config('cart.tax_rate') }}%):</strong></td>
                    <td colspan="2">${{ number_format($tax, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td colspan="2">${{ number_format($total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="text-right">
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary mr-2">Continue Shopping</a>
        <a href="{{ route('checkout.index') }}" class="btn btn-primary">Proceed to Checkout</a>
    </div>
@else
    <div class="alert alert-info">
        Your cart is empty. <a href="{{ route('products.index') }}">Start shopping</a>.
    </div>
@endif
@endsection