<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TrackNet') }} - Dashboard</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700,800" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

   <style>
@verbatim
        /* ── Variables ─────────────────────────────────────── */
        :root {
            --sb-w:        260px;
            --sb-w-mini:    72px;
            --sb-bg:       #0a0c10;        /* Dark matte black background */
            --sb-border:   #2a2e3a;         /* Dark gray border */
            --sb-shadow:   2px 0 25px rgba(0,0,0,.5);
            --accent:      #0a7aff;         /* Electric blue */
            --accent-glow: #0a7aff40;        /* Electric blue with transparency */
            --accent-soft: #1a2538;         /* Dark blue-tinted gray for hover */
            --nav-txt:     #b0b8c5;         /* Cool gray for navigation text */
            --nav-icon:    #6c7a96;         /* Muted blue-gray for icons */
            --lbl-txt:     #8a92a6;         /* Cool gray for labels */
            --body-bg:     #f4f6fb;
            --white:       #ffffff;          /* Pure white */
            --light-gray:  #eef0f4;          /* Light gray for borders/contrast */
            --ease:        .25s ease;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Nunito', sans-serif;
            background: var(--body-bg);
            min-height: 100vh;
            margin: 0;
        }

        /* ── Sidebar shell ──────────────────────────────────── */
        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--sb-w);
            display: flex;
            flex-direction: column;
            background: var(--sb-bg);
            box-shadow: var(--sb-shadow);
            z-index: 1040;
            transition: width var(--ease), transform var(--ease);
            overflow: hidden;
        }
        .sidebar.mini  { width: var(--sb-w-mini); }

        /* ── Brand / header ─────────────────────────────────── */
.sb-header {
    display: flex;
    align-items: center;
    justify-content: space-between;  /* brand left, toggle right */
    padding: 1.1rem 1rem 1.1rem 1.5rem; /* increased left padding from 1.2rem → 1.5rem */
    border-bottom: 1px solid var(--sb-border);
    min-height: 68px;
    flex-shrink: 0;
}

.sb-brand {
    display: flex;
    align-items: center;
    gap: 0rem;
    text-decoration: none;
    min-width: 0;
    flex: 1;  /* takes remaining space, pushes toggle to the right */
    margin-left: 0.50rem; /* space between brand and toggle */
}

.sb-logo {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--accent), #0055cc);
    color: var(--white);
    font-weight: 800;
    font-size: .75rem;
    letter-spacing: .04em;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px var(--accent-glow);
}

.sb-brand-text {
    overflow: hidden;
    max-width: 160px;
    margin-left: 0.50rem; 
    white-space: nowrap;
    transition: opacity var(--ease), max-width var(--ease);
}

.sb-brand-name {
    display: block;
    font-size: .93rem;
    font-weight: 800;
    color: var(--white);
    line-height: 1.2;
}

.sb-brand-sub {
    display: block;
    font-size: .68rem;
    color: var(--lbl-txt);
    text-transform: capitalize;
}

.sidebar.mini .sb-brand-text { opacity: 0; max-width: 0; }

/* collapse arrow – far right */
.sb-toggle {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: 1.5px solid var(--sb-border);
    background: #1e222a;
    color: var(--nav-icon);
    font-size: .6rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    margin-left: auto;  /* keeps it on the right edge */
    transition: border-color .2s, color .2s, transform .25s, background .2s;
}

.sb-toggle:hover { 
    border-color: var(--accent); 
    color: var(--accent);
    background: #252b36;
}

.sidebar.mini .sb-toggle { transform: rotate(180deg); }

/* mini: stack logo and toggle vertically */
.sidebar.mini .sb-header {
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: .75rem .5rem;
    gap: .45rem;
    min-height: auto;
}

