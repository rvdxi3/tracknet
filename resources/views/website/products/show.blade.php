<!-- resources/views/website/products/show.blade.php -->

@extends('website.layouts.app')

@section('title', $product->name)

@section('content')
<div class="row">
    <div class="col-md-6">
        <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('images/placeholder.png') }}" class="img-fluid" alt="{{ $product->name }}">
    </div>
    <div class="col-md-6">
        <h2>{{ $product->name }}</h2>
        <p class="text-muted">{{ $product->category->name }}</p>
        <h3 class="my-3">${{ number_format($product->price, 2) }}</h3>
        
        @if($product->stock > 0)
            <span class="badge badge-success">In Stock ({{ $product->stock }} available)</span>
        @else
            <span class="badge badge-danger">Out of Stock</span>
        @endif
        
        <div class="mt-4">
            <h4>Description</h4>
            <p>{{ $product->description }}</p>
        </div>
        
        <div class="mt-4">
            <h4>Specifications</h4>
            <ul>
                <li>SKU: {{ $product->sku }}</li>
                <!-- Add more specifications as needed -->
            </ul>
        </div>
        
        <div class="mt-4">
            @if($product->stock > 0)
                <form action="{{ route('cart.store') }}" method="POST" class="form-inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="form-group mr-2">
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                </form>
            @else
                <button class="btn btn-secondary" disabled>Out of Stock</button>
                <small class="text-muted d-block mt-2">Notify me when available</small>
            @endif
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <h3>Related Products</h3>
        <div class="row">
            @foreach($relatedProducts as $related)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <img src="{{ $related->image ? asset('storage/'.$related->image) : asset('images/placeholder.png') }}" class="card-img-top" alt="{{ $related->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $related->name }}</h5>
                            <p class="card-text">${{ number_format($related->price, 2) }}</p>
                            <a href="{{ route('products.show', $related) }}" class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection