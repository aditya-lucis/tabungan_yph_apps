<link href="{{ URL::asset('assets/master/lib/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/lib/typicons.font/typicons.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/css/datatables/dataTables.bs4.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/css/datatables/dataTables.bs4-custom.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/css/datatables/buttons.bs.css') }}" rel="stylesheet">
<link href="{{ URL::asset('assets/master/css/azia.css') }}" rel="stylesheet">

<style>
    :root {
        --yph-primary: #1e40af;
        --yph-primary-gradient: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
        --yph-accent-green: #10b981;
        --yph-accent-red: #ef4444;
        --yph-bg: #f8fafc;
        --yph-card: #ffffff;
        --yph-text-dark: #0f172a;
        --yph-text-muted: #64748b;
        --yph-border: #e2e8f0;
        --yph-radius: 16px;
        --yph-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
        --yph-shadow-hover: 0 10px 25px -3px rgba(30, 64, 175, 0.12), 0 4px 10px -2px rgba(30, 64, 175, 0.08);
    }

    html, body {
        width: 100% !important;
        max-width: 100vw !important;
        overflow-x: clip !important;
        position: relative;
    }

    body {
        font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
        background-color: var(--yph-bg) !important;
        color: var(--yph-text-dark) !important;
        -webkit-font-smoothing: antialiased;
        padding-bottom: 70px !important; /* Ruang untuk bottom nav mobile */
    }

    @media (min-width: 992px) {
        body {
            padding-bottom: 0 !important;
        }
    }

    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("{{ URL::asset('assets/auth/img/thumb.jpg') }}");
        background-size: 600px;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        opacity: 0.04;
        z-index: -1;
    }

    /* Header Sticky (Ikut melayang di atas saat scroll ke bawah) */
    .az-header {
        position: -webkit-sticky !important;
        position: sticky !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        background: rgba(255, 255, 255, 0.96) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-bottom: 1px solid var(--yph-border) !important;
        box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05) !important;
        z-index: 1030 !important;
    }

    /* Container Spacing & Strict Viewport Boundaries for Mobile */
    .az-header,
    .az-content,
    .az-content-body,
    .container,
    .container-fluid {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    /* Flex children default to min-width: auto. min-width: 0 prevents wide table children from blowing out page width */
    .az-content {
        min-width: 0 !important;
        overflow-x: hidden !important;
        padding-top: 15px !important;
        padding-bottom: 25px !important;
    }

    .az-content-body {
        min-width: 0 !important;
        max-width: 100% !important;
        overflow-x: hidden !important;
    }

    .row, .col, [class*="col-"] {
        min-width: 0 !important;
    }

    @media (max-width: 768px) {
        .container {
            padding-left: 14px !important;
            padding-right: 14px !important;
        }
        .az-content-body {
            padding-left: 0 !important;
        }
    }

    /* Responsive Table & Card Wrappers: Only table scrolls internally, never the body */
    .table-card,
    .table-container-card,
    .fintech-card,
    .filter-card {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
    }

    .table-responsive {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        display: block !important;
    }

    div.dt-container,
    div.dataTables_wrapper {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    div.dt-container .dt-layout-table,
    div.dt-container .dt-scroll,
    div.dt-container .dt-scroll-body,
    div.dataTables_scroll,
    div.dataTables_scrollBody {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        box-sizing: border-box !important;
    }

    @media (max-width: 767.98px) {
        div.dt-container .dt-layout-row {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 8px !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        div.dt-container .dt-search {
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }
        div.dt-container .dt-search input {
            flex: 1 !important;
            margin-left: 8px !important;
            max-width: 100% !important;
        }
    }

    /* Bottom Navigation Bar (Mobile Only) */
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 64px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-top: 1px solid var(--yph-border);
        display: flex;
        align-items: center;
        justify-content: space-around;
        z-index: 1040;
        box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.04);
    }

    .mobile-bottom-nav a {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        text-decoration: none;
        font-size: 11px;
        font-weight: 600;
        padding: 6px 12px;
        transition: all 0.2s ease;
        border-radius: 12px;
    }

    .mobile-bottom-nav a i {
        font-size: 22px;
        line-height: 1;
        margin-bottom: 3px;
        transition: transform 0.2s ease;
    }

    .mobile-bottom-nav a.active {
        color: var(--yph-primary);
    }

    .mobile-bottom-nav a.active i {
        transform: translateY(-2px);
    }

    /* Modern Fintech Cards */
    .fintech-card {
        background: var(--yph-card);
        border-radius: var(--yph-radius);
        border: 1px solid var(--yph-border);
        box-shadow: var(--yph-shadow);
        padding: 20px;
        margin-bottom: 18px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    @media (hover: hover) {
        .fintech-card:hover {
            box-shadow: var(--yph-shadow-hover);
        }
    }

    .fintech-balance-hero {
        background: var(--yph-primary-gradient);
        color: white;
        border-radius: var(--yph-radius);
        padding: 24px;
        box-shadow: 0 10px 25px -4px rgba(37, 99, 235, 0.35);
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .fintech-balance-hero::after {
        content: "";
        position: absolute;
        top: -40px;
        right: -40px;
        width: 140px;
        height: 140px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        pointer-events: none;
    }

    .fintech-balance-amount {
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.2;
    }

    @media (min-width: 768px) {
        .fintech-balance-amount {
            font-size: 34px;
        }
    }

    /* Modern Pill Badges */
    .badge-fintech {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-fintech-success {
        background: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .badge-fintech-warning {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .badge-fintech-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .badge-fintech-primary {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }

    /* Touch-friendly Action Buttons */
    .btn-fintech-primary {
        background: var(--yph-primary);
        color: white !important;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        padding: 12px 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 46px;
        box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);
        transition: all 0.2s ease;
    }
    .btn-fintech-primary:hover, .btn-fintech-primary:focus {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(30, 64, 175, 0.3);
    }
    .btn-fintech-secondary {
        background: #f1f5f9;
        color: #334155 !important;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 44px;
        transition: all 0.2s ease;
    }
    .btn-fintech-secondary:hover {
        background: #e2e8f0;
    }

    /* Modal Mobile Polish */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 10px !important;
        }
        .modal-content {
            border-radius: 20px !important;
            border: none !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2) !important;
    /* Modern Clean Footer */
    .app-footer {
        width: 100%;
        background: #ffffff;
        border-top: 1px solid var(--yph-border);
        margin-top: auto;
        padding: 18px 0;
        z-index: 10;
        position: relative;
    }

    @media (max-width: 991.98px) {
        .app-footer {
            padding-bottom: 85px; /* Ruang aman agar tidak tertutup mobile bottom nav */
        }
    }
</style>


@yield('css')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">