.sidebar.mini .sb-brand { justify-content: center; }
.sidebar.mini .sb-toggle { margin-left: 0; }   /* remove auto margin when stacked */
        /* ── Nav scroll area ────────────────────────────────── */
        .sb-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: .25rem 0 .5rem;
        }
        .sb-scroll::-webkit-scrollbar { width: 3px; }
        .sb-scroll::-webkit-scrollbar-thumb { 
            background: #2a2e3a; 
            border-radius: 3px; 
        }
        .sb-scroll::-webkit-scrollbar-track { background: #0f131a; }

        /* Section labels */
        .sb-label {
            padding: .65rem 1.2rem .2rem;
            font-size: .63rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #6c7a96;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity var(--ease), max-height var(--ease), padding var(--ease);
            max-height: 36px;
        }
        .sidebar.mini .sb-label { opacity: 0; max-height: 0; padding-top: 0; padding-bottom: 0; }

        /* Nav list */
        .sb-nav { list-style: none; padding: 0 .75rem; margin: 0; }
        .sb-nav li { margin-bottom: 2px; }

        .sb-nav .nav-link {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .58rem .85rem;
            color: var(--nav-txt);
            text-decoration: none;
            font-size: .865rem;
            font-weight: 600;
            border-radius: 9px;
            transition: background .18s, color .18s;
            white-space: nowrap;
            position: relative;
        }
        .sb-nav .nav-link i {
            width: 20px;
            text-align: center;
            font-size: .88rem;
            color: var(--nav-icon);
            flex-shrink: 0;
            transition: color .18s;
        }
        .sb-nav .nav-link:hover { 
            background: var(--accent-soft); 
            color: var(--white); 
        }
        .sb-nav .nav-link:hover i { color: var(--accent); }
        .sb-nav .nav-link.active { 
            background: var(--accent); 
            color: var(--white);
            box-shadow: 0 4px 12px var(--accent-glow);
        }
        .sb-nav .nav-link.active i { color: var(--white); }

        .nav-lbl {
            overflow: hidden;
            max-width: 180px;
            transition: opacity var(--ease), max-width var(--ease);
        }
        .nav-badge { 
            margin-left: auto; 
            font-size: .62rem;
            background: var(--accent) !important;
            color: var(--white);
        }

        /* collapsed nav */
        .sidebar.mini .sb-nav { padding: 0 .5rem; }
        .sidebar.mini .sb-nav .nav-link { justify-content: center; padding: .62rem; gap: 0; }
        .sidebar.mini .nav-lbl { opacity: 0; max-width: 0; }
        .sidebar.mini .nav-badge { 
            position: absolute; 
            top: 3px; 
            right: 3px; 
            margin: 0;
            background: var(--accent) !important;
        }

        /* ── Footer / user ──────────────────────────────────── */
        .sb-footer {
            border-top: 1px solid var(--sb-border);
            padding: .8rem .9rem;
            flex-shrink: 0;
        }
        .sb-user {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .45rem .55rem;
            border-radius: 10px;
            cursor: default;
            transition: background .2s;
        }
        .sb-user:hover { background: #151a22; }
        .user-av {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #0055cc);
            color: var(--white);
            font-weight: 800;
            font-size: .85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px var(--accent-glow);
        }
        .user-meta {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            max-width: 150px;
            transition: opacity var(--ease), max-width var(--ease);
        }
        .user-name {
            font-size: .82rem;
            font-weight: 700;
            color: var(--white);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }
        .user-role {
            font-size: .68rem;
            color: var(--lbl-txt);
            text-transform: capitalize;
            line-height: 1.3;
        }
        .logout-btn {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--nav-icon);
            font-size: .85rem;
            flex-shrink: 0;
            transition: background .2s, color .2s;
        }
        .logout-btn:hover { 
            background: #2a1f1f; 
            color: #ff4d4d; 
        }
        .sidebar.mini .user-meta { opacity: 0; max-width: 0; }
        .sidebar.mini .sb-user { justify-content: center; }
        .sidebar.mini .logout-btn { display: none; }

        /* ── Main content ───────────────────────────────────── */
        .main-wrap {
            margin-left: var(--sb-w);
            min-height: 100vh;
            transition: margin-left var(--ease);
        }
        .main-wrap.mini { margin-left: var(--sb-w-mini); }
        .content-wrapper { padding: 1.5rem; }

        /* ── Mobile hamburger ───────────────────────────────── */
        .ham-btn {
            display: none;
            position: fixed;
            top: .9rem;
            left: .9rem;
            z-index: 1035;
            width: 38px;
            height: 38px;
            background: var(--white);
            border: 1.5px solid var(--light-gray);
            border-radius: 9px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #5a6072;
            font-size: .9rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
        }

        /* ── Overlay ────────────────────────────────────────── */
        .sb-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.6);
            z-index: 1039;
            backdrop-filter: blur(3px);
        }
        .sb-overlay.on { display: block; }

        /* ── Shared component styles (keep existing) ─────────── */
        .stat-card {
            border: none;
            border-radius: .75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
            border-left: 4px solid;
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.1); }
        .stat-card.primary { border-left-color: var(--accent); }
        .stat-card.success { border-left-color: #198754; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.danger  { border-left-color: #dc3545; }
        .stat-card.info    { border-left-color: #0dcaf0; }
        .stat-card .stat-icon  { font-size: 2rem; opacity: .25; }
        .stat-card .stat-label { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; }
        .stat-card .stat-value { font-size: 1.5rem; font-weight: 800; color: #1a1d2e; }

        .card {
            border: none;
            border-radius: .75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #eef0f4;
            font-weight: 700;
            border-radius: .75rem .75rem 0 0 !important;
        }
        .table th {
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #9aa0b4;
            border-top: none;
        }

        /* ── Responsive ─────────────────────────────────────── */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sb-w) !important;
            }
            .sidebar.open { transform: translateX(0); }
            .sb-toggle { display: none !important; }
            .main-wrap { margin-left: 0 !important; }
            .ham-btn { display: flex; }
            .content-wrapper { padding: 1rem; padding-top: 3.8rem; }
        }

        /* ── Mobile content ─────────────────────────────────── */
        @media (max-width: 767px) {
            /* Tighter side padding */
            .content-wrapper { padding-left: .75rem; padding-right: .75rem; }
            .content-wrapper .container-fluid { padding-left: 0 !important; padding-right: 0 !important; }

            /* Gradient header: shrink & fix top margin */
            .dashboard-header {
                margin-top: 0 !important;
                padding: 1.1rem 1.1rem !important;
                border-radius: 14px !important;
            }
            .dashboard-header h2 { font-size: 1.35rem !important; }
            .dashboard-header p   { font-size: .8rem !important; }
            .dashboard-header .badge { font-size: .62rem !important; }

            /* Cards: remove lift hover on touch */
            .card:hover { transform: none !important; }

            /* Card header wrap on very small */
            .card-header .d-flex { flex-wrap: wrap; gap: .5rem; }

            /* Table: tighten cell padding */
            .table td, .table th { padding-top: .55rem !important; padding-bottom: .55rem !important; }
            .table td.px-4, .table th.px-4 { padding-left: .75rem !important; }

            /* Action icon buttons: slightly larger tap target */
            .btn[style*="width: 32px"] {
                width: 36px !important;
                height: 36px !important;
            }

            /* Modal: full-width on phone */
            .modal-box {
                border-radius: 16px !important;
                max-height: 95vh !important;
            }

            /* Pagination: smaller buttons */
            .page-btn { min-width: 32px !important; height: 32px !important; font-size: .78rem !important; }

            /* Card footer: stack on very small */
            .card-footer .d-flex { flex-direction: column; align-items: flex-start !important; gap: .6rem !important; }
            .card-footer nav.custom-pagination { align-self: center; }

            /* Hide less-critical columns on phone */
            .col-hide-xs { display: none !important; }
        }

        @media (max-width: 480px) {
            .dashboard-header h2 { font-size: 1.15rem !important; }
            .modal-box-footer { flex-direction: column; }
            .modal-box-footer .modal-btn-cancel,
            .modal-box-footer .modal-btn-submit { width: 100%; justify-content: center; }
        }
