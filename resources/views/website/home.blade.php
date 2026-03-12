@extends('website.layouts.app')

@section('show-footer', true)
@section('title', 'Home — Computer Parts & Accessories')

@push('styles')
<style>
    /* Hero */
    .hero-section {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
        min-height: 88vh;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url('https://images.unsplash.com/photo-1518770660439-4636190af475?w=1600&q=80') center/cover no-repeat;
        opacity: 0.08;
    }
    .hero-section .hero-content { position: relative; z-index: 1; }
    .hero-section .hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: rgba(255,255,255,0.85);
        padding: 0.35rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }
    .hero-section h1 {
        font-size: clamp(2.2rem, 5vw, 3.8rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.15;
        letter-spacing: -1px;
        margin-bottom: 1.25rem;
    }
    .hero-section h1 span { color: #f59e0b; }
    .hero-section p.lead {
        font-size: 1.1rem;
        color: rgba(255,255,255,0.75);
        max-width: 520px;
        margin-bottom: 2rem;
    }
    .hero-stats { display: flex; gap: 2.5rem; margin-top: 2.5rem; }
    .hero-stat-value { font-size: 1.75rem; font-weight: 800; color: #fff; line-height: 1; }
    .hero-stat-label { font-size: 0.78rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.06em; margin-top: 0.2rem; }
    .hero-image-wrap img {
        max-height: 480px;
        object-fit: cover;
        border-radius: 20px;
        filter: drop-shadow(0 30px 60px rgba(0,0,0,0.4));
        animation: float 4s ease-in-out infinite;
    }
    @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-18px); } }

    /* Feature strip */
    .feature-strip { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 1rem 0; }
    .feature-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; }
    .feature-item i { font-size: 1.4rem; color: #2563eb; flex-shrink: 0; }
    .feature-item .fi-title { font-size: 0.85rem; font-weight: 700; color: #0f172a; line-height: 1.2; }
    .feature-item .fi-sub { font-size: 0.75rem; color: #64748b; }

    /* Section headings */
    .section-head { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem; }
    .section-head h2 { font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0; position: relative; padding-bottom: 0.6rem; }
    .section-head h2::after { content: ''; position: absolute; left: 0; bottom: 0; width: 36px; height: 3px; background: #2563eb; border-radius: 2px; }
    .section-head .view-all { font-size: 0.875rem; color: #2563eb; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 0.3rem; }
    .section-head .view-all:hover { text-decoration: underline; }

    /* Category carousel */
    .cat-carousel-wrap {
        position: relative; padding: 0 2.5rem;
    }
    .cat-carousel {
        display: flex; gap: 1rem; overflow-x: auto; scroll-behavior: smooth;
        scrollbar-width: none; -ms-overflow-style: none; padding: 0.5rem 0;
    }
    .cat-carousel::-webkit-scrollbar { display: none; }
    .cat-carousel-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 40px; height: 40px; border-radius: 50%; border: none;
        background: #fff; box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; color: #475569; cursor: pointer; z-index: 5;
        transition: all 0.2s ease;
    }
    .cat-carousel-btn:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.18); color: #1e293b; }
    .cat-carousel-btn.prev { left: 0; }
    .cat-carousel-btn.next { right: 0; }
    .category-card {
        flex: 0 0 auto; width: 150px; text-decoration: none; display: flex;
        flex-direction: column; align-items: center; text-align: center;
        background: #fff; border-radius: 14px; padding: 1.25rem 0.75rem 1rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07); transition: all 0.25s ease;
    }
    .category-card:hover { transform: translateY(-6px); box-shadow: 0 10px 40px rgba(0,0,0,0.12); }
    .category-card .cat-img {
        width: 100px; height: 100px; object-fit: contain; margin-bottom: 0.75rem;
        transition: transform 0.25s ease;
    }
    .category-card:hover .cat-img { transform: scale(1.08); }
    .category-card .cat-name {
        font-size: 0.72rem; font-weight: 800; color: #1e293b;
        text-transform: uppercase; letter-spacing: 0.04em; line-height: 1.3;
    }

    /* CTA Banner */
    .cta-banner {
        background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
        border-radius: 12px; padding: 3rem 2.5rem; color: #fff;
        position: relative; overflow: hidden;
    }
    .cta-banner::before { content: ''; position: absolute; right: -60px; top: -60px; width: 260px; height: 260px; background: rgba(255,255,255,0.06); border-radius: 50%; }
    .cta-banner h2 { font-size: 1.75rem; font-weight: 800; margin-bottom: 0.75rem; }
    .cta-banner p { font-size: 1rem; opacity: 0.85; max-width: 460px; margin-bottom: 1.5rem; }

    /* Brand logos */
    .brand-logo {
        display: flex; align-items: center; justify-content: center; padding: 1.25rem;
        background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.25s ease; height: 80px;
    }
    .brand-logo:hover { box-shadow: 0 10px 40px rgba(0,0,0,0.12); transform: translateY(-3px); }
    .brand-logo span { font-size: 1.1rem; font-weight: 800; color: #64748b; letter-spacing: -0.5px; transition: color 0.2s; }
    .brand-logo:hover span { color: #2563eb; }
</style>
@endpush

@section('content')

{{-- ── Hero ── --}}
<section class="hero-section" style="margin: -1.5rem -0.75rem 0; border-radius: 0 0 20px 20px; overflow: hidden;">
    <div class="container" style="padding: 4rem 1rem;">
        <div class="row align-items-center gy-5">
            <div class="col-lg-6 hero-content">
                <div class="hero-eyebrow">
                    <i class="fas fa-bolt"></i> New Arrivals — Spring 2026
                </div>
                <h1>Build Your <span>Dream PC</span><br>The Right Way</h1>
                <p class="lead">
                    Premium processors, graphics cards, memory, and more — sourced from the world's best brands at unbeatable prices.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                    </a>
                    <a href="#categories" class="btn btn-outline-light btn-lg px-4">
                        Browse Categories
                    </a>
                </div>
                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-value">{{ \App\Models\Product::count() }}+</div>
                        <div class="hero-stat-label">Products</div>
                    </div>
                    <div>
                        <div class="hero-stat-value">{{ \App\Models\Category::count() }}</div>
                        <div class="hero-stat-label">Categories</div>
                    </div>
                    <div>
                        <div class="hero-stat-value">Free</div>
                        <div class="hero-stat-label">Shipping $500+</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center hero-image-wrap">
                <img src="https://images.unsplash.com/photo-1591405351990-4726e331f141?w=600&q=80"
                     alt="PC Components" class="w-100">
            </div>
        </div>
    </div>
</section>

{{-- ── Feature strip ── --}}
<div class="feature-strip">
    <div class="container">
        <div class="row row-cols-2 row-cols-md-4 g-0">
            <div class="col border-end">
                <div class="feature-item">
                    <i class="fas fa-truck"></i>
                    <div><div class="fi-title">Free Shipping</div><div class="fi-sub">On orders over $500</div></div>
                </div>
            </div>
            <div class="col border-end">
                <div class="feature-item">
                    <i class="fas fa-shield-alt"></i>
                    <div><div class="fi-title">Warranty Covered</div><div class="fi-sub">1-year on all products</div></div>
                </div>
            </div>
            <div class="col border-end">
                <div class="feature-item">
                    <i class="fas fa-undo"></i>
                    <div><div class="fi-title">Easy Returns</div><div class="fi-sub">15-day return policy</div></div>
                </div>
            </div>
            <div class="col">
                <div class="feature-item">
                    <i class="fas fa-headset"></i>
                    <div><div class="fi-title">24/7 Support</div><div class="fi-sub">Expert help available</div></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Categories ── --}}
<div class="container py-5" id="categories">
    <div class="section-head">
        <h2>Shop by Category</h2>
        <a href="{{ route('products.index') }}" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
    </div>

    @php
        $categoryImages = [
            'Processors'     => 'processors.png',
            'Graphics Cards' => 'graphics-cards.png',
            'Memory'         => 'memory.png',
            'Storage'        => 'storage.png',
            'Motherboards'   => 'motherboards.png',
            'Power Supplies' => 'power-supplies.png',
            'Cases'          => 'cases.png',
            'Cooling'        => 'cooling.png',
        ];
        $shopCategories = \App\Models\Category::withCount('products')->orderBy('name')->get();
    @endphp

    <div class="cat-carousel-wrap">
        <button class="cat-carousel-btn prev" onclick="scrollCatCarousel(-1)"><i class="fas fa-chevron-left"></i></button>
        <div class="cat-carousel" id="catCarousel">
            @foreach($shopCategories as $cat)
                <a href="{{ route('products.category', $cat) }}" class="category-card">
                    <img src="{{ asset('images/' . ($categoryImages[$cat->name] ?? 'default_hero_banner.jpg')) }}"
                         alt="{{ $cat->name }}" class="cat-img">
                    <div class="cat-name">{{ $cat->name }}</div>
                </a>
            @endforeach
        </div>
        <button class="cat-carousel-btn next" onclick="scrollCatCarousel(1)"><i class="fas fa-chevron-right"></i></button>
    </div>

    @if($shopCategories->isEmpty())
        <div class="text-center text-muted py-4">
            <i class="fas fa-tag fa-2x mb-2"></i><br>No categories yet.
        </div>
    @endif
    </div>
</div>

{{-- ── Featured Products ── --}}
<div class="container pb-5">
    <div class="section-head">
        <h2>Featured Products</h2>
        <a href="{{ route('products.index') }}" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4">
        @forelse($featuredProducts as $product)
            <div class="col">
                <div class="product-card h-100 position-relative">
                    @if($product->discount > 0)
                        <div class="badge-discount">-{{ $product->discount }}%</div>
                    @endif
                    <img src="{{ $product->image_url }}"
                         class="product-img w-100" alt="{{ $product->name }}">
                    <div class="card-body">
                        <div class="product-category">{{ $product->category->name ?? '' }}</div>
                        <div class="product-title">{{ Str::limit($product->name, 50) }}</div>
                        <div class="d-flex align-items-center mt-1">
                            @if($product->discount > 0)
                                <span class="product-price">₱{{ number_format($product->price * (1 - $product->discount/100), 2) }}</span>
                                <span class="product-price-old">₱{{ number_format($product->price, 2) }}</span>
                            @else
                                <span class="product-price">₱{{ number_format($product->price, 2) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm flex-fill">
                            View
                        </a>
                        @if($product->stock > 0)
                            <form action="{{ route('cart.store') }}" method="POST" class="flex-fill">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </form>
                        @else
                            <button class="btn btn-secondary btn-sm flex-fill" disabled>Out of Stock</button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="fas fa-box-open fa-3x mb-3 d-block opacity-25"></i>
                No featured products yet. Check back soon!
            </div>
        @endforelse
    </div>
</div>

{{-- ── CTA Banner ── --}}
<div class="container pb-5">
    <div class="cta-banner">
        <div class="row align-items-center">
            <div class="col-lg-8" style="position:relative;z-index:1;">
                <h2><i class="fas fa-tools me-2"></i>Need Help Building Your PC?</h2>
                <p>Our team of experts can help you pick the perfect components for your budget and performance goals. Get personalised recommendations today.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-light btn-lg px-4" style="font-weight:700;color:#2563eb;">
                        Start Shopping
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">
                            Create Account
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Brands ── --}}
<div class="container pb-5">
    <div class="section-head">
        <h2>Trusted Brands</h2>
    </div>
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-6 g-3">
        @foreach(['Intel','AMD','NVIDIA','Corsair','Samsung','ASUS'] as $brand)
            <div class="col">
                <div class="brand-logo">
                    <span>{{ $brand }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
function scrollCatCarousel(dir) {
    var el = document.getElementById('catCarousel');
    if (el) el.scrollBy({ left: dir * 320, behavior: 'smooth' });
}
</script>
@endsection
