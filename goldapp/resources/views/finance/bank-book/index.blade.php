@extends('layouts.app')
@section('title','Bank Book')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-bank me-2"></i>Bank Book</h5>
    <button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Print</button>
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3">
            <select name="bank_account_id" class="form-select form-select-sm">
                <option value="">All Bank Accounts</option>
                @foreach($bankAccounts as $b)
                <option value="{{ $b->id }}" {{ $bankAccountId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" class="form-control form-control-sm" value="{{ $fromDate }}"></div>
        <div class="col-md-2"><input type="date" name="to" class="form-control form-control-sm" value="{{ $toDate }}"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-gold w-100">View</button></div>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-gold">₹{{ number_format(abs($openingBalance),2) }}</div><div class="stat-lbl">Opening Balance</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-success">₹{{ number_format($totalReceipts,2) }}</div><div class="stat-lbl">Total Credits</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val text-danger">₹{{ number_format($totalPayments,2) }}</div><div class="stat-lbl">Total Debits</div></div></div>
    <div class="col-md-3"><div class="stat-mini"><div class="stat-val {{ $closingBalance < 0 ? 'text-danger' : 'text-gold' }} fw-bold">₹{{ number_format(abs($closingBalance),2) }}</div><div class="stat-lbl">Closing Balance</div></div></div>
</div>

<div class="table-responsive">
<table class="table table-dark-erp">
    <thead>
        <tr><th>Date</th><th>Voucher</th><th>Particular</th><th>Type</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr>
    </thead>
    <tbody>
        @forelse($rows as $row)
        <tr>
            <td>{{ $row['entry']->tdate }}</td>
            <td>{{ $row['entry']->slno }}</td>
            <td>{{ $row['entry']->particular }}</td>
            <td><span class="badge bg-secondary" style="font-size:.65rem">{{ $row['entry']->vtype }}</span></td>
            <td class="text-end text-danger">{{ $row['dr'] > 0 ? number_format($row['dr'],2) : '-' }}</td>
            <td class="text-end text-success">{{ $row['cr'] > 0 ? number_format($row['cr'],2) : '-' }}</td>
            <td class="text-end {{ $row['balance'] < 0 ? 'text-danger' : 'text-success' }} fw-bold">{{ number_format(abs($row['balance']),2) }} {{ $row['balance'] < 0 ? 'Dr' : 'Cr' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted">No bank transactions.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
<style>
.stat-mini{text-align:center;padding:10px;background:#13132a;border:1px solid var(--border-gold);border-radius:6px}
.stat-val{font-size:1.1rem;font-weight:700}.stat-lbl{font-size:.65rem;color:rgba(212,175,55,.6);letter-spacing:1px}
</style>
@endsection
