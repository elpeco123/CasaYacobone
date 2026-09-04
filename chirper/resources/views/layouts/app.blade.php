<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Casa Yacobone') — Control de Stock</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --cy-primary: #1a1a2e;
            --cy-secondary: #16213e;
            --cy-accent: #e94560;
            --cy-accent-hover: #c73e54;
            --cy-gold: #d4a574;
            --cy-gold-light: #e8c9a0;
            --cy-surface: #0f3460;
            --cy-text: #f8fafc;
            --cy-text-muted: #cbd5e1;
            --cy-success: #2ecc71;
            --cy-warning: #f39c12;
            --cy-danger: #e74c3c;
            --cy-border: rgba(255,255,255,0.08);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--cy-primary) 0%, var(--cy-secondary) 50%, #0a0a1a 100%);
            min-height: 100vh;
            color: var(--cy-text);
        }

        .text-muted {
            color: #cbd5e1 !important;
        }

        /* ===== NAVBAR ===== */
        .navbar-custom {
            background: rgba(15, 15, 30, 0.85) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--cy-border);
            padding: 0.6rem 0;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.3rem;
            letter-spacing: -0.5px;
            color: var(--cy-gold) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.2s ease;
        }

        .navbar-brand:hover {
            transform: scale(1.03);
        }

        .navbar-brand .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--cy-gold), var(--cy-accent));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: white;
            box-shadow: 0 4px 15px rgba(233, 69, 96, 0.3);
        }

        .nav-link {
            color: var(--cy-text-muted) !important;
            font-weight: 500;
            font-size: 0.88rem;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.25s ease;
            position: relative;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--cy-gold) !important;
            background: rgba(212, 165, 116, 0.1);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 3px;
            background: var(--cy-gold);
            border-radius: 2px;
        }

        .nav-link i {
            font-size: 1rem;
        }

        .user-badge {
            background: linear-gradient(135deg, rgba(212, 165, 116, 0.15), rgba(233, 69, 96, 0.1));
            border: 1px solid rgba(212, 165, 116, 0.2);
            border-radius: 12px;
            padding: 0.35rem 0.9rem;
            font-size: 0.82rem;
            color: var(--cy-gold-light);
            font-weight: 500;
        }

        .user-badge .role-tag {
            background: var(--cy-accent);
            color: white;
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 0.4rem;
        }

        .btn-logout {
            background: rgba(231, 76, 60, 0.15);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            font-size: 0.82rem;
            padding: 0.35rem 0.9rem;
            border-radius: 8px;
            transition: all 0.25s ease;
        }

        .btn-logout:hover {
            background: rgba(231, 76, 60, 0.25);
            color: #ff6b6b;
            transform: translateY(-1px);
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            padding: 1.5rem 0 3rem;
        }

        /* ===== CARDS ===== */
        .card-glass {
            background: rgba(22, 33, 62, 0.6);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--cy-border);
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        .card-glass:hover {
            border-color: rgba(212, 165, 116, 0.2);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }

        .card-glass .card-body {
            padding: 1.5rem;
        }

        /* ===== KPI CARDS ===== */
        .kpi-card {
            background: rgba(22, 33, 62, 0.5);
            backdrop-filter: blur(15px);
            border: 1px solid var(--cy-border);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 16px 16px 0 0;
        }

        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .kpi-card.kpi-blue::before { background: linear-gradient(90deg, #3498db, #2ecc71); }
        .kpi-card.kpi-green::before { background: linear-gradient(90deg, #2ecc71, #27ae60); }
        .kpi-card.kpi-gold::before { background: linear-gradient(90deg, var(--cy-gold), #f39c12); }
        .kpi-card.kpi-red::before { background: linear-gradient(90deg, var(--cy-accent), #e74c3c); }

        .kpi-icon {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }

        .kpi-blue .kpi-icon { background: rgba(52, 152, 219, 0.15); color: #3498db; }
        .kpi-green .kpi-icon { background: rgba(46, 204, 113, 0.15); color: #2ecc71; }
        .kpi-gold .kpi-icon { background: rgba(212, 165, 116, 0.15); color: var(--cy-gold); }
        .kpi-red .kpi-icon { background: rgba(233, 69, 96, 0.15); color: var(--cy-accent); }

        .kpi-value {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
            margin-bottom: 0.3rem;
        }

        .kpi-label {
            font-size: 0.82rem;
            color: var(--cy-text-muted);
            font-weight: 500;
        }

        /* ===== TABLES ===== */
        .table-dark-custom {
            --bs-table-bg: transparent;
            --bs-table-color: var(--cy-text);
            --bs-table-border-color: var(--cy-border);
            --bs-table-striped-bg: rgba(255,255,255,0.02);
            --bs-table-hover-bg: rgba(212, 165, 116, 0.05);
            font-size: 0.88rem;
        }

        .table-dark-custom thead th {
            background: rgba(15, 52, 96, 0.5);
            border-bottom: 2px solid rgba(212, 165, 116, 0.2);
            font-weight: 600;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--cy-gold-light);
            padding: 0.9rem 1rem;
        }

        .table-dark-custom td {
            padding: 0.8rem 1rem;
            vertical-align: middle;
        }

        /* ===== BUTTONS ===== */
        .btn-accent {
            background: linear-gradient(135deg, var(--cy-accent), #c73e54);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.55rem 1.3rem;
            border-radius: 10px;
            font-size: 0.88rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(233, 69, 96, 0.3);
        }

        .btn-accent:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(233, 69, 96, 0.4);
            color: white;
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--cy-gold), #c4955e);
            border: none;
            color: var(--cy-primary);
            font-weight: 600;
            padding: 0.55rem 1.3rem;
            border-radius: 10px;
            font-size: 0.88rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(212, 165, 116, 0.3);
        }

        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 165, 116, 0.4);
            color: var(--cy-primary);
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--cy-text);
            padding: 0.45rem 1rem;
            border-radius: 8px;
            font-size: 0.82rem;
            transition: all 0.25s ease;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--cy-gold-light);
            border-color: rgba(212, 165, 116, 0.3);
        }

        /* ===== BADGES ===== */
        .badge-stock-ok {
            background: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
            font-weight: 600;
            padding: 0.35rem 0.7rem;
            border-radius: 8px;
            font-size: 0.78rem;
        }

        .badge-stock-bajo {
            background: rgba(243, 156, 18, 0.15);
            color: #f39c12;
            font-weight: 600;
            padding: 0.35rem 0.7rem;
            border-radius: 8px;
            font-size: 0.78rem;
            animation: pulse-warning 2s ease-in-out infinite;
        }

        .badge-stock-critico {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            font-weight: 600;
            padding: 0.35rem 0.7rem;
            border-radius: 8px;
            font-size: 0.78rem;
            animation: pulse-danger 1.5s ease-in-out infinite;
        }

        @keyframes pulse-warning {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        @keyframes pulse-danger {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }

        /* ===== FORMS ===== */
        .form-control-dark,
        .form-select-dark {
            background: rgba(15, 15, 30, 0.7);
            border: 1px solid var(--cy-border);
            color: var(--cy-text);
            border-radius: 10px;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
            transition: all 0.25s ease;
        }

        .form-select-dark option {
            background-color: #16213e;
            color: #eaeaea;
            padding: 10px;
        }

        .form-control-dark:focus,
        .form-select-dark:focus {
            background: rgba(15, 15, 30, 0.9);
            border-color: var(--cy-gold);
            color: var(--cy-text);
            box-shadow: 0 0 0 3px rgba(212, 165, 116, 0.15);
        }

        .form-control-dark::placeholder {
            color: var(--cy-text-muted);
        }

        /* Hide browser default number spinners for dark inputs */
        input[type=number].form-control-dark::-webkit-inner-spin-button,
        input[type=number].form-control-dark::-webkit-outer-spin-button {
            opacity: 0.5;
        }

        .bg-gold-light {
            background: rgba(212, 165, 116, 0.2) !important;
            color: var(--cy-gold-light) !important;
            border: 1px solid rgba(212, 165, 116, 0.3);
        }

        .text-gold-light {
            color: var(--cy-gold-light) !important;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: var(--cy-text-muted);
            margin-bottom: 0.4rem;
        }

        /* ===== ALERTS ===== */
        .alert-custom-success {
            background: rgba(46, 204, 113, 0.1);
            border: 1px solid rgba(46, 204, 113, 0.3);
            color: #2ecc71;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .alert-custom-error {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #e74c3c;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .alert-custom-warning {
            background: rgba(243, 156, 18, 0.1);
            border: 1px solid rgba(243, 156, 18, 0.3);
            color: #f39c12;
            border-radius: 12px;
            font-size: 0.9rem;
        }

        /* ===== PAGE HEADER ===== */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .page-header .breadcrumb {
            font-size: 0.82rem;
        }

        .page-header .breadcrumb-item a {
            color: var(--cy-text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .page-header .breadcrumb-item a:hover {
            color: var(--cy-gold);
        }

        .page-header .breadcrumb-item.active {
            color: var(--cy-gold-light);
        }

        /* ===== PAGINATION ===== */
        .pagination .page-link {
            background: rgba(22, 33, 62, 0.5);
            border: 1px solid var(--cy-border);
            color: var(--cy-text-muted);
            font-size: 0.85rem;
            border-radius: 8px !important;
            margin: 0 2px;
            transition: all 0.2s;
        }

        .pagination .page-link:hover {
            background: rgba(212, 165, 116, 0.15);
            border-color: rgba(212, 165, 116, 0.3);
            color: var(--cy-gold);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--cy-gold), #c4955e);
            border-color: var(--cy-gold);
            color: var(--cy-primary);
            font-weight: 600;
        }

        .pagination .page-item.disabled .page-link {
            background: rgba(15, 15, 30, 0.3);
            border-color: var(--cy-border);
            color: rgba(160, 160, 176, 0.3);
        }

        .pagination svg,
        nav svg {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 1rem !important;
            max-height: 1rem !important;
            vertical-align: middle;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--cy-primary); }
        ::-webkit-scrollbar-thumb {
            background: rgba(212, 165, 116, 0.3);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover { background: rgba(212, 165, 116, 0.5); }

        /* ===== ANIMATIONS ===== */
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .kpi-value { font-size: 1.4rem; }
            .navbar-brand { font-size: 1.1rem; }
            .main-content { padding: 1rem 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Navbar --}}
    @auth
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <span class="brand-icon"><i class="bi bi-house-door-fill"></i></span>
                Casa Yacobone
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                           href="{{ route('dashboard') }}">
                            <i class="bi bi-grid-1x2-fill me-1"></i> Dashboard
                        </a>
                    </li>
                    @if(Auth::user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}"
                           href="{{ route('productos.index') }}">
                            <i class="bi bi-box-seam-fill me-1"></i> Productos
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}"
                           href="{{ route('ventas.index') }}">
                            <i class="bi bi-cart-fill me-1"></i> Ventas
                        </a>
                    </li>
                    @if(Auth::user()->isAdmin())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('reportes.*') ? 'active' : '' }}"
                           href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-graph-up me-1"></i> Reportes
                        </a>
                        <ul class="dropdown-menu" style="background: rgba(22,33,62,0.95); border: 1px solid var(--cy-border); border-radius: 12px;">
                            <li>
                                <a class="dropdown-item text-light" href="{{ route('reportes.diario') }}"
                                   style="font-size: 0.88rem;">
                                    <i class="bi bi-calendar-day me-2"></i>Reporte Diario
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-light" href="{{ route('reportes.semanal') }}"
                                   style="font-size: 0.88rem;">
                                    <i class="bi bi-calendar-week me-2"></i>Reporte Semanal
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif

                    {{-- Menú Configuración (Admin y Vendedor) --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('caja.*') || request()->routeIs('users.*') || request()->routeIs('proveedores.*') || request()->routeIs('categorias.*') ? 'active' : '' }}"
                           href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-gear-fill me-1"></i> Configuración
                        </a>
                        <ul class="dropdown-menu shadow-lg" style="background: rgba(22,33,62,0.98); border: 1px solid rgba(212,165,116,0.25); border-radius: 12px; min-width: 230px;">
                            <li>
                                <a class="dropdown-item text-light d-flex align-items-center py-2" href="{{ route('caja.index') }}"
                                   style="font-size: 0.88rem; font-weight: 600;">
                                    <i class="bi bi-safe2-fill me-2" style="color: var(--cy-gold); font-size: 1.05rem;"></i>Abrir Caja (Cambio Inicial)
                                </a>
                            </li>
                            @if(Auth::user()->isAdmin())
                            <li><hr class="dropdown-divider my-1" style="border-color: rgba(255,255,255,0.12);"></li>
                            <li>
                                <a class="dropdown-item text-light py-1.5" href="{{ route('users.index') }}"
                                   style="font-size: 0.88rem;">
                                    <i class="bi bi-people-fill me-2" style="color: #cbd5e1;"></i>Usuarios
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-light py-1.5" href="{{ route('proveedores.index') }}"
                                   style="font-size: 0.88rem;">
                                    <i class="bi bi-truck me-2" style="color: #cbd5e1;"></i>Proveedores
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-light py-1.5" href="{{ route('categorias.index') }}"
                                   style="font-size: 0.88rem;">
                                    <i class="bi bi-tags-fill me-2" style="color: #cbd5e1;"></i>Categorías
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('caja.index') }}" class="btn btn-glass btn-sm d-none d-md-inline-flex align-items-center text-light" title="Apertura y Cambio en Caja" style="border-color: rgba(212,165,116,0.3);">
                        <i class="bi bi-cash-coin me-1" style="color: var(--cy-gold);"></i>
                        <span>Caja</span>
                    </a>
                    <span class="user-badge">
                        <i class="bi bi-person-fill me-1"></i>{{ Auth::user()->name }}
                        <span class="role-tag">{{ Auth::user()->role }}</span>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-logout btn-sm" title="Cerrar Sesión">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    {{-- Content --}}
    <main class="main-content">
        <div class="container">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-custom-success alert-dismissible fade show fade-in" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-custom-error alert-dismissible fade show fade-in" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-custom-error alert-dismissible fade show fade-in" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>Errores de validación:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
