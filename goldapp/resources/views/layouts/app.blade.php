<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Food Production ERP') | Food Production ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --gold: #d4af37;
            --gold-light: #f0d060;
            --gold-dark: #a0821c;
            --bg-dark: #0d0d18;
            --bg-card: #13132a;
            --bg-sidebar: #0a0a1a;
            --bg-header: #0f0f22;
            --border-gold: rgba(212,175,55,0.35);
            --text-muted-gold: rgba(212,175,55,0.6);
        }
        * { box-sizing: border-box; }
        body {
            background: var(--bg-dark);
            color: #e8e0c8;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }
        /* ---- SIDEBAR ---- */
        #sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-gold);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s;
        }
        #sidebar .brand {
            padding: 22px 18px 16px;
            border-bottom: 1px solid var(--border-gold);
            text-align: center;
        }
        #sidebar .brand .diamond-row {
            color: var(--gold);
            font-size: 11px;
            letter-spacing: 4px;
            margin-bottom: 4px;
        }
        #sidebar .brand h4 {
            color: var(--gold);
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 3px;
            margin: 0;
            text-shadow: 0 0 12px rgba(212,175,55,0.5);
        }
        #sidebar .brand small {
            color: var(--text-muted-gold);
            font-size: 0.65rem;
            letter-spacing: 2px;
        }
        #sidebar .nav-section {
            padding: 8px 0 4px;
        }
        #sidebar .nav-label {
            color: var(--text-muted-gold);
            font-size: 0.6rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 6px 18px 2px;
        }
        #sidebar .nav-link {
            color: #b0a880;
            padding: 9px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            border-left: 3px solid transparent;
            transition: all 0.2s;
            text-decoration: none;
        }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            color: var(--gold);
            background: rgba(212,175,55,0.08);
            border-left-color: var(--gold);
        }
        #sidebar .nav-link .icon {
            font-size: 0.9rem;
            width: 18px;
            text-align: center;
        }
        #sidebar .nav-link .diamond {
            font-size: 0.7rem;
        }
        /* ---- TOPBAR ---- */
        #topbar {
            margin-left: 240px;
            background: linear-gradient(90deg, #0f0f22 0%, #1a1535 50%, #0f0f22 100%);
            border-bottom: 1px solid var(--border-gold);
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        #topbar .page-title {
            color: var(--gold);
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 1px;
        }
        #topbar .user-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        #topbar .user-info .user-name {
            color: var(--gold-light);
            font-size: 0.82rem;
        }
        #topbar .btn-logout {
            background: rgba(212,175,55,0.12);
            color: var(--gold);
            border: 1px solid var(--border-gold);
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.78rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        #topbar .btn-logout:hover {
            background: var(--gold);
            color: #000;
        }
        /* ---- MAIN CONTENT ---- */
        #main-content {
            margin-left: 240px;
            padding: 24px;
            min-height: calc(100vh - 56px);
        }
        /* ---- CARDS ---- */
        .card-gold {
            background: var(--bg-card);
            border: 1px solid var(--border-gold);
            border-radius: 8px;
            padding: 20px;
        }
        .card-gold .card-header-gold {
            border-bottom: 1px solid var(--border-gold);
            padding-bottom: 12px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-gold .card-header-gold h5 {
            color: var(--gold);
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 1px;
            margin: 0;
        }
        /* ---- STAT CARDS ---- */
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-gold);
            border-radius: 8px;
            padding: 18px 20px;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }
        .stat-card .stat-label {
            color: var(--text-muted-gold);
            font-size: 0.72rem;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .stat-card .stat-value {
            color: var(--gold-light);
            font-size: 1.5rem;
            font-weight: 700;
            margin: 4px 0 2px;
        }
        .stat-card .stat-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2rem;
            color: rgba(212,175,55,0.15);
        }
        /* ---- TABLES ---- */
        .table-gold {
            width: 100%;
            border-collapse: collapse;
        }
        .table-gold thead th {
            background: rgba(212,175,55,0.12);
            color: var(--gold);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 10px 12px;
            border-bottom: 1px solid var(--border-gold);
        }
        .table-gold tbody tr {
            border-bottom: 1px solid rgba(212,175,55,0.08);
            transition: background 0.15s;
        }
        .table-gold tbody tr:hover {
            background: rgba(212,175,55,0.05);
        }
        .table-gold tbody td {
            padding: 9px 12px;
            font-size: 0.83rem;
            color: #d0c8a8;
            vertical-align: middle;
        }
        /* ---- FORMS ---- */
        .form-label {
            color: var(--text-muted-gold);
            font-size: 0.75rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .form-control, .form-select {
            background: rgba(255,255,255,0.04) !important;
            border: 1px solid rgba(212,175,55,0.3) !important;
            color: #e0d8b8 !important;
            border-radius: 5px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--gold) !important;
            box-shadow: 0 0 0 3px rgba(212,175,55,0.12) !important;
            background: rgba(255,255,255,0.06) !important;
        }
        .form-control::placeholder { color: rgba(200,190,150,0.35) !important; }
        /* ---- BUTTONS ---- */
        .btn-gold {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold));
            color: #000;
            font-weight: 600;
            font-size: 0.82rem;
            letter-spacing: 0.5px;
            border: none;
            padding: 8px 18px;
            border-radius: 5px;
            transition: all 0.2s;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            color: #000;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212,175,55,0.35);
        }
        .btn-outline-gold {
            background: transparent;
            color: var(--gold);
            border: 1px solid var(--border-gold);
            font-size: 0.82rem;
            padding: 7px 16px;
            border-radius: 5px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-outline-gold:hover {
            background: rgba(212,175,55,0.1);
            color: var(--gold-light);
            border-color: var(--gold);
        }
        .btn-sm-gold {
            font-size: 0.72rem;
            padding: 4px 10px;
        }
        /* ---- BADGES ---- */
        .badge-gold {
            background: rgba(212,175,55,0.15);
            color: var(--gold);
            border: 1px solid rgba(212,175,55,0.3);
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 4px;
        }
        /* ---- ALERTS ---- */
        .alert-gold-success {
            background: rgba(0,200,100,0.08);
            border: 1px solid rgba(0,200,100,0.3);
            color: #5dde90;
            border-radius: 6px;
            padding: 10px 16px;
            font-size: 0.85rem;
        }
        .alert-gold-error {
            background: rgba(220,50,50,0.1);
            border: 1px solid rgba(220,50,50,0.35);
            color: #f87171;
            border-radius: 6px;
            padding: 10px 16px;
            font-size: 0.85rem;
        }
        /* ---- PAGINATION ---- */
        .pagination .page-link {
            background: var(--bg-card);
            border-color: var(--border-gold);
            color: var(--gold);
            font-size: 0.8rem;
        }
        .pagination .page-item.active .page-link {
            background: var(--gold);
            border-color: var(--gold);
            color: #000;
        }
        /* ---- DEBIT/CREDIT COLORS ---- */
        .text-debit  { color: #f87171; }
        .text-credit { color: #4ade80; }
        /* ---- MISC ---- */
        .divider-gold { border-color: var(--border-gold); }
        .text-gold { color: var(--gold); }
        .section-title {
            color: var(--gold);
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 2px;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-gold);
        }
        /* ---- Line Items Table ---- */
        .items-table th { background: rgba(212,175,55,0.08); color: var(--gold); font-size: 0.72rem; text-transform: uppercase; padding: 7px 8px; }
        .items-table td { padding: 5px 8px; }
        .items-table input { font-size: 0.82rem; padding: 4px 8px; }
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #topbar, #main-content { margin-left: 0; }
            #sidebar.open { transform: translateX(0); }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="brand">
        <div class="diamond-row">◇ ✦ ◇</div>
        <h4>FOOD ERP</h4>
        <small>FOOD PRODUCTION ERP SYSTEM</small>
    </div>
    <div style="overflow-y:auto; flex:1; padding-bottom:20px;">

        <div class="nav-section">
            <div class="nav-label">Main</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="diamond">◇</span> <span>Dashboard</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Masters</div>
            <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                <span class="diamond">◇</span> <span>Accounts</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Sales</div>
            <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <span class="diamond">◇</span> <span>Sales Invoices</span>
            </a>
            <a href="{{ route('receipts.index') }}" class="nav-link {{ request()->routeIs('receipts.*') ? 'active' : '' }}">
                <span class="diamond">◇</span> <span>Receipts</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Purchase</div>
            <a href="{{ route('purchase.index') }}" class="nav-link {{ request()->routeIs('purchase.*') ? 'active' : '' }}">
                <span class="diamond">◇</span> <span>Purchase Invoices</span>
            </a>
            <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                <span class="diamond">◇</span> <span>Payments</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-label">Accounts</div>
            <a href="{{ route('daybook.index') }}" class="nav-link {{ request()->routeIs('daybook.index') ? 'active' : '' }}">
                <span class="diamond">◇</span> <span>Daybook</span>
            </a>
            <a href="{{ route('daybook.ledger') }}" class="nav-link {{ request()->routeIs('daybook.ledger') ? 'active' : '' }}">
                <span class="diamond">◇</span> <span>Ledger</span>
            </a>
            <a href="{{ route('daybook.trial') }}" class="nav-link {{ request()->routeIs('daybook.trial') ? 'active' : '' }}">
                <span class="diamond">◇</span> <span>Trial Balance</span>
            </a>
        </div>
    </div>
</nav>

<!-- TOPBAR -->
<div id="topbar">
    <div class="page-title">◆ @yield('page-title', 'Dashboard')</div>
    <div class="user-info">
        <span class="user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Logout
            </button>
        </form>
    </div>
</div>

<!-- MAIN CONTENT -->
<div id="main-content">
    @if(session('success'))
        <div class="alert-gold-success mb-3"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-gold-error mb-3"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-gold-error mb-3">
            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
