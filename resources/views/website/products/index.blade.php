@extends('website.layouts.app')

@section('title', 'Products')

@push('styles')
<style>
    .products-page-header {
        background: linear-gradient(135deg, #0f172a, #1e3a8a);
        color: #fff;
        padding: 2.5rem 0 2rem;
        margin: -1rem 0 2rem;
    }
    .products-page-header h1 {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
    }
    .products-page-header .breadcrumb-item,
    .products-page-header .breadcrumb-item a,
    .products-page-header .breadcrumb-item + .breadcrumb-item::before {
        color: rgba(255,255,255,0.6);
    }
    .products-page-header .breadcrumb-item.active { color: rgba(255,255,255,0.9); }

    /* Search bar on product list */
    .search-bar-wrapper {
        background: #fff;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
    }

    /* Filter sidebar */
    .filter-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .filter-card-header {
        padding: 0.9rem 1.25rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #475569;
    }
    .filter-card-body { padding: 0.75rem 0; }
    .filter-cat-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 1.25rem;
        font-size: 0.875rem;
        color: #475569;
        text-decoration: none;
        border-left: 3px solid transparent;
        transition: all 0.2s;
    }
    .filter-cat-link:hover,
    .filter-cat-link.active {
        background: #eff6ff;
        border-left-color: #2563eb;
        color: #2563eb;
        font-weight: 600;
    }
    .filter-cat-link .badge {
        font-size: 0.7rem;
        background: #e2e8f0;
        color: #64748b;
        padding: 0.2em 0.5em;
    }
    .filter-cat-link.active .badge {
        background: #dbeafe;
        color: #1d4ed8;
    }

    /* Toolbar */
    .products-toolbar {
        background: #fff;
        border-radius: 10px;
        padding: 0.75rem 1.25rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .products-toolbar .results-count {
        font-size: 0.875rem;
        color: #64748b;
    }
    .products-toolbar .results-count strong { color: #0f172a; }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="products-page-header" style="margin:-1rem -0.75rem 2rem;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb mb-0" style="font-size:0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Products</li>
            </ol>
        </nav>
        <h1>
            @if(request('search'))
                Search: "{{ request('search') }}"
            @elseif(isset($currentCategory))
                {{ $currentCategory->name }}
            @else
                All Products
            @endif
        </h1>
    </div>
</div>

<div class="row g-4">
    {{-- ── Filter Sidebar ── --}}
    <div class="col-md-3 filter-sidebar">
        {{-- Search --}}
        <div class="filter-card">
            <div class="filter-card-header"><i class="fas fa-search me-1"></i> Search</div>
            <div class="filter-card-body px-3 pt-2 pb-3">
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" name="search"
                               value="{{ request('search') }}" placeholder="Search products...">
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Categories --}}
        <div class="filter-card">
            <div class="filter-card-header"><i class="fas fa-tag me-1"></i> Categories</div>
            <div class="filter-card-body">
                <a href="{{ route('products.index') }}"
                   class="filter-cat-link {{ !isset($currentCategory) ? 'active' : '' }}">
                    All Products
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('products.category', $category) }}"
                       class="filter-cat-link {{ isset($currentCategory) && $currentCategory->id == $category->id ? 'active' : '' }}">
                        {{ $category->name }}
                        <span class="badge rounded-pill">{{ $category->products_count ?? '' }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Price Filter --}}
        <div class="filter-card">
            <div class="filter-card-header"><i class="fas fa-dollar-sign me-1"></i> Price Range</div>
            <div class="filter-card-body px-3 pt-2 pb-3">
                <form method="GET" action="{{ request()->url() }}">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    <div class="mb-2">
                        <label class="form-label">Min Price (₱)</label>
                        <input type="number" class="form-control form-control-sm" name="price_min"
                               value="{{ request('price_min') }}" min="0" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Max Price (₱)</label>
                        <input type="number" class="form-control form-control-sm" name="price_max"
                               value="{{ request('price_max') }}" min="0" placeholder="9999">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Apply</button>
                    @if(request()->hasAny(['price_min','price_max']))
                        <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                            <i class="fas fa-times me-1"></i>Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- ── Product Grid ── --}}
    <div class="col-md-9">
        {{-- Toolbar --}}
        <div class="products-toolbar">
            <div class="results-count">
                Showing <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong>
                of <strong>{{ $products->total() }}</strong> products
                @if(request('search'))
                    for "<em>{{ request('search') }}</em>"
                @endif
            </div>
            <form method="GET" action="{{ request()->url() }}" class="d-flex align-items-center gap-2">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('price_min'))
                    <input type="hidden" name="price_min" value="{{ request('price_min') }}">
                @endif
                @if(request('price_max'))
                    <input type="hidden" name="price_max" value="{{ request('price_max') }}">
                @endif
                <label class="form-label mb-0 text-muted" style="font-size:0.82rem;white-space:nowrap;">Sort by:</label>
                <select class="form-select form-select-sm" name="sort" onchange="this.form.submit()" style="width:auto;">
                    <option value="name_asc"   {{ request('sort') == 'name_asc'   ? 'selected' : '' }}>Name A–Z</option>
                    <option value="name_desc"  {{ request('sort') == 'name_desc'  ? 'selected' : '' }}>Name Z–A</option>
                    <option value="price_asc"  {{ request('sort') == 'price_asc'  ? 'selected' : '' }}>Price: Low–High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High–Low</option>
                </select>
            </form>
        </div>

        {{-- Product Grid --}}
        @if($products->count() > 0)
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
                @foreach($products as $product)
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
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <div>
                                        @if($product->discount > 0)
                                            <span class="product-price">₱{{ number_format($product->price * (1 - $product->discount/100), 2) }}</span>
                                            <span class="product-price-old">₱{{ number_format($product->price, 2) }}</span>
                                        @else
                                            <span class="product-price">₱{{ number_format($product->price, 2) }}</span>
                                        @endif
                                    </div>
                                    @if($product->stock > 0)
                                        <span class="badge badge-stock-in"><i class="fas fa-check me-1"></i>In Stock</span>
                                    @else
                                        <span class="badge badge-stock-out">Out of Stock</span>
                                    @endif
                                </div>
                            </div>
                            <div class="card-footer d-flex gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                @if($product->stock > 0)
                                    <form action="{{ route('cart.store') }}" method="POST" class="flex-fill">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-cart-plus me-1"></i>Add
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary btn-sm flex-fill" disabled>Unavailable</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-5">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3 d-block opacity-25"></i>
                <h5 class="text-muted">No products found</h5>
                <p class="text-muted small">Try adjusting your filters or search term.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Clear All Filters</a>
            </div>
        @endif
    </div>
</div>

@endsection