@endverbatim
    </style>
    @stack('styles')
</head>
<body>

    <div class="sb-overlay" id="sbOverlay"></div>

    <button class="ham-btn" id="hamBtn" aria-label="Open menu">
        <i class="fas fa-bars"></i>
    </button>

    <!-- ═══════════ SIDEBAR ═══════════ -->
    <aside class="sidebar" id="sidebar">

        {{-- Brand --}}
        <div class="sb-header">
            <a class="sb-brand" href="{{ url('/') }}">
                <div class="sb-logo">TN</div>
                <div class="sb-brand-text">
                    <span class="sb-brand-name">TrackNet</span>
                    <span class="sb-brand-sub">
                        {{ auth()->check() ? ucfirst(auth()->user()->role).' Panel' : '' }}
                    </span>
                </div>
            </a>
            <button class="sb-toggle" id="sbToggle" title="Collapse sidebar">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>

       

        {{-- Navigation --}}
        @auth
        <div class="sb-scroll">
            <div class="sb-label">Navigation</div>
            <ul class="sb-nav">

                {{-- ── Admin ── --}}
                @if(Auth::user()->role === 'admin')
                    @php
                        $pendingCount = \App\Models\User::where('is_active', false)
                            ->whereNotNull('mfa_verified_at')
                            ->whereNull('rejected_at')
                            ->count();
                    @endphp
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                           href="{{ route('admin.dashboard') }}" title="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-lbl">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                           href="{{ route('admin.users.index') }}" title="Users">
                            <i class="fas fa-users"></i>
                            <span class="nav-lbl">Users</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}"
                           href="{{ route('admin.departments.index') }}" title="Departments">
                            <i class="fas fa-building"></i>
                            <span class="nav-lbl">Departments</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.pending-users.*') ? 'active' : '' }}"
                           href="{{ route('admin.pending-users.index') }}" title="Pending Approvals">
                            <i class="fas fa-user-clock"></i>
                            <span class="nav-lbl">Pending Approvals</span>
                            @if($pendingCount > 0)
                                <span class="badge bg-danger nav-badge" style="font-size:.6rem;">{{ $pendingCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                           href="{{ route('admin.customers.index') }}" title="Customers">
                            <i class="fas fa-user-tag"></i>
                            <span class="nav-lbl">Customers</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.activity-log') ? 'active' : '' }}"
                           href="{{ route('admin.activity-log') }}" title="Activity Log">
                            <i class="fas fa-clipboard-list"></i>
                            <span class="nav-lbl">Activity Log</span>
                        </a>
                    </li>

                {{-- ── Inventory ── --}}
                @elseif(Auth::user()->role === 'inventory')
                    <li>
                        <a class="nav-link {{ request()->routeIs('inventory.dashboard') ? 'active' : '' }}"
                           href="{{ route('inventory.dashboard') }}" title="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-lbl">Dashboard</span>
                        </a>
                    </li>

                    <div class="sb-label">Inventory</div>
                    <li>
                        <a class="nav-link {{ request()->routeIs('inventory.products.*') ? 'active' : '' }}"
                           href="{{ route('inventory.products.index') }}" title="Products">
                            <i class="fas fa-box"></i>
                            <span class="nav-lbl">Products</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('inventory.stock.index') ? 'active' : '' }}"
                           href="{{ route('inventory.stock.index') }}" title="Stock Levels">
                            <i class="fas fa-warehouse"></i>
                            <span class="nav-lbl">Stock Levels</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('inventory.stock.movements') ? 'active' : '' }}"
                           href="{{ route('inventory.stock.movements') }}" title="Movement History">
                            <i class="fas fa-exchange-alt"></i>
                            <span class="nav-lbl">Movement History</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('inventory.stock.alerts') ? 'active' : '' }}"
                           href="{{ route('inventory.stock.alerts') }}" title="Alerts">
                            <i class="fas fa-bell"></i>
                            <span class="nav-lbl">Alerts</span>
                        </a>
                    </li>

                    <div class="sb-label">Procurement</div>
                    <li>
                        <a class="nav-link {{ request()->routeIs('inventory.suppliers.*') ? 'active' : '' }}"
                           href="{{ route('inventory.suppliers.index') }}" title="Suppliers">
                            <i class="fas fa-truck"></i>
                            <span class="nav-lbl">Suppliers</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('inventory.purchase-orders.*') ? 'active' : '' }}"
                           href="{{ route('inventory.purchase-orders.index') }}" title="Purchase Orders">
                            <i class="fas fa-file-invoice"></i>
                            <span class="nav-lbl">Purchase Orders</span>
                        </a>
                    </li>

                {{-- ── Sales ── --}}
                @elseif(Auth::user()->role === 'sales')
                    <li>
                        <a class="nav-link {{ request()->routeIs('sales.dashboard') ? 'active' : '' }}"
                           href="{{ route('sales.dashboard') }}" title="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="nav-lbl">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('sales.orders.*') && !request()->routeIs('sales.reports.*') ? 'active' : '' }}"
                           href="{{ route('sales.orders.index') }}" title="Orders">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="nav-lbl">Orders</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('sales.reports.*') ? 'active' : '' }}"
                           href="{{ route('sales.reports.index') }}" title="Reports &amp; Analytics">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-lbl">Reports &amp; Analytics</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>

        {{-- User footer --}}
        <div class="sb-footer">
            <div class="sb-user">
                <div class="user-av">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="user-meta">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ Auth::user()->role }}</div>
                </div>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
        @endauth

    </aside>
    <!-- ═══════════════════════════════ -->

    <div class="main-wrap" id="mainWrap">
        <div class="content-wrapper">
            @include('partials.alerts')
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const sidebar  = document.getElementById('sidebar');
            const mainWrap = document.getElementById('mainWrap');
            const sbToggle = document.getElementById('sbToggle');
            const hamBtn   = document.getElementById('hamBtn');
            const overlay  = document.getElementById('sbOverlay');

            /* ── desktop: restore collapsed state (skip on mobile) ── */
            if (window.innerWidth > 991 && localStorage.getItem('sbMini') === '1') {
                sidebar.classList.add('mini');
                mainWrap.classList.add('mini');
            }

            /* ── desktop: collapse / expand ── */
            if (sbToggle) {
                sbToggle.addEventListener('click', function () {
                    const mini = sidebar.classList.toggle('mini');
                    mainWrap.classList.toggle('mini', mini);
                    localStorage.setItem('sbMini', mini ? '1' : '0');
                });
            }

            /* ── mobile: open (always show full labels, remove mini) ── */
            if (hamBtn) {
                hamBtn.addEventListener('click', function () {
                    sidebar.classList.remove('mini');
                    sidebar.classList.add('open');
                    overlay.classList.add('on');
                });
            }

            /* ── mobile: close via overlay ── */
            if (overlay) {
                overlay.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('on');
                });
            }
        })();
    </script>
    @stack('scripts')
</body>
</html>
