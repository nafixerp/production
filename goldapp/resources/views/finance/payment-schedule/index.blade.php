@extends('layouts.app')
@section('title','Payment Schedule')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-calendar-check me-2"></i>Payment Schedule — AP/AR Ageing</h5>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-2">
            <select name="party_type" class="form-select form-select-sm">
                <option value="all" {{ $partyType==='all'?'selected':'' }}>All Parties</option>
                <option value="supplier" {{ $partyType==='supplier'?'selected':'' }}>Suppliers (AP)</option>
                <option value="customer" {{ $partyType==='customer'?'selected':'' }}>Customers (AR)</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Status</option>
                @foreach(['pending','partial','paid','overdue'] as $s)
                <option value="{{ $s }}" {{ $status===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-gold w-100">Filter</button></div>
    </form>
</div>

<!-- Ageing Summary -->
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-success">₹{{ number_format($ageing->current_due ?? 0,0) }}</div><div class="stat-lbl">Current (Not Due)</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-warning">₹{{ number_format($ageing->overdue_30 ?? 0,0) }}</div><div class="stat-lbl">Overdue 1-30 Days</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-orange">₹{{ number_format($ageing->overdue_60 ?? 0,0) }}</div><div class="stat-lbl">Overdue 31-60 Days</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-danger">₹{{ number_format($ageing->overdue_90_plus ?? 0,0) }}</div><div class="stat-lbl">Overdue 60+ Days</div></div></div>
</div>

<div class="table-responsive">
<table class="table table-dark-erp table-hover">
    <thead>
        <tr><th>Party Type</th><th>Ref</th><th>Due Date</th><th class="text-end">Amount</th><th class="text-end">Paid</th><th class="text-end">Pending</th><th>Overdue Days</th><th>Status</th></tr>
    </thead>
    <tbody>
        @forelse($schedules as $s)
        @php
            $overdueDays = $s->due_date < today() ? today()->diffInDays($s->due_date) : 0;
            $pending = $s->amount - $s->paid_amount;
        @endphp
        <tr class="{{ $s->status==='overdue' ? 'overdue-row' : '' }}">
            <td><span class="badge {{ $s->party_type==='supplier'?'bg-purple':'bg-info' }}">{{ ucfirst($s->party_type) }}</span></td>
            <td>{{ $s->ref_type }} #{{ $s->ref_id }}</td>
            <td class="{{ $s->status==='overdue'?'text-danger fw-bold':'' }}">{{ $s->due_date->format('d M Y') }}</td>
            <td class="text-end">{{ number_format($s->amount,2) }}</td>
            <td class="text-end text-success">{{ number_format($s->paid_amount,2) }}</td>
            <td class="text-end {{ $s->status==='overdue'?'text-danger fw-bold':'' }}">{{ number_format($pending,2) }}</td>
            <td>{{ $overdueDays > 0 ? '<span class="text-danger">'.$overdueDays.' days</span>' : '-' }}</td>
            <td><span class="badge bg-{{ $s->status==='paid'?'success':($s->status==='overdue'?'danger':($s->status==='partial'?'warning':'secondary')) }}">{{ ucfirst($s->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted">No payment schedules.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
{{ $schedules->links() }}

<style>
.overdue-row{background:rgba(231,76,60,.05)}
.stat-mini{text-align:center;padding:10px;background:#13132a;border:1px solid var(--border-gold);border-radius:6px}
.stat-val{font-size:1.1rem;font-weight:700}.stat-lbl{font-size:.65rem;color:rgba(212,175,55,.6);letter-spacing:1px}
.text-orange{color:#e67e22!important}.bg-purple{background:#9b59b6!important}
</style>
@endsection
