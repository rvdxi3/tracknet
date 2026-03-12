@extends('website.layouts.app')

@section('title', $product->name)

@push('styles')
<style>
    .product-detail-img-wrap {
        background: #f8fafc;
        border-radius: 16px;
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 360px;
        position: sticky;
        top: 90px;
    }
    .product-detail-img-wrap img {
        max-height: 340px;
        max-width: 100%;
        object-fit: contain;
    }
    .product-detail-category {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #2563eb;
        margin-bottom: 0.5rem;
    }
    .product-detail-title {
        font-size: 1.7rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 1rem;
    }
    .product-detail-price {
        font-size: 2rem;
        font-weight: 800;
        color: #2563eb;
    }
    .product-detail-price-old {
        font-size: 1.1rem;
        color: #94a3b8;
        text-decoration: line-through;
        margin-left: 0.5rem;
    }
    .product-detail-sku {
        font-size: 0.78rem;
        color: #94a3b8;
        margin-top: 0.5rem;
    }
    .stock-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.9rem;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 600;
    }
    .stock-in  { background: #d1fae5; color: #065f46; }
    .stock-out { background: #fee2e2; color: #991b1b; }

    /* Qty selector */
    .qty-selector {
        display: flex;
        align-items: center;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        width: fit-content;
    }
    .qty-selector button {
        border: none;
        background: #f1f5f9;
        width: 38px;
        height: 42px;
        font-size: 1.1rem;
        color: #475569;
        cursor: pointer;
        transition: background 0.2s;
    }
    .qty-selector button:hover { background: #e2e8f0; }
    .qty-selector input {
        border: none;
        border-left: 1.5px solid #e2e8f0;
        border-right: 1.5px solid #e2e8f0;
        width: 52px;
        height: 42px;
        text-align: center;
        font-weight: 700;
        font-size: 0.95rem;
        color: #0f172a;
        outline: none;
    }

    /* Specs table */
    .specs-table { font-size: 0.875rem; }
    .specs-table th {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        width: 38%;
        background: #f8fafc;
    }
    .specs-table td { color: #1e293b; }
    .specs-table tr:last-child th,
    .specs-table tr:last-child td { border-bottom: none; }

    /* Related products */
    .related-section { background: #f8fafc; border-radius: 16px; padding: 2rem; }
    .related-section h4 { font-size: 1.2rem; font-weight: 800; color: #0f172a; margin-bottom: 1.5rem; }

    /* Tab content */
    .nav-tabs { border-bottom: 2px solid #e2e8f0; }
    .nav-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.75rem 1.25rem;
        border-radius: 0;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
    }
    .nav-tabs .nav-link:hover { color: #2563eb; }
    .nav-tabs .nav-link.active { color: #2563eb; border-bottom-color: #2563eb; background: transparent; }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        @if($product->category)
            <li class="breadcrumb-item">
                <a href="{{ route('products.category', $product->category) }}">{{ $product->category->name }}</a>
            </li>
        @endif
        <li class="breadcrumb-item active text-truncate" style="max-width:200px;">{{ $product->name }}</li>
    </ol>
</nav>

<div class="row g-5 mb-5">
    {{-- Product Image --}}
    <div class="col-md-5">
        <div class="product-detail-img-wrap">
            <img src="{{ $product->image_url }}"
                 alt="{{ $product->name }}">
        </div>
    </div>

    {{-- Product Info --}}
    <div class="col-md-7">
        <div class="product-detail-category">{{ $product->category->name ?? 'Uncategorized' }}</div>
        <h1 class="product-detail-title">{{ $product->name }}</h1>

        {{-- Stock --}}
        @if($product->stock > 0)
            <span class="stock-indicator stock-in mb-3 d-inline-flex">
                <i class="fas fa-check-circle"></i>
                In Stock ({{ $product->stock }} available)
            </span>
        @else
            <span class="stock-indicator stock-out mb-3 d-inline-flex">
                <i class="fas fa-times-circle"></i>
                Out of Stock
            </span>
        @endif

        {{-- Price --}}
        <div class="d-flex align-items-baseline gap-2 my-3">
            @if($product->discount > 0)
                <span class="product-detail-price">
                    ₱{{ number_format($product->price * (1 - $product->discount/100), 2) }}
                </span>
                <span class="product-detail-price-old">₱{{ number_format($product->price, 2) }}</span>
                <span class="badge bg-danger ms-1">{{ $product->discount }}% OFF</span>
            @else
                <span class="product-detail-price">₱{{ number_format($product->price, 2) }}</span>
            @endif
        </div>
        <div class="product-detail-sku">SKU: {{ $product->sku }}</div>

        <hr class="my-4" style="border-color:#e2e8f0;">

        {{-- Short Description --}}
        @if($product->description)
            <p style="font-size:0.9rem;color:#475569;line-height:1.7;">
                {{ Str::limit($product->description, 220) }}
            </p>
        @endif

        {{-- Add to Cart --}}
        @if($product->stock > 0)
            @auth
                <form action="{{ route('cart.store') }}" method="POST" class="mt-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        {{-- Qty selector --}}
                        <div class="qty-selector">
                            <button type="button" onclick="changeQty(-1)">&#8722;</button>
                            <input type="number" name="quantity" id="qty-input"
                                   value="1" min="1" max="{{ $product->stock }}">
                            <button type="button" onclick="changeQty(1)">&#43;</button>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </button>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-lg px-3">
                            <i class="fas fa-shopping-cart"></i>
                        </a>
                    </div>
                </form>
            @else
                <div class="mt-4">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Purchase
                    </a>
                </div>
            @endauth
        @else
            <div class="mt-4">
                <button class="btn btn-secondary btn-lg px-4" disabled>
                    <i class="fas fa-times me-2"></i>Out of Stock
                </button>
            </div>
        @endif

        {{-- Trust badges --}}
        <div class="d-flex flex-wrap gap-3 mt-4 pt-3" style="border-top:1px solid #e2e8f0;">
            <div class="d-flex align-items-center gap-2 text-muted" style="font-size:0.8rem;">
                <i class="fas fa-shield-alt text-success"></i> 1-Year Warranty
            </div>
            <div class="d-flex align-items-center gap-2 text-muted" style="font-size:0.8rem;">
                <i class="fas fa-truck text-primary"></i> Free Ship $500+
            </div>
            <div class="d-flex align-items-center gap-2 text-muted" style="font-size:0.8rem;">
                <i class="fas fa-undo text-warning"></i> 15-Day Returns
            </div>
            <div class="d-flex align-items-center gap-2 text-muted" style="font-size:0.8rem;">
                <i class="fas fa-lock text-danger"></i> Secure Checkout
            </div>
        </div>
    </div>
</div>

{{-- Tabs: Description & Specs --}}
<div class="card mb-5" style="border-radius:16px;">
    <div class="card-body px-4 pt-0 pb-4">
        <ul class="nav nav-tabs mb-4" id="productTabs">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-desc">Description</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-specs">Specifications</button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-desc">
                @if($product->description)
                    <p style="font-size:0.9rem;line-height:1.8;color:#475569;">{{ $product->description }}</p>
                @else
                    <p class="text-muted">No description available.</p>
                @endif
            </div>
            <div class="tab-pane fade" id="tab-specs">
                <table class="table specs-table">
                    <tbody>
                        <tr>
                            <th>SKU</th>
                            <td>{{ $product->sku }}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>{{ $product->category->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Price</th>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                        </tr>
                        @if($product->discount > 0)
                            <tr>
                                <th>Discount</th>
                                <td>{{ $product->discount }}%</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Stock</th>
                            <td>{{ $product->stock }} units</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Related Products --}}
@if($relatedProducts->count() > 0)
    <div class="related-section mb-4">
        <h4><i class="fas fa-th-large me-2 text-primary"></i>Related Products</h4>
        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach($relatedProducts as $related)
                <div class="col">
                    <div class="product-card h-100">
                        <img src="{{ $related->image ? asset('storage/'.$related->image) : 'https://placehold.co/400x300/f1f5f9/94a3b8?text=No+Image' }}"
                             class="product-img w-100" alt="{{ $related->name }}">
                        <div class="card-body">
                            <div class="product-title" style="font-size:0.85rem;">{{ Str::limit($related->name, 40) }}</div>
                            <div class="product-price" style="font-size:1rem;">₱{{ number_format($related->price, 2) }}</div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('products.show', $related) }}" class="btn btn-outline-primary btn-sm w-100">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
    function changeQty(delta) {
        const input = document.getElementById('qty-input');
        const max = parseInt(input.getAttribute('max')) || 999;
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > max) val = max;
        input.value = val;
    }
</script>
@endpush
