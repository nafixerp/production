@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Today's Sales</div>
            <div class="stat-value">₹{{ number_format($todaySalesAmount, 2) }}</div>
            <div class="stat-sub">{{ $todaySalesCount }} invoice(s)</div>
            <div class="stat-icon"><i class="bi bi-bag-check"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Today's Purchase</div>
            <div class="stat-value">₹{{ number_format($todayPurchaseAmount, 2) }}</div>
            <div class="stat-sub">{{ $todayPurchaseCount }} invoice(s)</div>
            <div class="stat-icon"><i class="bi bi-cart"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Today's Receipts</div>
            <div class="stat-value text-credit">₹{{ number_format($todayReceiptsAmount, 2) }}</div>
            <div class="stat-icon"><i class="bi bi-arrow-down-circle"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Today's Payments</div>
            <div class="stat-value text-debit">₹{{ number_format($todayPaymentsAmount, 2) }}</div>
            <div class="stat-icon"><i class="bi bi-arrow-up-circle"></i></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="border-color:rgba(74,222,128,0.4)">
            <div class="stat-label">Cash Balance</div>
            <div class="stat-value" style="color:{{ $cashBalance >= 0 ? '#4ade80' : '#f87171' }}">₹{{ number_format(abs($cashBalance), 2) }}</div>
            <div class="stat-sub">{{ $cashBalance >= 0 ? 'Debit (In Hand)' : 'Credit (Negative)' }}</div>
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card-gold">
            <div class="card-header-gold">
                <h6>◆ Recent Sales Invoices</h6>
                <a href="{{ route('sales.index') }}" class="btn-outline-gold btn-sm-gold">View All</a>
            </div>
            <table class="table-gold w-100">
                <thead><tr><th>Invoice No</th><th>Date</th><th>Customer</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                @forelse($recentSales as $s)
                <tr>
                    <td><a href="{{ route('sales.show', $s) }}" style="color:var(--gold)">{{ $s->invoice_no }}</a></td>
                    <td>{{ $s->invoice_date }}</td>
                    <td>{{ $s->customer_name }}</td>
                    <td class="text-end">₹{{ number_format($s->net_amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center" style="color:#666;padding:12px">No sales yet today.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-gold">
            <div class="card-header-gold">
                <h6>◆ Recent Purchase Invoices</h6>
                <a href="{{ route('purchase.index') }}" class="btn-outline-gold btn-sm-gold">View All</a>
            </div>
            <table class="table-gold w-100">
                <thead><tr><th>Doc No</th><th>Date</th><th>Supplier</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
                @forelse($recentPurchases as $p)
                <tr>
                    <td><a href="{{ route('purchase.show', $p) }}" style="color:var(--gold)">{{ $p->doc_no }}</a></td>
                    <td>{{ $p->invoice_date }}</td>
                    <td>{{ $p->supplier_name }}</td>
                    <td class="text-end">₹{{ number_format($p->net_amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center" style="color:#666;padding:12px">No purchases yet today.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
