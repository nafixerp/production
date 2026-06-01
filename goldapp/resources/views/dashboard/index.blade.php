@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Today's Sales</div>
            <div class="stat-value">₹{{ number_format($todaySales,2) }}</div>
            <div class="stat-icon"><i class="bi bi-bag-check"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Today's Purchase</div>
            <div class="stat-value">₹{{ number_format($todayPurchases,2) }}</div>
            <div class="stat-icon"><i class="bi bi-cart"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Today's Receipt</div>
            <div class="stat-value">₹{{ number_format($todayReceipts,2) }}</div>
            <div class="stat-icon"><i class="bi bi-arrow-down-circle"></i></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label">Cash Balance</div>
            <div class="stat-value">₹{{ number_format($cashBalance,2) }}</div>
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card-gold">
            <div class="card-header-gold">
                <h5>◆ Recent Sales Bills</h5>
                <a href="{{ route('sales.create') }}" class="btn-outline-gold btn-sm-gold">+ New Bill</a>
            </div>
            @if($recentSales->isEmpty())
                <p class="text-center" style="color:var(--text-muted-gold); padding:20px 0;">No sales bills yet.</p>
            @else
            <table class="table-gold w-100">
                <thead>
                    <tr>
                        <th>Bill No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSales as $s)
                    <tr>
                        <td><a href="{{ route('sales.show',$s->id) }}" class="text-gold">{{ $s->billno }}</a></td>
                        <td>{{ $s->billdate }}</td>
                        <td>{{ Str::limit($s->customer_name,18) }}</td>
                        <td class="text-end">₹{{ number_format($s->net_amount,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-gold">
            <div class="card-header-gold">
                <h5>◆ Recent Purchases</h5>
                <a href="{{ route('purchase.create') }}" class="btn-outline-gold btn-sm-gold">+ New Bill</a>
            </div>
            @if($recentPurchases->isEmpty())
                <p class="text-center" style="color:var(--text-muted-gold); padding:20px 0;">No purchase bills yet.</p>
            @else
            <table class="table-gold w-100">
                <thead>
                    <tr>
                        <th>Doc No</th>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPurchases as $p)
                    <tr>
                        <td><a href="{{ route('purchase.show',$p->id) }}" class="text-gold">{{ $p->docno }}</a></td>
                        <td>{{ $p->billdate }}</td>
                        <td>{{ Str::limit($p->supplier_name,18) }}</td>
                        <td class="text-end">₹{{ number_format($p->net_amount,2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-md-6">
        <div class="card-gold">
            <div class="card-header-gold">
                <h5>◆ Quick Actions</h5>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('sales.create') }}"    class="btn-outline-gold"><i class="bi bi-bag-plus me-1"></i>New Sale</a>
                <a href="{{ route('purchase.create') }}" class="btn-outline-gold"><i class="bi bi-cart-plus me-1"></i>New Purchase</a>
                <a href="{{ route('receipt.create') }}"  class="btn-outline-gold"><i class="bi bi-arrow-down-circle me-1"></i>Receipt</a>
                <a href="{{ route('payment.create') }}"  class="btn-outline-gold"><i class="bi bi-arrow-up-circle me-1"></i>Payment</a>
                <a href="{{ route('accounts.create') }}" class="btn-outline-gold"><i class="bi bi-person-plus me-1"></i>New Account</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-gold">
            <div class="card-header-gold">
                <h5>◆ Summary</h5>
            </div>
            <table class="table-gold w-100">
                <tbody>
                    <tr>
                        <td>Total Sales (All Time)</td>
                        <td class="text-end text-credit">₹{{ number_format($totalSales,2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Purchase (All Time)</td>
                        <td class="text-end text-debit">₹{{ number_format($totalPurchases,2) }}</td>
                    </tr>
                    <tr>
                        <td>Today's Payments Out</td>
                        <td class="text-end text-debit">₹{{ number_format($todayPayments,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
