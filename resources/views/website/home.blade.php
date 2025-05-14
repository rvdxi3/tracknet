<!-- resources/views/website/home.blade.php -->
@extends('website.layouts.app')

@section('title', 'Home - Computer Shop')

@section('content')
<style>
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 100px 0;
        margin-bottom: 50px;
    }
    
    .category-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 25px;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .category-icon {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: #0d6efd;
    }
    
    .product-card {
        transition: transform 0.3s ease;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 25px;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
    }
    
    .product-img {
        height: 200px;
        object-fit: contain;
        background: #f8f9fa;
        padding: 20px;
    }
    
    .btn-outline-primary {
        border-width: 2px;
    }
    
    .section-title {
        position: relative;
        margin-bottom: 30px;
        padding-bottom: 15px;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 50px;
        height: 3px;
        background: #0d6efd;
    }
    
    .discount-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
    }
</style>

<!-- Hero Section -->
<section class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-4">Build Your Dream PC</h1>
        <p class="lead mb-5">High-performance components at competitive prices with expert support</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg px-4">Shop Now</a>
            <a href="#" class="btn btn-outline-light btn-lg px-4">Learn More</a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<div class="container mb-5">
    <h2 class="section-title">Shop by Category</h2>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="category-card card h-100 text-center p-4">
                <div class="category-icon">
                    <i class="fas fa-microchip"></i>
                </div>
                <h5>Processors</h5>
                <p class="text-muted">Latest Intel & AMD CPUs</p>
                <a href="#" class="btn btn-outline-primary mt-auto">Browse</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="category-card card h-100 text-center p-4">
                <div class="category-icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <h5>Graphics Cards</h5>
                <p class="text-muted">Powerful GPUs for gaming</p>
                <a href="#" class="btn btn-outline-primary mt-auto">Browse</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="category-card card h-100 text-center p-4">
                <div class="category-icon">
                    <i class="fas fa-memory"></i>
                </div>
                <h5>Memory</h5>
                <p class="text-muted">High-speed RAM & SSDs</p>
                <a href="#" class="btn btn-outline-primary mt-auto">Browse</a>
            </div>
        </div>
        <div class="col-md-3">
            <div class="category-card card h-100 text-center p-4">
                <div class="category-icon">
                    <i class="fas fa-hdd"></i>
                </div>
                <h5>Storage</h5>
                <p class="text-muted">SSDs & Hard Drives</p>
                <a href="#" class="btn btn-outline-primary mt-auto">Browse</a>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">Featured Products</h2>
        <a href="{{ route('products.index') }}" class="btn btn-link">View All Products →</a>
    </div>
    <div class="row g-4">
        @foreach($featuredProducts as $product)
            <div class="col-md-3">
                <div class="product-card card h-100">
                    @if($product->discount > 0)
                        <div class="discount-badge">-{{ $product->discount }}%</div>
                    @endif
                    <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('images/placeholder.png') }}" 
                         class="card-img-top product-img" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ Str::limit($product->name, 40) }}</h5>
                        <div class="d-flex justify-content-between align-items-center">
                            @if($product->discount > 0)
                                <div>
                                    <span class="text-danger fw-bold">${{ number_format($product->price * (1 - $product->discount/100), 2) }}</span>
                                    <small class="text-decoration-line-through text-muted ms-2">${{ number_format($product->price, 2) }}</small>
                                </div>
                            @else
                                <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                            @endif
                            <span class="badge bg-success">In Stock</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="{{ route('products.show', $product) }}" class="btn btn-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Call to Action -->
<section class="bg-light py-5 mb-5">
    <div class="container text-center">
        <h2 class="mb-4">Need Help Building Your PC?</h2>
        <p class="lead mb-4">Our experts can help you choose the right components for your needs and budget.</p>
        <a href="#" class="btn btn-primary btn-lg px-4 me-3">PC Builder Tool</a>
        <a href="#" class="btn btn-outline-secondary btn-lg px-4">Contact Support</a>
    </div>
</section>

<!-- Brands Section -->
<div class="container mb-5">
    <h2 class="section-title text-center mb-5">Trusted Brands</h2>
    <div class="row g-4 justify-content-center">
        <div class="col-auto text-center">
            <img src="https://via.placeholder.com/150x80?text=Intel" alt="Intel" class="img-fluid" style="max-height: 50px;">
        </div>
        <div class="col-auto text-center">
            <img src="https://via.placeholder.com/150x80?text=AMD" alt="AMD" class="img-fluid" style="max-height: 50px;">
        </div>
        <div class="col-auto text-center">
            <img src="https://via.placeholder.com/150x80?text=NVIDIA" alt="NVIDIA" class="img-fluid" style="max-height: 50px;">
        </div>
        <div class="col-auto text-center">
            <img src="https://via.placeholder.com/150x80?text=Corsair" alt="Corsair" class="img-fluid" style="max-height: 50px;">
        </div>
        <div class="col-auto text-center">
            <img src="https://via.placeholder.com/150x80?text=Samsung" alt="Samsung" class="img-fluid" style="max-height: 50px;">
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush