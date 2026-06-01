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
        :root{--gold:#d4af37;--gold-light:#f0d060;--gold-dark:#a0821c;--bg-dark:#0d0d18;--bg-card:#13132a;--bg-sidebar:#0a0a1a;--bg-header:#0f0f22;--border-gold:rgba(212,175,55,0.35);--text-muted-gold:rgba(212,175,55,0.6)}
        *{box-sizing:border-box}
        body{background:var(--bg-dark);color:#e8e0c8;font-family:'Segoe UI',sans-serif;min-height:100vh;overflow-x:hidden}
        #sidebar{width:240px;min-height:100vh;background:var(--bg-sidebar);border-right:1px solid var(--border-gold);position:fixed;top:0;left:0;z-index:1000;display:flex;flex-direction:column;transition:transform .3s}
        #sidebar .brand{padding:18px 16px 14px;border-bottom:1px solid var(--border-gold);text-align:center}
        #sidebar .brand .diamond-row{color:var(--gold);font-size:10px;letter-spacing:4px;margin-bottom:3px}
        #sidebar .brand h4{color:var(--gold);font-size:1.1rem;font-weight:700;letter-spacing:3px;margin:0;text-shadow:0 0 12px rgba(212,175,55,.5)}
        #sidebar .brand small{color:var(--text-muted-gold);font-size:.6rem;letter-spacing:2px}
        #sidebar .nav-scroll{overflow-y:auto;flex:1;padding-bottom:20px}
        .nav-group-btn{background:none;border:none;width:100%;text-align:left;color:var(--text-muted-gold);font-size:.63rem;letter-spacing:2.5px;text-transform:uppercase;padding:10px 16px 4px;display:flex;align-items:center;justify-content:space-between;cursor:pointer;transition:color .2s}
        .nav-group-btn:hover{color:var(--gold)}
        .nav-group-btn .arrow{font-size:.6rem;transition:transform .2s}
        .nav-group-btn[aria-expanded="true"] .arrow{transform:rotate(90deg)}
        .nav-sub .nav-link{color:#a09878;padding:7px 16px 7px 28px;display:flex;align-items:center;gap:8px;font-size:.8rem;border-left:2px solid transparent;transition:all .18s;text-decoration:none}
        .nav-sub .nav-link:hover,.nav-sub .nav-link.active{color:var(--gold);background:rgba(212,175,55,.06);border-left-color:var(--gold)}
        .nav-sub .nav-link .dot{width:4px;height:4px;border-radius:50%;background:currentColor;flex-shrink:0}
        .dash-link{color:#b0a880;padding:10px 16px;display:flex;align-items:center;gap:10px;font-size:.85rem;border-left:3px solid transparent;transition:all .2s;text-decoration:none}
        .dash-link:hover,.dash-link.active{color:var(--gold);background:rgba(212,175,55,.08);border-left-color:var(--gold)}
        #topbar{margin-left:240px;background:linear-gradient(90deg,#0f0f22 0%,#1a1535 50%,#0f0f22 100%);border-bottom:1px solid var(--border-gold);padding:0 20px;height:52px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:999}
        #topbar .page-title{color:var(--gold);font-size:.82rem;font-weight:600;letter-spacing:1px;white-space:nowrap}
        #topbar .user-info{display:flex;align-items:center;gap:12px;flex-shrink:0}
        #topbar .user-name{color:var(--gold-light);font-size:.8rem}
        /* Top Dropdown Nav */
        .top-nav{display:flex;align-items:center;gap:2px;margin:0 18px}
        .top-nav .tn-item{position:relative}
        .top-nav .tn-btn{background:none;border:none;color:#b0a878;font-size:.78rem;font-weight:600;letter-spacing:.8px;padding:6px 13px;border-radius:5px;cursor:pointer;display:flex;align-items:center;gap:5px;transition:all .18s;white-space:nowrap;text-transform:uppercase}
        .top-nav .tn-btn:hover,.top-nav .tn-btn.open{color:var(--gold);background:rgba(212,175,55,.1)}
        .top-nav .tn-btn .tn-arrow{font-size:.55rem;transition:transform .2s}
        .top-nav .tn-btn.open .tn-arrow{transform:rotate(180deg)}
        .top-nav .tn-dropdown{display:none;position:absolute;top:calc(100% + 4px);left:0;min-width:200px;background:#0e0e24;border:1px solid var(--border-gold);border-radius:7px;box-shadow:0 8px 32px rgba(0,0,0,.6);z-index:2000;padding:6px 0}
        .top-nav .tn-dropdown.multi-col{min-width:560px;display:none;flex-wrap:wrap}
        .top-nav .tn-dropdown.show{display:block}
        .top-nav .tn-dropdown.multi-col.show{display:flex}
        .top-nav .tn-col{min-width:180px;flex:1}
        .top-nav .tn-col-title{color:var(--text-muted-gold);font-size:.6rem;letter-spacing:2px;text-transform:uppercase;padding:8px 14px 4px;border-bottom:1px solid rgba(212,175,55,.1);margin-bottom:2px}
        .top-nav .tn-link{display:block;color:#a09878;font-size:.78rem;padding:6px 14px;text-decoration:none;transition:all .15s;white-space:nowrap}
        .top-nav .tn-link:hover{color:var(--gold);background:rgba(212,175,55,.07);padding-left:18px}
        .btn-logout{background:rgba(212,175,55,.12);color:var(--gold);border:1px solid var(--border-gold);padding:4px 12px;border-radius:4px;font-size:.76rem;cursor:pointer;text-decoration:none;transition:all .2s}
        .btn-logout:hover{background:var(--gold);color:#000}
        #main-content{margin-left:240px;padding:22px;min-height:calc(100vh - 52px)}
        .card-gold{background:var(--bg-card);border:1px solid var(--border-gold);border-radius:8px;padding:20px}
        .card-gold .card-header-gold{border-bottom:1px solid var(--border-gold);padding-bottom:12px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between}
        .card-gold .card-header-gold h5{color:var(--gold);font-size:.92rem;font-weight:600;letter-spacing:1px;margin:0}
        .stat-card{background:var(--bg-card);border:1px solid var(--border-gold);border-radius:8px;padding:18px 20px;position:relative;overflow:hidden}
        .stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,transparent,var(--gold),transparent)}
        .stat-card .stat-label{color:var(--text-muted-gold);font-size:.7rem;letter-spacing:2px;text-transform:uppercase}
        .stat-card .stat-value{color:var(--gold-light);font-size:1.45rem;font-weight:700;margin:4px 0 2px}
        .stat-card .stat-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:2rem;color:rgba(212,175,55,.15)}
        .table-gold{width:100%;border-collapse:collapse}
        .table-gold thead th{background:rgba(212,175,55,.12);color:var(--gold);font-size:.73rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;padding:10px 12px;border-bottom:1px solid var(--border-gold)}
        .table-gold tbody tr{border-bottom:1px solid rgba(212,175,55,.07);transition:background .15s}
        .table-gold tbody tr:hover{background:rgba(212,175,55,.05)}
        .table-gold tbody td{padding:9px 12px;font-size:.82rem;color:#d0c8a8;vertical-align:middle}
        .form-label{color:var(--text-muted-gold);font-size:.73rem;letter-spacing:1px;text-transform:uppercase;margin-bottom:4px}
        .form-control,.form-select{background:rgba(255,255,255,.04)!important;border:1px solid rgba(212,175,55,.3)!important;color:#e0d8b8!important;border-radius:5px}
        .form-control:focus,.form-select:focus{border-color:var(--gold)!important;box-shadow:0 0 0 3px rgba(212,175,55,.12)!important;background:rgba(255,255,255,.06)!important}
        .form-control::placeholder{color:rgba(200,190,150,.35)!important}
        .btn-gold{background:linear-gradient(135deg,var(--gold-dark),var(--gold));color:#000;font-weight:600;font-size:.82rem;border:none;padding:8px 18px;border-radius:5px;transition:all .2s;cursor:pointer}
        .btn-gold:hover{background:linear-gradient(135deg,var(--gold),var(--gold-light));color:#000;transform:translateY(-1px);box-shadow:0 4px 12px rgba(212,175,55,.35)}
        .btn-outline-gold{background:transparent;color:var(--gold);border:1px solid var(--border-gold);font-size:.82rem;padding:7px 16px;border-radius:5px;transition:all .2s;text-decoration:none;display:inline-flex;align-items:center;gap:5px;cursor:pointer}
        .btn-outline-gold:hover{background:rgba(212,175,55,.1);color:var(--gold-light);border-color:var(--gold)}
        .btn-sm-gold{font-size:.72rem;padding:4px 10px}
        .badge-gold{background:rgba(212,175,55,.15);color:var(--gold);border:1px solid rgba(212,175,55,.3);font-size:.68rem;padding:3px 8px;border-radius:4px}
        .alert-gold-success{background:rgba(0,200,100,.08);border:1px solid rgba(0,200,100,.3);color:#5dde90;border-radius:6px;padding:10px 16px;font-size:.85rem}
        .alert-gold-error{background:rgba(220,50,50,.1);border:1px solid rgba(220,50,50,.35);color:#f87171;border-radius:6px;padding:10px 16px;font-size:.85rem}
        .pagination .page-link{background:var(--bg-card);border-color:var(--border-gold);color:var(--gold);font-size:.8rem}
        .pagination .page-item.active .page-link{background:var(--gold);border-color:var(--gold);color:#000}
        .text-debit{color:#f87171}.text-credit{color:#4ade80}.text-gold{color:var(--gold)}
        .section-title{color:var(--gold);font-size:.95rem;font-weight:600;letter-spacing:2px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border-gold)}
        .items-table th{background:rgba(212,175,55,.08);color:var(--gold);font-size:.7rem;text-transform:uppercase;padding:7px 8px}
        .items-table td{padding:5px 8px}
        .items-table input,.items-table select{font-size:.8rem;padding:3px 7px}
        @media(max-width:768px){#sidebar{transform:translateX(-100%)}#topbar,#main-content{margin-left:0}#sidebar.open{transform:translateX(0)}}
    </style>
    @stack('styles')
</head>
<body>

<nav id="sidebar">
    <div class="brand">
        <div class="diamond-row">◇ ✦ ◇</div>
        <h4>FOOD ERP</h4>
        <small>FOOD PRODUCTION ERP SYSTEM</small>
    </div>
    <div class="nav-scroll">

        <a href="{{ route('dashboard') }}" class="dash-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
        </a>

        {{-- MASTERS --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-masters" aria-expanded="{{ request()->routeIs('companies.*','branches.*','warehouses.*','factories.*','departments.*','staff.*','vendors.*','customers.*','distributors.*','routes.*','vehicles.*','units.*','taxes.*','hsn-sac.*','raw-materials.*','packing-materials.*','finished-goods.*','semi-finished-goods.*','recipes.*','recipe-versions.*','ingredient-substitutions.*','nutritional-info.*','allergens.*','fssai.*') ? 'true' : 'false' }}">
            <span><i class="bi bi-grid-3x3-gap me-1"></i> Masters</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub {{ request()->routeIs('companies.*','branches.*','warehouses.*','factories.*','departments.*','staff.*','vendors.*','customers.*','distributors.*','routes.*','vehicles.*','units.*','taxes.*','hsn-sac.*','raw-materials.*','packing-materials.*','finished-goods.*','semi-finished-goods.*','recipes.*','allergens.*','fssai.*') ? 'show' : '' }}" id="menu-masters">
            <a href="{{ route('companies.index') }}" class="nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}"><span class="dot"></span> Company</a>
            <a href="{{ route('branches.index') }}" class="nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}"><span class="dot"></span> Branch</a>
            <a href="{{ route('warehouses.index') }}" class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}"><span class="dot"></span> Warehouse</a>
            <a href="{{ route('factories.index') }}" class="nav-link {{ request()->routeIs('factories.*') ? 'active' : '' }}"><span class="dot"></span> Factory</a>
            <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}"><span class="dot"></span> Department</a>
            <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}"><span class="dot"></span> Staff</a>
            <a href="{{ route('vendors.index') }}" class="nav-link {{ request()->routeIs('vendors.*') ? 'active' : '' }}"><span class="dot"></span> Vendor/Supplier</a>
            <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}"><span class="dot"></span> Customer</a>
            <a href="{{ route('distributors.index') }}" class="nav-link {{ request()->routeIs('distributors.*') ? 'active' : '' }}"><span class="dot"></span> Distributor</a>
            <a href="{{ route('routes.index') }}" class="nav-link {{ request()->routeIs('routes.*') ? 'active' : '' }}"><span class="dot"></span> Route</a>
            <a href="{{ route('vehicles.index') }}" class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}"><span class="dot"></span> Vehicle</a>
            <a href="{{ route('units.index') }}" class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}"><span class="dot"></span> Unit</a>
            <a href="{{ route('taxes.index') }}" class="nav-link {{ request()->routeIs('taxes.*') ? 'active' : '' }}"><span class="dot"></span> Tax/GST</a>
            <a href="{{ route('hsn-sac.index') }}" class="nav-link {{ request()->routeIs('hsn-sac.*') ? 'active' : '' }}"><span class="dot"></span> HSN/SAC</a>
            <a href="{{ route('raw-materials.index') }}" class="nav-link {{ request()->routeIs('raw-materials.*') ? 'active' : '' }}"><span class="dot"></span> Raw Material</a>
            <a href="{{ route('packing-materials.index') }}" class="nav-link {{ request()->routeIs('packing-materials.*') ? 'active' : '' }}"><span class="dot"></span> Packing Material</a>
            <a href="{{ route('finished-goods.index') }}" class="nav-link {{ request()->routeIs('finished-goods.*') ? 'active' : '' }}"><span class="dot"></span> Finished Goods</a>
            <a href="{{ route('semi-finished-goods.index') }}" class="nav-link {{ request()->routeIs('semi-finished-goods.*') ? 'active' : '' }}"><span class="dot"></span> Semi Finished</a>
            <a href="{{ route('recipes.index') }}" class="nav-link {{ request()->routeIs('recipes.*') ? 'active' : '' }}"><span class="dot"></span> Recipe/BOM</a>
            <a href="{{ route('recipe-versions.index') }}" class="nav-link {{ request()->routeIs('recipe-versions.*') ? 'active' : '' }}"><span class="dot"></span> Recipe Versioning</a>
            <a href="{{ route('ingredient-substitutions.index') }}" class="nav-link {{ request()->routeIs('ingredient-substitutions.*') ? 'active' : '' }}"><span class="dot"></span> Ingredient Sub.</a>
            <a href="{{ route('nutritional-info.index') }}" class="nav-link {{ request()->routeIs('nutritional-info.*') ? 'active' : '' }}"><span class="dot"></span> Nutritional Info</a>
            <a href="{{ route('allergens.index') }}" class="nav-link {{ request()->routeIs('allergens.*') ? 'active' : '' }}"><span class="dot"></span> Allergen</a>
            <a href="{{ route('fssai.index') }}" class="nav-link {{ request()->routeIs('fssai.*') ? 'active' : '' }}"><span class="dot"></span> FSSAI</a>
        </div>

        {{-- PURCHASE --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-purchase" aria-expanded="{{ request()->routeIs('purchase-*','grn.*','new-grn.*','new-purchase-*','rm-qc.*','vendor-*','contract-*','suppliers.*') ? 'true' : 'false' }}">
            <span><i class="bi bi-cart3 me-1"></i> Purchase</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub {{ request()->routeIs('purchase-*','grn.*','new-grn.*','new-purchase-*','rm-qc.*','vendor-*','contract-*','suppliers.*') ? 'show' : '' }}" id="menu-purchase">
            <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"><span class="dot"></span> Suppliers</a>
            <a href="{{ route('purchase-requisitions.index') }}" class="nav-link"><span class="dot"></span> Requisition</a>
            <a href="{{ route('purchase-rfq.index') }}" class="nav-link"><span class="dot"></span> RFQ</a>
            <a href="{{ route('new-purchase-orders.index') }}" class="nav-link {{ request()->routeIs('new-purchase-orders.*') ? 'active' : '' }}"><span class="dot"></span> Purchase Order</a>
            <a href="{{ route('new-grn.index') }}" class="nav-link {{ request()->routeIs('new-grn.*') ? 'active' : '' }}"><span class="dot"></span> GRN</a>
            <a href="{{ route('rm-qc.index') }}" class="nav-link"><span class="dot"></span> RM QC</a>
            <a href="{{ route('new-purchase-invoices.index') }}" class="nav-link {{ request()->routeIs('new-purchase-invoices.*') ? 'active' : '' }}"><span class="dot"></span> Purchase Invoice</a>
            <a href="{{ route('purchase-returns.index') }}" class="nav-link"><span class="dot"></span> Purchase Return</a>
            <a href="{{ route('vendor-payments.index') }}" class="nav-link"><span class="dot"></span> Vendor Payment</a>
            <a href="{{ route('vendor-ledger.index') }}" class="nav-link"><span class="dot"></span> Vendor Ledger</a>
            <a href="{{ route('vendor-ratings.index') }}" class="nav-link"><span class="dot"></span> Vendor Rating</a>
            <a href="{{ route('contract-pricing.index') }}" class="nav-link"><span class="dot"></span> Contract Pricing</a>
        </div>

        {{-- INVENTORY --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-inventory" aria-expanded="{{ request()->routeIs('rm-inventory','batch-*','expiry-*','fifo-*','cold-*','stock-*','damage-*','reorder') ? 'true' : 'false' }}">
            <span><i class="bi bi-boxes me-1"></i> Inventory</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub" id="menu-inventory">
            <a href="{{ route('rm-inventory.index') }}" class="nav-link"><span class="dot"></span> RM Inventory</a>
            <a href="{{ route('batch-tracking.index') }}" class="nav-link"><span class="dot"></span> Batch/Lot</a>
            <a href="{{ route('expiry-tracking.index') }}" class="nav-link"><span class="dot"></span> Expiry Tracking</a>
            <a href="{{ route('fifo-fefo.index') }}" class="nav-link"><span class="dot"></span> FIFO/FEFO</a>
            <a href="{{ route('cold-storage.index') }}" class="nav-link"><span class="dot"></span> Cold Storage</a>
            <a href="{{ route('stock-ledger.index') }}" class="nav-link"><span class="dot"></span> Stock Ledger</a>
            <a href="{{ route('stock-transfer.index') }}" class="nav-link"><span class="dot"></span> Stock Transfer</a>
            <a href="{{ route('stock-adjustment.index') }}" class="nav-link"><span class="dot"></span> Adjustment</a>
            <a href="{{ route('stock-verification.index') }}" class="nav-link"><span class="dot"></span> Stock Verify</a>
            <a href="{{ route('damage-writeoff.index') }}" class="nav-link"><span class="dot"></span> Damage/Expiry</a>
            <a href="{{ route('reorder.index') }}" class="nav-link"><span class="dot"></span> Reorder</a>
        </div>

        {{-- PRODUCTION --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-production" aria-expanded="{{ request()->routeIs('production-*','material-*','wip','preparation.*','cooking.*','cooling.*','packing-stage.*','fg-receipt.*','yield-*','byproduct.*','rework.*','machines.*','machine-*','downtime.*','maintenance.*') ? 'true' : 'false' }}">
            <span><i class="bi bi-gear me-1"></i> Production</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub" id="menu-production">
            <a href="{{ route('production-plans.index') }}" class="nav-link"><span class="dot"></span> Planning</a>
            <a href="{{ route('production-orders.index') }}" class="nav-link"><span class="dot"></span> Prod. Order</a>
            <a href="{{ route('material-issues.index') }}" class="nav-link"><span class="dot"></span> Material Issue</a>
            <a href="{{ route('wip.index') }}" class="nav-link"><span class="dot"></span> WIP</a>
            <a href="{{ route('preparation.index') }}" class="nav-link"><span class="dot"></span> Preparation</a>
            <a href="{{ route('cooking.index') }}" class="nav-link"><span class="dot"></span> Cooking</a>
            <a href="{{ route('cooling.index') }}" class="nav-link"><span class="dot"></span> Cooling</a>
            <a href="{{ route('packing-stage.index') }}" class="nav-link"><span class="dot"></span> Packing</a>
            <a href="{{ route('fg-receipt.index') }}" class="nav-link"><span class="dot"></span> FG Receipt</a>
            <a href="{{ route('yield-wastage.index') }}" class="nav-link"><span class="dot"></span> Yield/Wastage</a>
            <a href="{{ route('byproduct.index') }}" class="nav-link"><span class="dot"></span> By-product</a>
            <a href="{{ route('rework.index') }}" class="nav-link"><span class="dot"></span> Rework</a>
            <a href="{{ route('production-costing.index') }}" class="nav-link"><span class="dot"></span> Costing</a>
            <a href="{{ route('machines.index') }}" class="nav-link"><span class="dot"></span> Machine</a>
            <a href="{{ route('machine-utilization.index') }}" class="nav-link"><span class="dot"></span> Utilization</a>
            <a href="{{ route('downtime.index') }}" class="nav-link"><span class="dot"></span> Downtime</a>
            <a href="{{ route('maintenance.index') }}" class="nav-link"><span class="dot"></span> Maintenance</a>
        </div>

        {{-- QUALITY --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-quality">
            <span><i class="bi bi-shield-check me-1"></i> Quality</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub" id="menu-quality">
            <a href="{{ route('quality-control.index') }}" class="nav-link"><span class="dot"></span> QC Dashboard</a>
            <a href="{{ route('incoming-qc.index') }}" class="nav-link"><span class="dot"></span> Incoming QC</a>
            <a href="{{ route('inprocess-qc.index') }}" class="nav-link"><span class="dot"></span> In-process QC</a>
            <a href="{{ route('final-qc.index') }}" class="nav-link"><span class="dot"></span> Final QC</a>
            <a href="{{ route('lab-tests.index') }}" class="nav-link"><span class="dot"></span> Lab Test</a>
            <a href="{{ route('sample-retention.index') }}" class="nav-link"><span class="dot"></span> Sample Retention</a>
            <a href="{{ route('haccp.index') }}" class="nav-link"><span class="dot"></span> HACCP</a>
            <a href="{{ route('batch-traceability.index') }}" class="nav-link"><span class="dot"></span> Traceability</a>
            <a href="{{ route('recalls.index') }}" class="nav-link"><span class="dot"></span> Recall</a>
        </div>

        {{-- SALES --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-sales" aria-expanded="{{ request()->routeIs('fg-warehouse','dispatch-*','sales-*','retail-*','wholesale-*','distributor-*','sales.*','credit-*','delivery-*','route-*','courier.*','fleet.*') ? 'true' : 'false' }}">
            <span><i class="bi bi-receipt me-1"></i> Sales & Delivery</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub {{ request()->routeIs('fg-warehouse','dispatch-*','sales-quotations.*','sales-orders.*','sales.*','retail-*','wholesale-*','distributor-sales.*','sales-returns.*','credit-*','delivery-*','route-*','courier.*','fleet.*') ? 'show' : '' }}" id="menu-sales">
            <a href="{{ route('fg-warehouse.index') }}" class="nav-link"><span class="dot"></span> FG Warehouse</a>
            <a href="{{ route('dispatch-plans.index') }}" class="nav-link"><span class="dot"></span> Dispatch Plan</a>
            <a href="{{ route('sales-quotations.index') }}" class="nav-link"><span class="dot"></span> Quotation</a>
            <a href="{{ route('sales-orders.index') }}" class="nav-link"><span class="dot"></span> Sales Order</a>
            <a href="{{ route('retail-pos.index') }}" class="nav-link"><span class="dot"></span> Retail POS</a>
            <a href="{{ route('wholesale-sales.index') }}" class="nav-link"><span class="dot"></span> Wholesale</a>
            <a href="{{ route('distributor-sales.index') }}" class="nav-link"><span class="dot"></span> Distributor Sales</a>
            <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}"><span class="dot"></span> Sales Invoice</a>
            <a href="{{ route('sales-returns.index') }}" class="nav-link"><span class="dot"></span> Sales Return</a>
            <a href="{{ route('credit-notes.index') }}" class="nav-link"><span class="dot"></span> Credit Note</a>
            <a href="{{ route('delivery-notes.index') }}" class="nav-link"><span class="dot"></span> Delivery Note</a>
            <a href="{{ route('route-delivery.index') }}" class="nav-link"><span class="dot"></span> Route Delivery</a>
            <a href="{{ route('courier.index') }}" class="nav-link"><span class="dot"></span> Courier</a>
            <a href="{{ route('fleet.index') }}" class="nav-link"><span class="dot"></span> Fleet</a>
        </div>

        {{-- ECOMMERCE --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-ecom">
            <span><i class="bi bi-shop me-1"></i> E-Commerce</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub" id="menu-ecom">
            <a href="{{ route('ecom-products.index') }}" class="nav-link"><span class="dot"></span> Product Catalog</a>
            <a href="{{ route('ecom-orders.index') }}" class="nav-link"><span class="dot"></span> Online Orders</a>
            <a href="{{ route('cart.index') }}" class="nav-link"><span class="dot"></span> Cart</a>
            <a href="{{ route('checkout.index') }}" class="nav-link"><span class="dot"></span> Checkout</a>
            <a href="{{ route('online-payments.index') }}" class="nav-link"><span class="dot"></span> Online Payment</a>
            <a href="{{ route('coupons.index') }}" class="nav-link"><span class="dot"></span> Coupons</a>
            <a href="{{ route('loyalty.index') }}" class="nav-link"><span class="dot"></span> Loyalty</a>
            <a href="{{ route('customer-portal.index') }}" class="nav-link"><span class="dot"></span> Customer Portal</a>
            <a href="{{ route('distributor-portal.index') }}" class="nav-link"><span class="dot"></span> Distributor Portal</a>
            <a href="{{ route('online-returns.index') }}" class="nav-link"><span class="dot"></span> Online Returns</a>
            <a href="{{ route('marketplace.index') }}" class="nav-link"><span class="dot"></span> Marketplace</a>
            <a href="{{ route('integrations.index') }}" class="nav-link"><span class="dot"></span> Integrations</a>
        </div>

        {{-- CRM --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-crm">
            <span><i class="bi bi-people me-1"></i> CRM</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub" id="menu-crm">
            <a href="{{ route('crm-customers.index') }}" class="nav-link"><span class="dot"></span> Customers</a>
            <a href="{{ route('new-crm-leads.index') }}" class="nav-link"><span class="dot"></span> Leads</a>
            <a href="{{ route('crm-activities.index') }}" class="nav-link"><span class="dot"></span> Activities</a>
            <a href="{{ route('crm-opportunities.index') }}" class="nav-link"><span class="dot"></span> Opportunities</a>
            <a href="{{ route('customer-complaints.index') }}" class="nav-link"><span class="dot"></span> Complaints</a>
            <a href="{{ route('crm-leads.index') }}" class="nav-link"><span class="dot"></span> Lead (Legacy)</a>
            <a href="{{ route('crm-followup.index') }}" class="nav-link"><span class="dot"></span> Follow-up</a>
            <a href="{{ route('complaints.index') }}" class="nav-link"><span class="dot"></span> Complaints (Old)</a>
            <a href="{{ route('feedback.index') }}" class="nav-link"><span class="dot"></span> Feedback</a>
        </div>

        {{-- ACCOUNTS --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-accounts" aria-expanded="{{ request()->routeIs('accounts.*','receipts.*','payments.*','journal-*','contra-*','debit-*','bank-*','cash-*','bank-book*','general-*','daybook*','ledger*','trial-*','profit-*','balance-*','cash-flow*','cogs-*','gst-*','gstr*','einvoice.*','eway-*') ? 'true' : 'false' }}">
            <span><i class="bi bi-journal-bookmark me-1"></i> Accounts</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub {{ request()->routeIs('accounts.*','receipts.*','payments.*','journal-*','contra-*','daybook*','ledger*','trial-*','profit-*','balance-*','gst-*','gstr*') ? 'show' : '' }}" id="menu-accounts">
            <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}"><span class="dot"></span> Chart of Accounts</a>
            <a href="{{ route('receipts.index') }}" class="nav-link {{ request()->routeIs('receipts.*') ? 'active' : '' }}"><span class="dot"></span> Receipt Voucher</a>
            <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}"><span class="dot"></span> Payment Voucher</a>
            <a href="{{ route('journal-vouchers.index') }}" class="nav-link"><span class="dot"></span> Journal Voucher</a>
            <a href="{{ route('contra-vouchers.index') }}" class="nav-link"><span class="dot"></span> Contra Voucher</a>
            <a href="{{ route('debit-credit-notes.index') }}" class="nav-link"><span class="dot"></span> Dr/Cr Note</a>
            <a href="{{ route('bank-reconciliation.index') }}" class="nav-link"><span class="dot"></span> Bank Recon.</a>
            <a href="{{ route('cash-book.index') }}" class="nav-link"><span class="dot"></span> Cash Book</a>
            <a href="{{ route('bank-book.index') }}" class="nav-link"><span class="dot"></span> Bank Book</a>
            <a href="{{ route('general-ledger.index') }}" class="nav-link"><span class="dot"></span> General Ledger</a>
            <a href="{{ route('daybook.index') }}" class="nav-link {{ request()->routeIs('daybook.index') ? 'active' : '' }}"><span class="dot"></span> Daybook</a>
            <a href="{{ route('daybook.ledger') }}" class="nav-link {{ request()->routeIs('daybook.ledger') ? 'active' : '' }}"><span class="dot"></span> Ledger</a>
            <a href="{{ route('trial-balance.index') }}" class="nav-link"><span class="dot"></span> Trial Balance</a>
            <a href="{{ route('profit-loss.index') }}" class="nav-link"><span class="dot"></span> P&amp;L</a>
            <a href="{{ route('balance-sheet.index') }}" class="nav-link"><span class="dot"></span> Balance Sheet</a>
            <a href="{{ route('cash-flow.index') }}" class="nav-link"><span class="dot"></span> Cash Flow</a>
            <a href="{{ route('cogs-posting.index') }}" class="nav-link"><span class="dot"></span> COGS Posting</a>
            <a href="{{ route('gst-purchase-register.index') }}" class="nav-link"><span class="dot"></span> GST Purchase</a>
            <a href="{{ route('gst-sales-register.index') }}" class="nav-link"><span class="dot"></span> GST Sales</a>
            <a href="{{ route('gstr1.index') }}" class="nav-link"><span class="dot"></span> GSTR-1</a>
            <a href="{{ route('gstr3b.index') }}" class="nav-link"><span class="dot"></span> GSTR-3B</a>
            <a href="{{ route('einvoice.index') }}" class="nav-link"><span class="dot"></span> E-Invoice</a>
            <a href="{{ route('eway-bill.index') }}" class="nav-link"><span class="dot"></span> E-Way Bill</a>
        </div>

        {{-- HRMS --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-hrms">
            <span><i class="bi bi-person-badge me-1"></i> HRMS</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub" id="menu-hrms">
            <a href="{{ route('payroll.index') }}" class="nav-link"><span class="dot"></span> Payroll</a>
            <a href="{{ route('attendance.index') }}" class="nav-link"><span class="dot"></span> Attendance</a>
            <a href="{{ route('leaves.index') }}" class="nav-link"><span class="dot"></span> Leave</a>
            <a href="{{ route('shifts.index') }}" class="nav-link"><span class="dot"></span> Shifts</a>
            <a href="{{ route('overtime.index') }}" class="nav-link"><span class="dot"></span> Overtime</a>
            <a href="{{ route('pf-esi-tds.index') }}" class="nav-link"><span class="dot"></span> PF/ESI/TDS</a>
            <a href="{{ route('salary-posting.index') }}" class="nav-link"><span class="dot"></span> Salary Posting</a>
        </div>

        {{-- SETTINGS --}}
        <button class="nav-group-btn" type="button" data-bs-toggle="collapse" data-bs-target="#menu-settings">
            <span><i class="bi bi-sliders me-1"></i> Settings</span><span class="arrow">▶</span>
        </button>
        <div class="collapse nav-sub" id="menu-settings">
            <a href="{{ route('assets.index') }}" class="nav-link"><span class="dot"></span> Assets</a>
            <a href="{{ route('budgets.index') }}" class="nav-link"><span class="dot"></span> Budgeting</a>
            <a href="{{ route('approval-workflow.index') }}" class="nav-link"><span class="dot"></span> Approval Workflow</a>
            <a href="{{ route('notifications.index') }}" class="nav-link"><span class="dot"></span> Notifications</a>
            <a href="{{ route('comm-logs.index') }}" class="nav-link"><span class="dot"></span> SMS/Email Logs</a>
            <a href="{{ route('documents.index') }}" class="nav-link"><span class="dot"></span> Document Vault</a>
            <a href="{{ route('barcode.index') }}" class="nav-link"><span class="dot"></span> Barcode/QR</a>
            <a href="{{ route('admin-settings.index') }}" class="nav-link"><span class="dot"></span> Admin Settings</a>
            <a href="{{ route('users.index') }}" class="nav-link"><span class="dot"></span> User/Role/Permission</a>
            <a href="{{ route('audit-logs.index') }}" class="nav-link"><span class="dot"></span> Audit Logs</a>
            <a href="{{ route('backup.index') }}" class="nav-link"><span class="dot"></span> Backup/Restore</a>
            <a href="{{ route('report-builder.index') }}" class="nav-link"><span class="dot"></span> Report Builder</a>
            <a href="{{ route('bi-dashboard.index') }}" class="nav-link"><span class="dot"></span> BI Dashboard</a>
        </div>

    </div>
</nav>

<div id="topbar">
    <div class="page-title">◆ @yield('page-title', 'Dashboard')</div>

    {{-- ── TOP DROPDOWN MENUS ── --}}
    <nav class="top-nav">

        {{-- MASTERS --}}
        <div class="tn-item">
            <button class="tn-btn" onclick="toggleTN('tn-masters')">
                <i class="bi bi-grid-3x3-gap"></i> Masters <span class="tn-arrow">▼</span>
            </button>
            <div class="tn-dropdown multi-col" id="tn-masters">
                <div class="tn-col">
                    <div class="tn-col-title">Organisation</div>
                    <a class="tn-link" href="{{ route('companies.index') }}">Company</a>
                    <a class="tn-link" href="{{ route('branches.index') }}">Branch</a>
                    <a class="tn-link" href="{{ route('warehouses.index') }}">Warehouse</a>
                    <a class="tn-link" href="{{ route('factories.index') }}">Factory</a>
                    <a class="tn-link" href="{{ route('departments.index') }}">Department</a>
                    <a class="tn-link" href="{{ route('staff.index') }}">Staff</a>
                    <a class="tn-link" href="{{ route('routes.index') }}">Route</a>
                    <a class="tn-link" href="{{ route('vehicles.index') }}">Vehicle</a>
                </div>
                <div class="tn-col">
                    <div class="tn-col-title">Parties</div>
                    <a class="tn-link" href="{{ route('suppliers.index') }}">Suppliers</a>
                    <a class="tn-link" href="{{ route('vendors.index') }}">Vendor/Supplier</a>
                    <a class="tn-link" href="{{ route('crm-customers.index') }}">Customers (CRM)</a>
                    <a class="tn-link" href="{{ route('customers.index') }}">Customer Master</a>
                    <a class="tn-link" href="{{ route('distributors.index') }}">Distributor</a>
                    <div class="tn-col-title" style="margin-top:8px">Config</div>
                    <a class="tn-link" href="{{ route('units.index') }}">Unit</a>
                    <a class="tn-link" href="{{ route('taxes.index') }}">Tax / GST</a>
                    <a class="tn-link" href="{{ route('hsn-sac.index') }}">HSN / SAC</a>
                    <a class="tn-link" href="{{ route('accounts.index') }}">Chart of Accounts</a>
                    <div class="tn-col-title" style="margin-top:8px">Settings</div>
                    <a class="tn-link" href="{{ route('financial-years.index') }}">Financial Year</a>
                    <a class="tn-link" href="{{ route('sequences.index') }}">Sequence Config</a>
                </div>
                <div class="tn-col">
                    <div class="tn-col-title">Items</div>
                    <a class="tn-link" href="{{ route('raw-materials.index') }}">Raw Material</a>
                    <a class="tn-link" href="{{ route('packing-materials.index') }}">Packing Material</a>
                    <a class="tn-link" href="{{ route('finished-goods.index') }}">Finished Goods</a>
                    <a class="tn-link" href="{{ route('fg-products.index') }}">FG Products</a>
                    <a class="tn-link" href="{{ route('semi-finished-goods.index') }}">Semi Finished</a>
                    <a class="tn-link" href="{{ route('recipes.index') }}">Recipe / BOM</a>
                    <a class="tn-link" href="{{ route('allergens.index') }}">Allergen</a>
                    <a class="tn-link" href="{{ route('fssai.index') }}">FSSAI</a>
                    <a class="tn-link" href="{{ route('machines.index') }}">Machine</a>
                    <a class="tn-link" href="{{ route('warehouse-mgmt.index') }}">Warehouse Mgmt</a>
                </div>
            </div>
        </div>

        {{-- TRANSACTIONS --}}
        <div class="tn-item">
            <button class="tn-btn" onclick="toggleTN('tn-transactions')">
                <i class="bi bi-arrow-left-right"></i> Transactions <span class="tn-arrow">▼</span>
            </button>
            <div class="tn-dropdown multi-col" id="tn-transactions">
                <div class="tn-col">
                    <div class="tn-col-title">Purchase</div>
                    <a class="tn-link" href="{{ route('purchase-requisitions.index') }}">Requisition</a>
                    <a class="tn-link" href="{{ route('new-purchase-orders.index') }}">Purchase Order</a>
                    <a class="tn-link" href="{{ route('new-grn.index') }}">GRN</a>
                    <a class="tn-link" href="{{ route('new-purchase-invoices.index') }}">Purchase Invoice</a>
                    <a class="tn-link" href="{{ route('purchase-returns.index') }}">Purchase Return</a>
                    <a class="tn-link" href="{{ route('vendor-payments.index') }}">Vendor Payment</a>
                    <div class="tn-col-title" style="margin-top:8px">Inventory</div>
                    <a class="tn-link" href="{{ route('stock-transfer.index') }}">Stock Transfer</a>
                    <a class="tn-link" href="{{ route('stock-adjustment.index') }}">Stock Adjustment</a>
                    <a class="tn-link" href="{{ route('fg-stock.index') }}">FG Stock</a>
                    <a class="tn-link" href="{{ route('damage-writeoff.index') }}">Damage Write-off</a>
                </div>
                <div class="tn-col">
                    <div class="tn-col-title">Production</div>
                    <a class="tn-link" href="{{ route('production-plans.index') }}">Production Plan</a>
                    <a class="tn-link" href="{{ route('production-orders.index') }}">Production Order</a>
                    <a class="tn-link" href="{{ route('material-issues.index') }}">Material Issue</a>
                    <a class="tn-link" href="{{ route('fg-receipt.index') }}">FG Receipt</a>
                    <a class="tn-link" href="{{ route('yield-wastage.index') }}">Yield / Wastage</a>
                    <div class="tn-col-title" style="margin-top:8px">Sales</div>
                    <a class="tn-link" href="{{ route('sales-quotations.index') }}">Quotation</a>
                    <a class="tn-link" href="{{ route('sales-orders.index') }}">Sales Order</a>
                    <a class="tn-link" href="{{ route('sales.index') }}">Sales Invoice</a>
                    <a class="tn-link" href="{{ route('dispatch-orders.index') }}">Dispatch Order</a>
                    <a class="tn-link" href="{{ route('couriers.index') }}">Courier Shipment</a>
                    <a class="tn-link" href="{{ route('sales-returns.index') }}">Sales Return</a>
                    <a class="tn-link" href="{{ route('retail-pos.index') }}">Retail POS</a>
                    <a class="tn-link" href="{{ route('delivery-notes.index') }}">Delivery Note</a>
                </div>
                <div class="tn-col">
                    <div class="tn-col-title">Accounts</div>
                    <a class="tn-link" href="{{ route('receipts.index') }}">Receipt Voucher</a>
                    <a class="tn-link" href="{{ route('payments.index') }}">Payment Voucher</a>
                    <a class="tn-link" href="{{ route('journal-vouchers.index') }}">Journal Voucher</a>
                    <a class="tn-link" href="{{ route('contra-vouchers.index') }}">Contra Voucher</a>
                    <a class="tn-link" href="{{ route('debit-credit-notes.index') }}">Dr / Cr Note</a>
                    <a class="tn-link" href="{{ route('bank-reconciliation.index') }}">Bank Reconciliation</a>
                    <div class="tn-col-title" style="margin-top:8px">Integrations</div>
                    <a class="tn-link" href="{{ route('ecom-orders.index') }}">Ecom Orders</a>
                    <a class="tn-link" href="{{ route('payment-gateway.index') }}">Payment Txns</a>
                    <div class="tn-col-title" style="margin-top:8px">CRM</div>
                    <a class="tn-link" href="{{ route('new-crm-leads.index') }}">Leads</a>
                    <a class="tn-link" href="{{ route('crm-activities.index') }}">Activities</a>
                    <a class="tn-link" href="{{ route('crm-opportunities.index') }}">Opportunities</a>
                    <a class="tn-link" href="{{ route('customer-complaints.index') }}">Complaints</a>
                    <div class="tn-col-title" style="margin-top:8px">HR &amp; Payroll</div>
                    <a class="tn-link" href="{{ route('attendance.index') }}">Attendance</a>
                    <a class="tn-link" href="{{ route('leaves.index') }}">Leave</a>
                    <a class="tn-link" href="{{ route('payroll.index') }}">Payroll</a>
                    <a class="tn-link" href="{{ route('salary-posting.index') }}">Salary Posting</a>
                </div>
            </div>
        </div>

        {{-- REPORTS --}}
        <div class="tn-item">
            <button class="tn-btn" onclick="toggleTN('tn-reports')">
                <i class="bi bi-bar-chart-line"></i> Reports <span class="tn-arrow">▼</span>
            </button>
            <div class="tn-dropdown multi-col" id="tn-reports">
                <div class="tn-col">
                    <div class="tn-col-title">Accounts</div>
                    <a class="tn-link" href="{{ route('daybook.index') }}">Daybook</a>
                    <a class="tn-link" href="{{ route('daybook.ledger') }}">Account Ledger</a>
                    <a class="tn-link" href="{{ route('trial-balance.index') }}">Trial Balance</a>
                    <a class="tn-link" href="{{ route('profit-loss.index') }}">Profit &amp; Loss</a>
                    <a class="tn-link" href="{{ route('balance-sheet.index') }}">Balance Sheet</a>
                    <a class="tn-link" href="{{ route('cash-flow.index') }}">Cash Flow</a>
                    <a class="tn-link" href="{{ route('cash-book.index') }}">Cash Book</a>
                    <a class="tn-link" href="{{ route('bank-book.index') }}">Bank Book</a>
                    <a class="tn-link" href="{{ route('general-ledger.index') }}">General Ledger</a>
                </div>
                <div class="tn-col">
                    <div class="tn-col-title">GST</div>
                    <a class="tn-link" href="{{ route('gst-purchase-register.index') }}">GST Purchase Register</a>
                    <a class="tn-link" href="{{ route('gst-sales-register.index') }}">GST Sales Register</a>
                    <a class="tn-link" href="{{ route('gstr1.index') }}">GSTR-1</a>
                    <a class="tn-link" href="{{ route('gstr3b.index') }}">GSTR-3B</a>
                    <a class="tn-link" href="{{ route('einvoice.index') }}">E-Invoice</a>
                    <a class="tn-link" href="{{ route('eway-bill.index') }}">E-Way Bill</a>
                    <div class="tn-col-title" style="margin-top:8px">Purchase</div>
                    <a class="tn-link" href="{{ route('vendor-ledger.index') }}">Vendor Ledger</a>
                    <a class="tn-link" href="{{ route('vendor-ratings.index') }}">Vendor Rating</a>
                </div>
                <div class="tn-col">
                    <div class="tn-col-title">Inventory &amp; Production</div>
                    <a class="tn-link" href="{{ route('stock-ledger.index') }}">Stock Ledger</a>
                    <a class="tn-link" href="{{ route('expiry-tracking.index') }}">Expiry Tracking</a>
                    <a class="tn-link" href="{{ route('batch-traceability.index') }}">Batch Traceability</a>
                    <a class="tn-link" href="{{ route('production-costing.index') }}">Production Costing</a>
                    <a class="tn-link" href="{{ route('rm-inventory.index') }}">RM Inventory</a>
                    <a class="tn-link" href="{{ route('reorder.index') }}">Reorder Report</a>
                    <div class="tn-col-title" style="margin-top:8px">Analytics</div>
                    <a class="tn-link" href="{{ route('bi-dashboard.index') }}">BI Dashboard</a>
                    <a class="tn-link" href="{{ route('reports.sales') }}">Sales Analytics</a>
                    <a class="tn-link" href="{{ route('reports.inventory') }}">Inventory Report</a>
                    <a class="tn-link" href="{{ route('reports.ageing') }}">AR Ageing</a>
                    <a class="tn-link" href="{{ route('reports.pl') }}">P&amp;L Report</a>
                    <a class="tn-link" href="{{ route('report-builder.index') }}">Report Builder</a>
                    <a class="tn-link" href="{{ route('cogs-posting.index') }}">COGS Posting</a>
                </div>
            </div>
        </div>

    </nav>

    <div class="user-info">
        <span class="user-name">{{ Auth::user()->name ?? 'Admin' }}</span>
        <form method="POST" action="{{ route('logout') }}" style="margin:0">
            @csrf
            <button type="submit" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Logout</button>
        </form>
    </div>
</div>

<div id="main-content">
    @if(session('success'))
        <div class="alert-gold-success mb-3"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-gold-error mb-3"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-gold-error mb-3">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleTN(id) {
    const el = document.getElementById(id);
    const btn = el.previousElementSibling;
    const isOpen = el.classList.contains('show');
    // close all
    document.querySelectorAll('.tn-dropdown').forEach(d => d.classList.remove('show'));
    document.querySelectorAll('.tn-btn').forEach(b => b.classList.remove('open'));
    if (!isOpen) {
        el.classList.add('show');
        btn.classList.add('open');
    }
}
// close on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('.tn-item')) {
        document.querySelectorAll('.tn-dropdown').forEach(d => d.classList.remove('show'));
        document.querySelectorAll('.tn-btn').forEach(b => b.classList.remove('open'));
    }
});
</script>
@stack('scripts')
</body>
</html>
