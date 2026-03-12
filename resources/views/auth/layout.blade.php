<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TrackNet') — TrackNet</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --brand-dark:   #0f172a;
            --brand-navy:   #1e3a5f;
            --brand-accent: #2563eb;
        }

        /* ── Page Background ─────────────────────────────────── */
        body {
            min-height: 100vh;
            background-color: #0f172a;
            background-image:
                radial-gradient(ellipse 900px 700px at 20% 30%,  rgba(37,99,235,.22)  0%, transparent 65%),
                radial-gradient(ellipse 650px 600px at 85% 75%,  rgba(79,70,229,.16)  0%, transparent 65%),
                radial-gradient(ellipse 500px 450px at 65% 5%,   rgba(14,165,233,.11) 0%, transparent 60%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            font-family: 'Segoe UI', system-ui, sans-serif;
            position: relative;
            overflow: hidden;
        }

        /* subtle grid overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.017) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.017) 1px, transparent 1px);
            background-size: 52px 52px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Card ────────────────────────────────────────────── */
        .auth-card {
            background: #fff;
            border-radius: 20px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,.07),
                0 30px 60px rgba(0,0,0,.55),
                0 0 90px  rgba(37,99,235,.18);
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        .auth-card-wide { max-width: 520px; }

        /* ── Header ──────────────────────────────────────────── */
        .auth-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
            color: #fff;
            padding: 2rem 2rem 1.75rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        /* decorative circles in header */
        .auth-header::before {
            content: '';
            position: absolute;
            top: -35px; right: -35px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,.07);
            pointer-events: none;
        }
        .auth-header::after {
            content: '';
            position: absolute;
            bottom: -45px; left: -25px;
            width: 130px; height: 130px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
            pointer-events: none;
        }

        /* logo icon badge */
        .auth-logo-icon {
            width: 54px; height: 54px;
            background: rgba(255,255,255,.14);
            border: 1.5px solid rgba(255,255,255,.25);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto .8rem;
            font-size: 1.4rem;
            position: relative;
            z-index: 1;
        }
        .auth-header .brand {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -.5px;
            position: relative;
            z-index: 1;
        }
        .auth-header .brand span { color: #93c5fd; }
        .auth-header h5 {
            font-size: .88rem;
            font-weight: 400;
            opacity: .78;
            margin: .35rem 0 0;
            letter-spacing: .3px;
            position: relative;
            z-index: 1;
        }

        /* ── Body / Footer ───────────────────────────────────── */
        .auth-body { padding: 2rem; }

        .auth-footer {
            padding: 1rem 2rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: .85rem;
            color: #64748b;
        }

        /* ── Form Controls ───────────────────────────────────── */
        .input-group-text {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #94a3b8;
        }
        .form-control, .form-select {
            border-color: #e2e8f0;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand-accent);
            box-shadow: 0 0 0 .2rem rgba(37,99,235,.13);
        }

        /* ── Primary Button ──────────────────────────────────── */
        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            border: none;
            box-shadow: 0 4px 14px rgba(37,99,235,.38);
            transition: transform .15s, box-shadow .15s;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
            box-shadow: 0 6px 20px rgba(37,99,235,.48);
            transform: translateY(-1px);
        }
        .btn-primary:active { transform: translateY(0); }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-card @yield('card-class')">
        <div class="auth-header">
            <div class="auth-logo-icon">
                <i class="fas fa-cubes"></i>
            </div>
            <div class="brand">Track<span>Net</span></div>
            <h5>@yield('subtitle', 'Inventory Management')</h5>
        </div>
        <div class="auth-body">
            {{-- Flash messages --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
        @hasSection('footer')
        <div class="auth-footer">
            @yield('footer')
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
