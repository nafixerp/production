@extends('layouts.app')
@section('title','Profit & Loss Statement')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Profit & Loss Statement</h5>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Print</button>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3"><label class="form-label text-small">From</label><input type="date" name="from" class="form-control form-control-sm" value="{{ $fromDate }}"></div>
        <div class="col-md-3"><label class="form-label text-small">To</label><input type="date" name="to" class="form-control form-control-sm" value="{{ $toDate }}"></div>
        <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Generate</button></div>
    </form>
</div>

<div class="pl-report">
    <!-- INCOME SECTION -->
    <div class="pl-section">
        <div class="pl-section-title income-bg">INCOME</div>

        <div class="pl-subsection-title">Sales Revenue</div>
        @foreach($salesAccounts as $acc)
        <div class="pl-row"><span>{{ $acc->name }}</span><span class="text-success">{{ number_format($acc->total,2) }}</span></div>
        @endforeach
        <div class="pl-subtotal"><span>Total Sales</span><span class="text-success fw-bold">₹{{ number_format($totalSales,2) }}</span></div>

        @if($otherIncomeAccounts->count() > 0)
        <div class="pl-subsection-title">Other Income</div>
        @foreach($otherIncomeAccounts as $acc)
        <div class="pl-row"><span>{{ $acc->name }}</span><span class="text-success">{{ number_format($acc->total,2) }}</span></div>
        @endforeach
        <div class="pl-subtotal"><span>Total Other Income</span><span class="text-success fw-bold">₹{{ number_format($totalOtherIncome,2) }}</span></div>
        @endif
    </div>

    <!-- COST OF GOODS SOLD -->
    <div class="pl-section">
        <div class="pl-section-title expense-bg">COST OF GOODS SOLD</div>
        @foreach($cogsAccounts as $acc)
        <div class="pl-row"><span>{{ $acc->name }}</span><span class="text-danger">₹{{ number_format(abs($acc->total),2) }}</span></div>
        @endforeach
        <div class="pl-subtotal"><span>Total COGS</span><span class="text-danger fw-bold">₹{{ number_format($totalCOGS,2) }}</span></div>
    </div>

    <!-- GROSS PROFIT -->
    <div class="pl-total {{ $grossProfit >= 0 ? 'profit' : 'loss' }}">
        <span>GROSS PROFIT {{ $grossProfit < 0 ? '(LOSS)' : '' }}</span>
        <span class="{{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}">₹{{ number_format(abs($grossProfit),2) }}</span>
    </div>

    <!-- OPERATING EXPENSES -->
    @if($otherExpenseAccounts->count() > 0)
    <div class="pl-section">
        <div class="pl-section-title expense-bg">OPERATING EXPENSES</div>
        @foreach($otherExpenseAccounts as $acc)
        <div class="pl-row"><span>{{ $acc->name }}</span><span class="text-danger">₹{{ number_format(abs($acc->total),2) }}</span></div>
        @endforeach
        <div class="pl-subtotal"><span>Total Expenses</span><span class="text-danger fw-bold">₹{{ number_format($totalOtherExpense,2) }}</span></div>
    </div>
    @endif

    <!-- NET PROFIT -->
    <div class="pl-total {{ $netProfit >= 0 ? 'profit' : 'loss' }} net-profit">
        <span>NET PROFIT {{ $netProfit < 0 ? '(LOSS)' : '' }}</span>
        <span class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }} fw-bold fs-5">₹{{ number_format(abs($netProfit),2) }}</span>
    </div>
</div>

<style>
.pl-report{max-width:700px;margin:0 auto}
.pl-section{background:#13132a;border:1px solid var(--border-gold);border-radius:8px;margin-bottom:12px;overflow:hidden}
.pl-section-title{padding:8px 16px;font-size:.72rem;font-weight:700;letter-spacing:2px;text-transform:uppercase}
.income-bg{background:rgba(39,174,96,.15);color:#2ecc71}
.expense-bg{background:rgba(231,76,60,.15);color:#e74c3c}
.pl-subsection-title{padding:6px 16px;font-size:.72rem;color:rgba(212,175,55,.7);letter-spacing:1px;text-transform:uppercase;border-top:1px solid rgba(212,175,55,.1)}
.pl-row{display:flex;justify-content:space-between;padding:5px 16px;font-size:.83rem;border-top:1px solid rgba(255,255,255,.03)}
.pl-subtotal{display:flex;justify-content:space-between;padding:8px 16px;background:rgba(0,0,0,.2);font-size:.85rem;border-top:1px solid var(--border-gold)}
.pl-total{display:flex;justify-content:space-between;padding:14px 20px;border-radius:8px;margin-bottom:12px;font-size:1rem;font-weight:700;letter-spacing:.5px}
.profit{background:rgba(39,174,96,.12);border:2px solid rgba(39,174,96,.4)}
.loss{background:rgba(231,76,60,.12);border:2px solid rgba(231,76,60,.4)}
.net-profit{font-size:1.1rem;margin-top:8px}
.text-small{font-size:.78rem}
</style>
@endsection
