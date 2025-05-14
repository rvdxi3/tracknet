<!-- resources/views/website/products/index.blade.php -->

@extends('website.layouts.app')

@section('title', 'Products')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card mb-4">
            <div class="card-header">
                Categories
            </div>
            <div class="list-group list-group-flush">
                @foreach($categories as $category)
                    <a href="{{ route('products.category', $category) }}" class="list-group-item list-group-item-action {{ request()->is('categories/'.$category->id) ? 'active' : '' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">
                Filters
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="form-group">
                        <label for="price_min">Min Price</label>
                        <input type="number" class="form-control" id="price_min" name="price_min" value="{{ request('price_min') }}">
                    </div>
                    <div class="form-group">
                        <label for="price_max">Max Price</label>
                        <input type="number" class="form-control" id="price_max" name="price_max" value="{{ request('price_max') }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
                    @if(request()->has('price_min') || request()->has('price_max'))
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-block mt-2">Clear Filters</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>All Products</h2>
            </div>
            <div class="col-md-6 text-right">
                <form method="GET" action="{{ route('products.index') }}" class="form-inline">
                    <div class="form-group mr-2">
                        <select class="form-control" name="sort" onchange="this.form.submit()">
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="row">
            @foreach($products as $product)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('images/placeholder.png') }}" class="card-img-top" alt="{{ $product->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">${{ number_format($product->price, 2) }}</p>
                            @if($product->stock > 0)
                                <span class="badge badge-success">In Stock</span>
                            @else
                                <span class="badge badge-danger">Out of Stock</span>
                            @endif
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-primary btn-sm">View Details</a>
                            @if($product->stock > 0)
                                <form action="{{ route('cart.store') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="btn btn-success btn-sm">Add to Cart</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection