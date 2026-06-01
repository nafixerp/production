@extends('layouts.app')
@section('title','General Ledger')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-book me-2"></i>General Ledger</h5>
    @if($account)<button onclick="window.print()" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer me-1"></i>Print</button>@endif
</div>

<div class="card-dark mb-3">
    <form method="GET" class="row g-2">
        <div class="col-md-4">
            <select name="account_id" class="form-select form-select-sm" required>
                <option value="">-- Select Account --</option>
                @foreach($accounts as $a)
                <option value="{{ $a->id }}" {{ $accountId == $a->id ? 'selected' : '' }}>{{ $a->code }} - {{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" class="form-control form-control-sm" value="{{ $fromDate }}"></div>
        <div class="col-md-2"><input type="date" name="to" class="form-control form-control-sm" value="{{ $toDate }}"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-outline-gold w-100">View Ledger</button></div>
    </form>
</div>

@if($account)
<div class="card-dark p-3 mb-3">
    <div class="d-flex justify-content-between">
        <h6 class="text-gold">{{ $account->code }} — {{ $account->name }} <span class="badge badge-group-{{ $account->account_group }}">{{ ucfirst($account->account_group) }}</span></h6>
        <div class="text-small text-muted">Period: {{ date('d M Y', strtotime($fromDate)) }} to {{ date('d M Y', strtotime($toDate)) }}</div>
    </div>
</div>

<div class="table-responsive">
<table class="table table-dark-erp">
    <thead>
        <tr><th>Date</th><th>Voucher No.</th><th>Particular</th><th>Type</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr>
    </thead>
    <tbody>
        <!-- Opening balance row -->
        <tr class="ob-row">
            <td colspan="4" class="text-gold">Opening Balance (as of {{ date('d M Y', strtotime($fromDate)) }})</td>
            <td class="text-end text-danger">{{ $openingBalance < 0 ? number_format(abs($openingBalance),2) : '-' }}</td>
            <td class="text-end text-success">{{ $openingBalance > 0 ? number_format($openingBalance,2) : '-' }}</td>
            <td class="text-end {{ $openingBalance < 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format(abs($openingBalance),2) }} {{ $openingBalance < 0 ? 'Dr' : 'Cr' }}
            </td>
        </tr>
        @forelse($entries as $row)
        <tr>
            <td>{{ $row['entry']->tdate }}</td>
            <td>{{ $row['entry']->slno }}</td>
            <td>{{ $row['entry']->particular }}</td>
            <td><span class="badge bg-secondary" style="font-size:.65rem">{{ $row['entry']->vtype }}</span></td>
            <td class="text-end text-danger">{{ $row['dr'] > 0 ? number_format($row['dr'],2) : '-' }}</td>
            <td class="text-end text-success">{{ $row['cr'] > 0 ? number_format($row['cr'],2) : '-' }}</td>
            <td class="text-end {{ $row['balance'] < 0 ? 'text-danger' : 'text-success' }} fw-bold">
                {{ number_format(abs($row['balance']),2) }} {{ $row['balance'] < 0 ? 'Dr' : 'Cr' }}
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted">No transactions in this period.</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="fw-bold" style="background:rgba(212,175,55,.1)">
            <td colspan="4" class="text-end text-gold">Closing Balance:</td>
            <td class="text-end text-danger">{{ $runningBalance < 0 ? number_format(abs($runningBalance),2) : '' }}</td>
            <td class="text-end text-success">{{ $runningBalance > 0 ? number_format($runningBalance,2) : '' }}</td>
            <td class="text-end {{ $runningBalance < 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format(abs($runningBalance),2) }} {{ $runningBalance < 0 ? 'Dr' : 'Cr' }}
            </td>
        </tr>
    </tfoot>
</table>
</div>
@else
<div class="text-center text-muted p-5">
    <i class="bi bi-book" style="font-size:3rem;opacity:.3"></i>
    <p class="mt-3">Select an account and date range to view the ledger.</p>
</div>
@endif

<style>
.badge-group-asset{background:#3498db}.badge-group-liability{background:#e74c3c}
.badge-group-equity{background:#9b59b6}.badge-group-income{background:#27ae60}
.badge-group-expense{background:#e67e22}.badge-group-tax{background:#95a5a6}
.ob-row{background:rgba(212,175,55,.05)}
.text-small{font-size:.78rem}
</style>
@endsection
