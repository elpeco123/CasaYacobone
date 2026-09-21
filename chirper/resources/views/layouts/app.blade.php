<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Casa Yacobone') — Control de Stock</title>
    <link rel="icon" type="image/png" href="/images/logopag.png">

    {{-- ===== PWA: manifest + colores + compatibilidad iOS ===== --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="description" content="Casa Yacobone — Sistema de control de stock, ventas y reportes. Instalable en tu celular.">
    <meta name="theme-color" content="#1f1510">
    {{-- Estándar moderno (Chrome/Edge/Firefox) --}}
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Casa Yacobone">
    {{-- Compatibilidad Safari iOS --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Casa Yacobone">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="preload" href="{{ asset('images/logopag.png') }}" as="image" type="image/png">

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Google Fonts: Bitter (títulos y montos) + Work Sans (interfaz) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bitter:wght@500;600;700;800&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =========================================================
           Paleta "cuero y fogón": cuero curtido, suela, pastura, punzó.
           Las variables --cy-* se mantienen para no romper las vistas.
           ========================================================= */
        :root {
            --cy-bg: #1f1510;          /* cuero oscuro */
            --cy-bg-deep: #170f0b;
            --cy-primary: #1f1510;
            --cy-secondary: #2b1d15;
            --cy-surface: #2b1d15;      /* cuero curtido */
            --cy-surface-2: #37261b;    /* cuero con uso */
            --cy-accent: #c0492f;       /* punzó */
            --cy-accent-hover: #a33c25;
            --cy-gold: #d49a52;         /* suela */
            --cy-gold-light: #ecc58f;
            --cy-grass: #9bb35f;        /* pastura */
            --cy-sky: #8fbcd4;          /* cielo de la pampa */
            --cy-thistle: #cfa0c6;      /* flor de cardo */
            --cy-text: #f2e6d2;         /* hueso */
            --cy-text-muted: #cdb99c;
            --cy-text-faint: #a8927a;
            --cy-success: #9bb35f;
            --cy-warning: #e0a33a;
            --cy-danger: #d0553a;
            --cy-border: rgba(236, 197, 143, 0.12);
            --cy-border-strong: rgba(212, 154, 82, 0.35);
            --cy-stitch: rgba(212, 154, 82, 0.45);
            --cy-sidebar-w: 260px;
            --cy-font-display: 'Bitter', Georgia, serif;
            --cy-font-ui: 'Work Sans', system-ui, sans-serif;

            --bs-body-font-family: var(--cy-font-ui);
            --bs-body-color: var(--cy-text);
            --bs-body-bg: var(--cy-bg);
            --bs-border-color: var(--cy-border);
            --bs-link-color: var(--cy-gold);
            --bs-link-hover-color: var(--cy-gold-light);
            --bs-link-color-rgb: 212, 154, 82;
            --bs-link-hover-color-rgb: 236, 197, 143;
        }

        body {
            font-family: var(--cy-font-ui);
            background-color: var(--cy-bg);
            /* grano de cuero muy sutil */
            background-image:
                radial-gradient(ellipse at 100% 0%, rgba(212, 154, 82, 0.07), transparent 55%),
                radial-gradient(ellipse at 0% 100%, rgba(192, 73, 47, 0.05), transparent 50%);
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--cy-text);
        }

        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6,
        .kpi-value, .cy-display {
            font-family: var(--cy-font-display);
        }

        .text-muted { color: var(--cy-text-muted) !important; }
        .text-light { color: var(--cy-text) !important; }
        .text-white { color: #fbf4e8 !important; }
        .text-warning { color: var(--cy-warning) !important; }
        .text-success { color: var(--cy-grass) !important; }
        .text-danger { color: var(--cy-danger) !important; }
        .text-info { color: var(--cy-sky) !important; }

        :focus-visible {
            outline: 2px solid var(--cy-gold-light);
            outline-offset: 2px;
        }

        /* ===== SHELL: barra lateral + contenido ===== */
        .cy-shell { min-height: 100vh; }

        @media (min-width: 992px) {
            .cy-shell {
                display: grid;
                grid-template-columns: var(--cy-sidebar-w) minmax(0, 1fr);
            }
        }

        /* ===== BARRA LATERAL ===== */
        .cy-sidebar {
            background: linear-gradient(180deg, #2a1b12 0%, #221610 100%);
            color: var(--cy-text);
        }

        /* La costura: puntada de talabartero a lo largo del borde */
        .cy-sidebar::after {
            content: '';
            position: absolute;
            top: 10px;
            bottom: 10px;
            right: 7px;
            border-right: 2px dashed var(--cy-stitch);
            pointer-events: none;
        }

        @media (min-width: 992px) {
            .cy-sidebar {
                position: sticky;
                top: 0;
                height: 100vh;
                border-right: 1px solid rgba(0, 0, 0, 0.4);
                box-shadow: inset -1px 0 0 rgba(236, 197, 143, 0.06);
            }
            .cy-sidebar .offcanvas-body {
                height: 100%;
            }
        }

        @media (max-width: 991.98px) {
            .cy-sidebar.offcanvas-lg {
                --bs-offcanvas-width: 290px;
                --bs-offcanvas-bg: #251810;
                --bs-offcanvas-color: var(--cy-text);
            }
        }

        .cy-sidebar .offcanvas-body {
            display: flex;
            flex-direction: column;
            padding: 1.25rem 1.1rem 1rem 0.9rem;
            overflow-y: auto;
        }

        .cy-brand {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            text-decoration: none;
            padding: 0 0.4rem 1.1rem;
            margin-bottom: 0.9rem;
            border-bottom: 1px solid var(--cy-border);
        }

        .cy-brand img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--cy-gold);
            flex-shrink: 0;
        }

        .cy-brand-name {
            font-family: var(--cy-font-display);
            font-weight: 800;
            font-size: 1.18rem;
            line-height: 1.05;
            color: var(--cy-gold-light);
            letter-spacing: -0.2px;
        }

        .cy-brand-sub {
            display: block;
            font-family: var(--cy-font-ui);
            font-weight: 500;
            font-size: 0.74rem;
            color: var(--cy-text-faint);
            letter-spacing: 0;
            margin-top: 2px;
        }

        .cy-sell-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin: 0 0.1rem 1.1rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            background: var(--cy-gold);
            color: #22150c;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            box-shadow: 0 2px 0 #9a6a30;
        }

        .cy-sell-btn:hover { background: var(--cy-gold-light); color: #22150c; }
        .cy-sell-btn:active { transform: translateY(1px); box-shadow: 0 1px 0 #9a6a30; }

        .cy-nav-group + .cy-nav-group { margin-top: 1.1rem; }

        .cy-nav-heading {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--cy-text-faint);
            padding: 0 0.75rem;
            margin-bottom: 0.3rem;
        }

        .cy-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .cy-nav a {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.55rem 0.75rem;
            border-radius: 8px;
            color: var(--cy-text-muted);
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            position: relative;
        }

        .cy-nav a i {
            font-size: 1.05rem;
            width: 1.2rem;
            text-align: center;
            color: var(--cy-text-faint);
        }

        .cy-nav a:hover {
            background: rgba(236, 197, 143, 0.06);
            color: var(--cy-text);
        }

        .cy-nav a.active {
            background: rgba(212, 154, 82, 0.14);
            color: var(--cy-gold-light);
            font-weight: 600;
        }

        .cy-nav a.active i { color: var(--cy-gold); }

        .cy-nav a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 8px;
            border-left: 3px solid var(--cy-gold);
            border-radius: 2px;
        }

        .cy-sidebar-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid var(--cy-border);
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .cy-user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--cy-text);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cy-user-role {
            font-size: 0.76rem;
            color: var(--cy-text-faint);
            text-transform: capitalize;
        }

        .cy-logout {
            margin-left: auto;
            background: transparent;
            border: 1px solid var(--cy-border);
            color: var(--cy-text-muted);
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .cy-logout:hover { color: var(--cy-danger); border-color: rgba(208, 85, 58, 0.5); }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--cy-gold);
            color: #22150c;
            font-family: var(--cy-font-display);
            font-weight: 800;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        /* ===== BARRA SUPERIOR (solo celular/tablet) ===== */
        .cy-topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 1rem;
            padding-top: calc(0.6rem + env(safe-area-inset-top));
            background: rgba(31, 21, 16, 0.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--cy-border);
        }

        .cy-topbar .cy-brand {
            padding: 0;
            margin: 0;
            border: 0;
            gap: 0.55rem;
        }

        .cy-topbar .cy-brand img { width: 34px; height: 34px; }
        .cy-topbar .cy-brand-name { font-size: 1.05rem; }

        .cy-icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            border: 1px solid var(--cy-border-strong);
            background: rgba(212, 154, 82, 0.1);
            color: var(--cy-gold-light);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            padding: 0;
        }

        .cy-user-btn {
            background: transparent;
            border: 0;
            padding: 0;
            border-radius: 50%;
        }

        .dropdown-menu {
            --bs-dropdown-bg: #2e1f16;
            --bs-dropdown-color: var(--cy-text);
            --bs-dropdown-border-color: var(--cy-border-strong);
            --bs-dropdown-link-color: var(--cy-text);
            --bs-dropdown-link-hover-bg: rgba(212, 154, 82, 0.12);
            --bs-dropdown-link-hover-color: var(--cy-gold-light);
            --bs-dropdown-divider-bg: var(--cy-border);
            border-radius: 12px;
        }

        /* ===== TABS INFERIORES DEL VENDEDOR (celular) ===== */
        .cy-tabbar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1030;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            align-items: end;
            background: #251810;
            border-top: 1px solid var(--cy-border-strong);
            padding: 0.35rem 0.5rem calc(0.35rem + env(safe-area-inset-bottom));
        }

        .cy-tab {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            padding: 0.35rem 0;
            color: var(--cy-text-faint);
            text-decoration: none;
            font-size: 0.74rem;
            font-weight: 600;
            min-height: 52px;
            justify-content: center;
        }

        .cy-tab i { font-size: 1.3rem; line-height: 1; }
        .cy-tab.active { color: var(--cy-gold-light); }
        .cy-tab.active i { color: var(--cy-gold); }

        .cy-tab-sell .cy-tab-sell-disc {
            width: 58px;
            height: 58px;
            margin-top: -26px;
            border-radius: 50%;
            background: var(--cy-gold);
            color: #22150c;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #251810;
            box-shadow: 0 0 0 1px var(--cy-border-strong);
        }

        .cy-tab-sell .cy-tab-sell-disc i { font-size: 1.6rem; color: #22150c; }
        .cy-tab-sell { color: var(--cy-gold-light); }

        body.has-tabbar .main-content {
            padding-bottom: calc(6rem + env(safe-area-inset-bottom));
        }

        @media (min-width: 992px) {
            .cy-tabbar { display: none; }
            body.has-tabbar .main-content { padding-bottom: 3rem; }
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            padding: 1.75rem 0 3rem;
        }

        .main-content > .container {
            max-width: 1320px;
        }

        @media (min-width: 992px) {
            .main-content > .container {
                padding-left: 2rem;
                padding-right: 2rem;
            }
        }

        /* ===== CARDS ===== */
        .card-glass {
            background: var(--cy-surface);
            border: 1px solid var(--cy-border);
            border-radius: 14px;
            box-shadow: 0 1px 0 rgba(236, 197, 143, 0.04) inset, 0 6px 18px rgba(0, 0, 0, 0.22);
            color: var(--cy-text);
        }

        .card-glass .card-body {
            padding: 1.5rem;
        }

        /* ===== KPI CARDS ===== */
        .kpi-card {
            background: var(--cy-surface);
            border: 1px solid var(--cy-border);
            border-radius: 14px;
            padding: 1.4rem;
            position: relative;
            overflow: hidden;
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
        }

        .kpi-card.kpi-blue::before { background: var(--cy-sky); }
        .kpi-card.kpi-green::before { background: var(--cy-grass); }
        .kpi-card.kpi-gold::before { background: var(--cy-gold); }
        .kpi-card.kpi-red::before { background: var(--cy-accent); }

        .kpi-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1rem;
        }

        .kpi-blue .kpi-icon { background: rgba(143, 188, 212, 0.14); color: var(--cy-sky); }
        .kpi-green .kpi-icon { background: rgba(155, 179, 95, 0.16); color: var(--cy-grass); }
        .kpi-gold .kpi-icon { background: rgba(212, 154, 82, 0.16); color: var(--cy-gold); }
        .kpi-red .kpi-icon { background: rgba(192, 73, 47, 0.16); color: #e2765c; }

        .kpi-value {
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            line-height: 1.05;
            margin-bottom: 0.3rem;
            font-variant-numeric: tabular-nums;
        }

        .kpi-label {
            font-size: 0.84rem;
            color: var(--cy-text-muted);
            font-weight: 500;
        }

        /* ===== TABLES ===== */
        .table-dark-custom {
            --bs-table-bg: transparent;
            --bs-table-color: var(--cy-text);
            --bs-table-border-color: var(--cy-border);
            --bs-table-striped-bg: rgba(236, 197, 143, 0.025);
            --bs-table-striped-color: var(--cy-text);
            --bs-table-hover-bg: rgba(212, 154, 82, 0.07);
            --bs-table-hover-color: var(--cy-text);
            font-size: 0.9rem;
            font-variant-numeric: tabular-nums;
        }

        .table-dark-custom thead th {
            background: rgba(23, 15, 11, 0.55);
            border-bottom: 1px solid var(--cy-border-strong);
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--cy-gold-light);
            padding: 0.85rem 1rem;
        }

        .table-dark-custom td {
            padding: 0.8rem 1rem;
            vertical-align: middle;
        }

        /* ===== BUTTONS ===== */
        .btn-accent {
            background: var(--cy-accent);
            border: none;
            color: #fff5ea;
            font-weight: 600;
            padding: 0.55rem 1.3rem;
            border-radius: 10px;
            font-size: 0.9rem;
            box-shadow: 0 2px 0 #7a2c1a;
        }

        .btn-accent:hover,
        .btn-accent:focus {
            background: var(--cy-accent-hover);
            color: #fff5ea;
        }

        .btn-gold {
            background: var(--cy-gold);
            border: none;
            color: #22150c;
            font-weight: 600;
            padding: 0.55rem 1.3rem;
            border-radius: 10px;
            font-size: 0.9rem;
            box-shadow: 0 2px 0 #9a6a30;
        }

        .btn-gold:hover,
        .btn-gold:focus {
            background: var(--cy-gold-light);
            color: #22150c;
        }

        .btn-accent:active,
        .btn-gold:active { transform: translateY(1px); }

        .btn-glass {
            background: rgba(236, 197, 143, 0.06);
            border: 1px solid var(--cy-border-strong);
            color: var(--cy-text);
            padding: 0.45rem 1rem;
            border-radius: 9px;
            font-size: 0.86rem;
        }

        .btn-glass:hover,
        .btn-glass:focus {
            background: rgba(236, 197, 143, 0.12);
            color: var(--cy-gold-light);
            border-color: var(--cy-gold);
        }

        .btn-outline-light {
            --bs-btn-color: var(--cy-text);
            --bs-btn-border-color: var(--cy-border-strong);
            --bs-btn-hover-bg: rgba(236, 197, 143, 0.12);
            --bs-btn-hover-color: var(--cy-gold-light);
            --bs-btn-hover-border-color: var(--cy-gold);
        }

        /* ===== BADGES ===== */
        .badge-stock-ok,
        .badge-stock-bajo,
        .badge-stock-critico {
            font-weight: 600;
            padding: 0.35rem 0.7rem;
            border-radius: 7px;
            font-size: 0.78rem;
        }

        .badge-stock-ok { background: rgba(155, 179, 95, 0.16); color: #b9cf80; }
        .badge-stock-bajo { background: rgba(224, 163, 58, 0.16); color: #ecbb62; }
        .badge-stock-critico {
            background: rgba(208, 85, 58, 0.18);
            color: #ea8469;
            animation: pulse-danger 1.8s ease-in-out infinite;
        }

        @keyframes pulse-danger {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.65; }
        }

        /* ===== FORMS ===== */
        .form-control-dark,
        .form-select-dark {
            background-color: rgba(23, 15, 11, 0.7);
            border: 1px solid var(--cy-border-strong);
            color: var(--cy-text);
            border-radius: 10px;
            padding: 0.62rem 1rem;
            font-size: 0.95rem;
        }

        .form-select-dark {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ecc58f' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        }

        .form-select-dark option {
            background-color: #2b1d15;
            color: var(--cy-text);
        }

        .form-control-dark:focus,
        .form-select-dark:focus {
            background-color: rgba(23, 15, 11, 0.9);
            border-color: var(--cy-gold);
            color: var(--cy-text);
            box-shadow: 0 0 0 3px rgba(212, 154, 82, 0.2);
        }

        .form-control-dark::placeholder { color: var(--cy-text-faint); opacity: 0.8; }

        input[type=number].form-control-dark::-webkit-inner-spin-button,
        input[type=number].form-control-dark::-webkit-outer-spin-button {
            opacity: 0.5;
        }

        .input-group-text {
            background-color: rgba(23, 15, 11, 0.85) !important;
            border-color: var(--cy-border-strong) !important;
            color: var(--cy-gold);
        }

        .form-check-input:checked {
            background-color: var(--cy-gold);
            border-color: var(--cy-gold);
        }

        .bg-gold-light {
            background: rgba(212, 154, 82, 0.2) !important;
            color: var(--cy-gold-light) !important;
            border: 1px solid var(--cy-border-strong);
        }

        .text-gold-light { color: var(--cy-gold-light) !important; }

        .form-label {
            font-weight: 500;
            font-size: 0.87rem;
            color: var(--cy-text-muted);
            margin-bottom: 0.4rem;
        }

        /* ===== MODALS ===== */
        .modal-content {
            --bs-modal-bg: #2b1d15;
            --bs-modal-color: var(--cy-text);
            --bs-modal-border-color: var(--cy-border-strong);
        }

        /* ===== ALERTS ===== */
        .alert-custom-success,
        .alert-custom-error,
        .alert-custom-warning {
            border-radius: 12px;
            font-size: 0.92rem;
            border-left-width: 4px;
        }

        .alert-custom-success { background: rgba(155, 179, 95, 0.12); border: 1px solid rgba(155, 179, 95, 0.35); border-left: 4px solid var(--cy-grass); color: #c3d690; }
        .alert-custom-error { background: rgba(208, 85, 58, 0.12); border: 1px solid rgba(208, 85, 58, 0.35); border-left: 4px solid var(--cy-danger); color: #f0a08a; }
        .alert-custom-warning { background: rgba(224, 163, 58, 0.12); border: 1px solid rgba(224, 163, 58, 0.35); border-left: 4px solid var(--cy-warning); color: #f0c47a; }

        /* ===== PAGE HEADER ===== */
        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: #fbf4e8;
        }

        .page-header .breadcrumb { font-size: 0.84rem; }

        .page-header .breadcrumb-item a {
            color: var(--cy-text-muted);
            text-decoration: none;
        }

        .page-header .breadcrumb-item a:hover { color: var(--cy-gold); }
        .page-header .breadcrumb-item.active { color: var(--cy-gold-light); }
        .breadcrumb-item + .breadcrumb-item::before { color: var(--cy-text-faint); }

        /* ===== PAGINATION ===== */
        .pagination .page-link {
            background: var(--cy-surface);
            border: 1px solid var(--cy-border);
            color: var(--cy-text-muted);
            font-size: 0.86rem;
            border-radius: 8px !important;
            margin: 0 2px;
        }

        .pagination .page-link:hover {
            background: rgba(212, 154, 82, 0.15);
            border-color: var(--cy-border-strong);
            color: var(--cy-gold);
        }

        .pagination .page-item.active .page-link {
            background: var(--cy-gold);
            border-color: var(--cy-gold);
            color: #22150c;
            font-weight: 600;
        }

        .pagination .page-item.disabled .page-link {
            background: rgba(23, 15, 11, 0.4);
            border-color: var(--cy-border);
            color: rgba(205, 185, 156, 0.35);
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
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: var(--cy-bg-deep); }
        ::-webkit-scrollbar-thumb { background: rgba(212, 154, 82, 0.35); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(212, 154, 82, 0.55); }

        /* ===== ANIMATIONS ===== */
        .fade-in { animation: fadeIn 0.3s ease-out; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation: none !important;
                transition: none !important;
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .kpi-value { font-size: 1.45rem; }
            .main-content { padding: 1.1rem 0 2rem; }
            .page-header h1 { font-size: 1.4rem; }
        }
    </style>
    @stack('styles')
</head>
@php
    $esVendedor = Auth::check() && ! Auth::user()->isAdmin();
@endphp
<body class="{{ $esVendedor ? 'has-tabbar' : '' }}">
    @auth
    <div class="cy-shell">
        {{-- ===== Barra lateral (fija en escritorio, panel deslizable en celular) ===== --}}
        <aside class="cy-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="cySidebar" aria-label="Menú principal">
            <div class="offcanvas-body">
                <div class="d-flex align-items-start">
                    <a class="cy-brand flex-grow-1" href="{{ route('dashboard') }}">
                        <img src="/images/logo.jpeg" alt="">
                        <span class="cy-brand-name">
                            Casa Yacobone
                            <span class="cy-brand-sub">Ropa y talabartería de campo</span>
                        </span>
                    </a>
                    <button type="button" class="btn-close btn-close-white d-lg-none mt-2" data-bs-dismiss="offcanvas" data-bs-target="#cySidebar" aria-label="Cerrar menú"></button>
                </div>

                @if($esVendedor)
                <a href="{{ route('ventas.create') }}" class="cy-sell-btn">
                    <i class="bi bi-plus-lg"></i>Nueva venta
                </a>
                @endif

                <nav>
                    <div class="cy-nav-group">
                        <ul class="cy-nav">
                            <li>
                                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                    <i class="bi bi-house-door"></i>Inicio
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('ventas.index') }}" class="{{ request()->routeIs('ventas.index', 'ventas.show') ? 'active' : '' }}">
                                    <i class="bi bi-receipt"></i>Ventas
                                </a>
                            </li>
                            @if(Auth::user()->isAdmin())
                            <li>
                                <a href="{{ route('productos.index') }}" class="{{ request()->routeIs('productos.*') ? 'active' : '' }}">
                                    <i class="bi bi-box-seam"></i>Productos
                                </a>
                            </li>
                            @endif
                            <li>
                                <a href="{{ route('caja.index') }}" class="{{ request()->routeIs('caja.*') ? 'active' : '' }}">
                                    <i class="bi bi-safe2"></i>Caja
                                </a>
                            </li>
                        </ul>
                    </div>

                    @if(Auth::user()->isAdmin())
                    <div class="cy-nav-group">
                        <div class="cy-nav-heading">Reportes</div>
                        <ul class="cy-nav">
                            <li>
                                <a href="{{ route('reportes.diario') }}" class="{{ request()->routeIs('reportes.diario') ? 'active' : '' }}">
                                    <i class="bi bi-calendar-day"></i>Reporte diario
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reportes.rendimiento') }}" class="{{ request()->routeIs('reportes.rendimiento') ? 'active' : '' }}">
                                    <i class="bi bi-bar-chart-line"></i>Rendimiento
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reportes.cajas') }}" class="{{ request()->routeIs('reportes.cajas') ? 'active' : '' }}">
                                    <i class="bi bi-journal-text"></i>Historial de cajas
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="cy-nav-group">
                        <div class="cy-nav-heading">Administración</div>
                        <ul class="cy-nav">
                            <li>
                                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <i class="bi bi-people"></i>Usuarios
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('proveedores.index') }}" class="{{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                                    <i class="bi bi-truck"></i>Proveedores
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('categorias.index') }}" class="{{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                                    <i class="bi bi-tags"></i>Categorías
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('categoria-gastos.index') }}" class="{{ request()->routeIs('categoria-gastos.*') ? 'active' : '' }}">
                                    <i class="bi bi-wallet2"></i>Categorías de gasto
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </nav>

                <div class="cy-sidebar-footer">
                    <span class="user-avatar">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                    <div class="min-w-0" style="min-width: 0;">
                        <div class="cy-user-name">{{ Auth::user()->name }}</div>
                        <div class="cy-user-role">{{ Auth::user()->role }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="ms-auto">
                        @csrf
                        <button type="submit" class="cy-logout" title="Cerrar sesión" aria-label="Cerrar sesión">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="cy-main">
            {{-- ===== Barra superior: solo en celular / tablet ===== --}}
            <header class="cy-topbar d-lg-none">
                @unless($esVendedor)
                <button class="cy-icon-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#cySidebar" aria-controls="cySidebar" aria-label="Abrir menú">
                    <i class="bi bi-list"></i>
                </button>
                @endunless
                <a class="cy-brand" href="{{ route('dashboard') }}">
                    <img src="/images/logo.jpeg" alt="">
                    <span class="cy-brand-name">Casa Yacobone</span>
                </a>
                @unless($esVendedor)
                <div class="dropdown ms-auto">
                    <button class="cy-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menú de usuario">
                        <span class="user-avatar">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="min-width: 220px;">
                        <li class="px-3 pt-2 pb-1">
                            <div class="fw-semibold" style="font-size: 0.92rem;">{{ Auth::user()->name }}</div>
                            <div class="cy-user-role">{{ Auth::user()->role }}</div>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2" style="color: #ea8469; font-weight: 500;">
                                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endunless
            </header>
    @endauth

            {{-- Content --}}
            <main class="main-content">
                <div class="container">
                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="alert alert-custom-success alert-dismissible fade show fade-in" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-custom-error alert-dismissible fade show fade-in" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-custom-error alert-dismissible fade show fade-in" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Revisá estos datos:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
    @auth
        </div>
    </div>

    {{-- ===== Tabs inferiores del vendedor (celular) ===== --}}
    @if($esVendedor)
    <nav class="cy-tabbar" aria-label="Navegación rápida">
        <a href="{{ route('dashboard') }}" class="cy-tab {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door{{ request()->routeIs('dashboard') ? '-fill' : '' }}"></i>Inicio
        </a>
        <a href="{{ route('ventas.index') }}" class="cy-tab {{ request()->routeIs('ventas.index', 'ventas.show') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i>Ventas
        </a>
        <a href="{{ route('ventas.create') }}" class="cy-tab cy-tab-sell {{ request()->routeIs('ventas.create') ? 'active' : '' }}">
            <span class="cy-tab-sell-disc"><i class="bi bi-plus-lg"></i></span>Vender
        </a>
        <a href="{{ route('caja.index') }}" class="cy-tab {{ request()->routeIs('caja.*') ? 'active' : '' }}">
            <i class="bi bi-safe2{{ request()->routeIs('caja.*') ? '-fill' : '' }}"></i>Caja
        </a>
        <div class="dropup">
            <button type="button" class="cy-tab w-100 border-0 bg-transparent" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i>Cuenta
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg mb-2" style="min-width: 220px;">
                <li class="px-3 pt-2 pb-1">
                    <div class="fw-semibold" style="font-size: 0.92rem;">{{ Auth::user()->name }}</div>
                    <div class="cy-user-role">{{ Auth::user()->role }}</div>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item py-2" style="color: #ea8469; font-weight: 500;">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    @endif
    @endauth

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- PWA: banner de instalación + registro del Service Worker (ver public/js/pwa-install.js) --}}
    <script src="{{ asset('js/pwa-install.js') }}" defer></script>
    @include('partials.pwa-install')
    @stack('scripts')
</body>
</html>
