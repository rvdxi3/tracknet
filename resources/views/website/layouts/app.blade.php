<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrackNet &mdash; @yield('title', 'Computer Parts & Accessories')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary:       #2563eb;
            --primary-dark:  #1d4ed8;
            --primary-light: #eff6ff;
            --accent:        #f59e0b;
            --dark:          #0f172a;
            --gray-50:       #f8fafc;
            --gray-100:      #f1f5f9;
            --gray-200:      #e2e8f0;
            --gray-400:      #94a3b8;
            --gray-500:      #64748b;
            --gray-600:      #475569;
            --gray-800:      #1e293b;
            --success:       #10b981;
            --danger:        #ef4444;
            --radius:        12px;
            --radius-sm:     8px;
            --shadow:        0 4px 20px rgba(0,0,0,0.08);
            --shadow-lg:     0 10px 40px rgba(0,0,0,0.12);
            --transition:    all 0.25s ease;
        }

        * { box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.6;
        }

        /* ── Buttons ── */
        .btn {
            font-weight: 500;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--gray-200);
            border-radius: var(--radius) var(--radius) 0 0 !important;
            font-weight: 600;
        }

        /* ── Product cards ── */
        .product-card {
            border: none;
            border-radius: var(--radius);
            overflow: hidden;
            transition: var(--transition);
            box-shadow: var(--shadow);
            background: #fff;
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
        }
        .product-card .product-img {
            height: 210px;
            object-fit: contain;
            background: var(--gray-50);
            padding: 1.25rem;
            transition: var(--transition);
        }
        .product-card:hover .product-img {
            background: var(--primary-light);
        }
        .product-card .card-body { padding: 1rem 1.25rem 0.5rem; }
        .product-card .card-footer {
            background: #fff;
            border-top: 1px solid var(--gray-100);
            padding: 0.75rem 1.25rem;
        }
        .product-card .product-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
            line-height: 1.35;
        }
        .product-card .product-category {
            font-size: 0.75rem;
            color: var(--gray-400);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }
        .product-card .product-price {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--primary);
        }
        .product-card .product-price-old {
            font-size: 0.85rem;
            color: var(--gray-400);
            text-decoration: line-through;
            margin-left: 0.4rem;
        }

        /* ── Discount badge ── */
        .badge-discount {
            position: absolute;
            top: 12px;
            left: 12px;
            background: var(--danger);
            color: #fff;
            padding: 0.2rem 0.6rem;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 20px;
            z-index: 2;
        }

        /* ── Filter sidebar ── */
        .filter-sidebar .card { margin-bottom: 1.25rem; }
        .filter-sidebar .card-header { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.06em; }
        .filter-sidebar .list-group-item {
            border: none;
            border-left: 3px solid transparent;
            padding: 0.55rem 1rem;
            font-size: 0.88rem;
            color: var(--gray-600);
            transition: var(--transition);
            background: transparent;
        }
        .filter-sidebar .list-group-item:hover,
        .filter-sidebar .list-group-item.active {
            background: var(--primary-light);
            border-left-color: var(--primary);
            color: var(--primary);
            font-weight: 600;
        }

        /* ── Account sidebar ── */
        .account-sidebar .list-group-item {
            border: none;
            border-left: 3px solid transparent;
            padding: 0.6rem 1.25rem;
            font-size: 0.9rem;
            color: var(--gray-600);
            transition: var(--transition);
        }
        .account-sidebar .list-group-item:hover {
            background: var(--gray-100);
            border-left-color: var(--primary);
            color: var(--primary);
        }
        .account-sidebar .list-group-item.active {
            background: var(--primary-light);
            border-left-color: var(--primary);
            color: var(--primary);
            font-weight: 600;
        }
        .account-sidebar .list-group-item .nav-icon {
            width: 20px;
            text-align: center;
            margin-right: 0.5rem;
            opacity: 0.7;
        }

        /* ── Tables ── */
        .table th {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-500);
            border-top: none;
        }

        /* ── Badges ── */
        .badge { font-weight: 500; border-radius: 6px; }

        /* ── Stock badge ── */
        .badge-stock-in { background: #d1fae5; color: #065f46; }
        .badge-stock-out { background: #fee2e2; color: #991b1b; }

        /* ── Pagination ── */
        .pagination .page-link {
            border-radius: var(--radius-sm) !important;
            margin: 0 2px;
            border: 1px solid var(--gray-200);
            color: var(--gray-600);
            font-size: 0.875rem;
        }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }

        /* ── Form controls ── */
        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border: 1px solid var(--gray-200);
            font-size: 0.9rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 0.35rem;
        }

        /* ── Qty input spinner ── */
        .qty-input {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .qty-input input[type=number] {
            width: 60px;
            text-align: center;
        }

        /* ── Section title underline ── */
        .section-title {
            position: relative;
            display: inline-block;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -6px;
            width: 40px;
            height: 3px;
            background: var(--primary);
            border-radius: 2px;
        }

        /* ── Alert styles ── */
        .alert { border-radius: var(--radius-sm); border: none; }

        /* ── Breadcrumb ── */
        .breadcrumb-item a { color: var(--primary); text-decoration: none; }
        .breadcrumb-item.active { color: var(--gray-500); }
        .breadcrumb-item + .breadcrumb-item::before { color: var(--gray-400); }

        /* ── Cart table ── */
        .cart-img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            border-radius: var(--radius-sm);
            background: var(--gray-100);
            padding: 4px;
        }
        .cart-summary {
            background: #fff;
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
        }
        .cart-summary .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.4rem 0;
            font-size: 0.9rem;
            color: var(--gray-600);
            border-bottom: 1px dashed var(--gray-200);
        }
        .cart-summary .summary-row:last-child { border-bottom: none; }
        .cart-summary .summary-total {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0 0.2rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--dark);
        }

        /* ── Order status timeline ── */
        .order-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.82rem;
            font-weight: 600;
        }

        /* ── Scroll to top ── */
        #scrollTop {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 42px;
            height: 42px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            z-index: 999;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        #scrollTop:hover { background: var(--primary-dark); transform: translateY(-2px); }
        #scrollTop.visible { display: flex; }
    </style>

    @stack('styles')
</head>
<body>
    @include('website.layouts.header')

    <main>
        <div class="container py-4">
            @include('partials.alerts')
            @yield('content')
        </div>
    </main>

    @hasSection('show-footer')
        @include('website.layouts.footer')
    @endif

    <!-- Scroll to top -->
    <button id="scrollTop" aria-label="Scroll to top"><i class="fas fa-chevron-up"></i></button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Scroll to top
        const scrollBtn = document.getElementById('scrollTop');
        window.addEventListener('scroll', () => {
            scrollBtn.classList.toggle('visible', window.scrollY > 300);
        });
        scrollBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        // Auto-dismiss alerts after 5s
        document.querySelectorAll('.alert-dismissible').forEach(el => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                if (bsAlert) bsAlert.close();
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>
</html>
