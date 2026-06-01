@extends('layouts.app')
@section('title', 'Account: '.$account->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-diagram-3 me-2"></i>{{ $account->code }} — {{ $account->name }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('chart-of-accounts.edit',$account->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('chart-of-accounts.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-5">
        <div class="card-dark p-3">
            <table class="table table-sm table-borderless mb-0">
                <tr><th class="text-muted" width="40%">Group</th><td><span class="badge badge-group-{{ $account->account_group }}">{{ ucfirst($account->account_group) }}</span></td></tr>
                <tr><th class="text-muted">Type</th><td>{{ $account->account_type }}</td></tr>
                <tr><th class="text-muted">Parent</th><td>{{ $account->parent?->name ?? 'Top Level' }}</td></tr>
                <tr><th class="text-muted">Opening Balance</th><td>{{ number_format($account->opening_balance,2) }} {{ strtoupper($account->ob_type) }}</td></tr>
                <tr><th class="text-muted">Current Balance</th><td class="{{ $balance < 0 ? 'text-danger' : 'text-success' }} fw-bold">₹{{ number_format(abs($balance),2) }} {{ $balance < 0 ? 'Dr' : 'Cr' }}</td></tr>
                @if($account->is_bank_account)
                <tr><th class="text-muted">Bank</th><td>{{ $account->bank_name }} — {{ $account->bank_account_no }}</td></tr>
                <tr><th class="text-muted">IFSC</th><td>{{ $account->ifsc }}</td></tr>
                @endif
                <tr><th class="text-muted">Status</th><td><span class="badge {{ $account->status ? 'bg-success' : 'bg-danger' }}">{{ $account->status ? 'Active' : 'Inactive' }}</span></td></tr>
            </table>
        </div>
    </div>
    @if($account->children->count() > 0)
    <div class="col-md-4">
        <div class="card-dark p-3">
            <h6 class="text-gold mb-2">Sub-Accounts</h6>
            @foreach($account->children as $child)
            <div class="d-flex justify-content-between mb-1">
                <a href="{{ route('chart-of-accounts.show',$child->id) }}" class="text-gold-link">{{ $child->code }} - {{ $child->name }}</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<div class="card-dark p-3">
    <h6 class="text-gold mb-3">Recent Transactions</h6>
    <div class="table-responsive">
    <table class="table table-dark-erp table-sm">
        <thead><tr><th>Date</th><th>Voucher</th><th>Particular</th><th>Type</th><th class="text-end">Dr</th><th class="text-end">Cr</th></tr></thead>
        <tbody>
            @forelse($entries as $e)
            <tr>
                <td>{{ $e->tdate }}</td>
                <td>{{ $e->slno }}</td>
                <td>{{ $e->particular }}</td>
                <td>{{ $e->vtype }}</td>
                <td class="text-end text-danger">{{ $e->amount < 0 ? number_format(abs($e->amount),2) : '-' }}</td>
                <td class="text-end text-success">{{ $e->amount > 0 ? number_format($e->amount,2) : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted">No transactions.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<style>
.badge-group-asset{background:#3498db}.badge-group-liability{background:#e74c3c}
.badge-group-equity{background:#9b59b6}.badge-group-income{background:#27ae60}
.badge-group-expense{background:#e67e22}.badge-group-tax{background:#95a5a6}
</style>
@endsection
