<style>
    /* ── Navbar ── */
    .site-navbar {
        background: #fff;
        border-bottom: 1px solid var(--gray-200);
        padding: 0;
        position: sticky;
        top: 0;
        z-index: 1030;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .site-navbar .navbar-brand {
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--primary) !important;
        letter-spacing: -0.5px;
        padding: 0.9rem 0;
        gap: 0.4rem;
    }
    .site-navbar .navbar-brand span { color: var(--dark); }
    .site-navbar .nav-link {
        font-size: 0.88rem;
        font-weight: 500;
        color: var(--gray-600) !important;
        padding: 1rem 0.9rem !important;
        transition: color 0.2s;
        position: relative;
    }
    .site-navbar .nav-link:hover,
    .site-navbar .nav-link.active { color: var(--primary) !important; }
    .site-navbar .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0.9rem;
        right: 0.9rem;
        height: 2px;
        background: var(--primary);
        border-radius: 2px;
    }

    /* Search bar */
    .navbar-search { max-width: 360px; flex: 1; }
    .navbar-search .form-control {
        border-radius: 20px 0 0 20px;
        border: 1.5px solid var(--gray-200);
        border-right: none;
        background: var(--gray-50);
        font-size: 0.875rem;
        padding: 0.45rem 1rem;
    }
    .navbar-search .form-control:focus { background: #fff; }
    .navbar-search .btn {
        border-radius: 0 20px 20px 0;
        border: 1.5px solid var(--primary);
        background: var(--primary);
        color: #fff;
        padding: 0.45rem 1rem;
        font-size: 0.875rem;
    }
    .navbar-search .btn:hover { background: var(--primary-dark); }

    /* Cart badge */
    .cart-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.5rem 0.85rem !important;
        background: var(--primary-light);
        border-radius: 8px;
        color: var(--primary) !important;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        transition: var(--transition);
    }
    .cart-link:hover { background: var(--primary); color: #fff !important; }
    .cart-link .cart-count {
        position: absolute;
        top: -6px;
        right: -6px;
        background: var(--danger);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 3px;
    }

    /* User dropdown */
    .user-avatar-sm {
        width: 32px;
        height: 32px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
    }
    .dropdown-menu {
        border: none;
        box-shadow: var(--shadow-lg);
        border-radius: var(--radius);
        min-width: 200px;
        padding: 0.5rem 0;
    }
    .dropdown-item {
        font-size: 0.875rem;
        padding: 0.55rem 1.25rem;
        color: var(--gray-600);
        transition: background 0.15s;
    }
    .dropdown-item:hover { background: var(--gray-100); color: var(--primary); }
    .dropdown-item i { width: 18px; text-align: center; margin-right: 0.4rem; opacity: 0.7; }
    .dropdown-divider { border-color: var(--gray-200); margin: 0.25rem 0; }

    /* Announcement bar */
    .announcement-bar {
        background: var(--primary);
        color: #fff;
        font-size: 0.8rem;
        text-align: center;
        padding: 0.4rem 1rem;
        font-weight: 500;
    }
    .announcement-bar a { color: #fff; font-weight: 700; }
</style>

<!-- Announcement Bar -->
<div class="announcement-bar">
    <i class="fas fa-truck me-1"></i> Free shipping on orders over $500 &mdash;
    <a href="{{ route('products.index') }}">Shop Now &rarr;</a>
</div>

<!-- Main Navbar -->
<nav class="site-navbar navbar navbar-expand-lg">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <i class="fas fa-laptop-code me-2" style="color: var(--primary);"></i>
            <span>Track</span>Net
        </a>

        <!-- Mobile toggler -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Center links -->
            <ul class="navbar-nav me-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        Products
                    </a>
                </li>
                @php
                    $navCategories = \App\Models\Category::orderBy('name')->get();
                @endphp
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Categories
                    </a>
                    <ul class="dropdown-menu">
                        @foreach($navCategories as $cat)
                            <li>
                                <a class="dropdown-item" href="{{ route('products.category', $cat) }}">
                                    <i class="fas fa-tag"></i> {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                        @if($navCategories->isEmpty())
                            <li><span class="dropdown-item text-muted">No categories</span></li>
                        @endif
                    </ul>
                </li>
            </ul>

            <!-- Search bar -->
            <form class="d-flex navbar-search mx-auto" method="GET" action="{{ route('products.index') }}">
                <input class="form-control" type="search" name="search"
                       value="{{ request('search') }}"
                       placeholder="Search products...">
                <button class="btn" type="submit"><i class="fas fa-search"></i></button>
            </form>

            <!-- Right side -->
            <ul class="navbar-nav align-items-center ms-3 gap-2">
                @auth
                    {{-- Cart --}}
                    @php $cartCount = auth()->user()->cart?->items->count() ?? 0; @endphp
                    <li class="nav-item">
                        <a href="{{ route('cart.index') }}" class="cart-link">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="d-none d-md-inline">Cart</span>
                            @if($cartCount > 0)
                                <span class="cart-count">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </li>
                    {{-- User dropdown --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 py-2" href="#"
                           data-bs-toggle="dropdown">
                            <div class="user-avatar-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                            <span class="d-none d-lg-inline" style="font-size:0.875rem;font-weight:500;color:var(--gray-700);">
                                {{ Str::words(auth()->user()->name, 1, '') }}
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header text-truncate">{{ auth()->user()->name }}</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('account.index') }}">
                                    <i class="fas fa-user"></i> My Account
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('account.orders') }}">
                                    <i class="fas fa-box"></i> My Orders
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('header-logout-form').submit();">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                                <form id="header-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm px-3" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm px-3" href="{{ route('register') }}">Register</a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
