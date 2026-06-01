@extends('layouts.app')
@section('title','Payment Gateway Transactions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-credit-card me-2"></i>Payment Gateway Transactions</h5>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Total Settled</div>
            <div class="stat-value">₹{{ number_format($summary['total_success'],2) }}</div>
            <i class="bi bi-check-circle stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Pending Txns</div>
            <div class="stat-value">{{ $summary['total_pending'] }}</div>
            <i class="bi bi-hourglass stat-icon"></i>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Failed Txns</div>
            <div class="stat-value" style="color:#f87171">{{ $summary['total_failed'] }}</div>
            <i class="bi bi-x-circle stat-icon"></i>
        </div>
    </div>
</div>

<div class="card-gold mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <input type="text" name="q" class="form-control" placeholder="Search order ref / txn ID..." value="{{ $q }}">
        </div>
        <div class="col-md-2">
            <select name="gateway" class="form-select">
                <option value="">All Gateways</option>
                @foreach(['razorpay','paytm','stripe','paypal','upi'] as $gw)
                    <option value="{{ $gw }}" {{ $gateway === $gw ? 'selected' : '' }}>{{ ucfirst($gw) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach(['created','processing','success','failed','refunded'] as $s)
                    <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-gold w-100">Filter</button>
        </div>
    </form>
</div>

<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th><th>Order Ref</th><th>Gateway</th><th>Txn ID</th>
                <th>Amount</th><th>Currency</th><th>Status</th><th>Invoice</th>
                <th>Settled At</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($txns as $txn)
            <tr>
                <td>{{ $txn->id }}</td>
                <td style="font-size:.78rem">{{ $txn->order_ref }}</td>
                <td><span class="badge-gold">{{ strtoupper($txn->gateway) }}</span></td>
                <td style="font-size:.72rem;font-family:monospace">{{ substr($txn->gateway_txn_id,0,20) }}…</td>
                <td class="text-gold">₹{{ number_format($txn->amount,2) }}</td>
                <td>{{ $txn->currency }}</td>
                <td>
                    @php $sc = match($txn->status) { 'success'=>'#4ade80','failed'=>'#f87171','refunded'=>'#fb923c','processing'=>'#60a5fa', default=>'#ccc' }; @endphp
                    <span style="color:{{ $sc }};font-weight:600">{{ ucfirst($txn->status) }}</span>
                </td>
                <td>{{ $txn->sales_invoice_id ?? '—' }}</td>
                <td style="font-size:.75rem">{{ $txn->settled_at?->format('d/m/Y') ?? '—' }}</td>
                <td>
                    @if(!$txn->settled_at && $txn->status === 'success')
                    <form method="POST" action="/payment-gateway/{{ $txn->id }}/reconcile" class="d-inline">
                        @csrf
                        <button class="btn-outline-gold btn-sm-gold" title="Reconcile"><i class="bi bi-check2-all"></i></button>
                    </form>
                    @else
                        <span style="color:#666;font-size:.75rem">{{ $txn->settled_at ? 'Reconciled' : '—' }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center" style="color:#666;padding:30px">No transactions found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $txns->links() }}</div>
</div>
@endsection
