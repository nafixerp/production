@extends('layouts.app')
@section('title','Account Ledger')
@section('page-title','Account Ledger')

@section('content')
<div class="card-gold mb-3">
    <div class="card-header-gold"><h5>◆ Account Ledger</h5></div>

    <form method="GET" class="mb-3 d-flex flex-wrap gap-2 align-items-end">
        <div style="min-width:250px">
            <label class="form-label">Account *</label>
            <select name="account_id" class="form-select" required>
                <option value="">-- Select Account --</option>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>{{ $acc->code }} - {{ $acc->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="form-label">From</label><input type="date" name="from" value="{{ $from }}" class="form-control" style="width:155px"></div>
        <div><label class="form-label">To</label><input type="date" name="to" value="{{ $to }}" class="form-control" style="width:155px"></div>
        <button class="btn-outline-gold">Show Ledger</button>
    </form>
</div>

@if($ledgerData)
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ {{ $ledgerData['account']->name }} ({{ $ledgerData['account']->code }})</h5>
        <span class="form-label">Period: {{ $from }} to {{ $to }}</span>
    </div>

    <div class="table-responsive">
    <table class="table-gold w-100">
        <thead>
            <tr><th>Date</th><th>Slno</th><th>Type</th><th>Particular</th><th class="text-end text-debit">Debit</th><th class="text-end text-credit">Credit</th><th class="text-end">Balance</th><th>Dr/Cr</th></tr>
        </thead>
        <tbody>
        @forelse($ledgerData['rows'] as $row)
        <tr>
            <td>{{ $row['date'] }}</td>
            <td>{{ $row['slno'] }}</td>
            <td><span class="badge-gold">{{ $row['vtype'] }}</span></td>
            <td>{{ $row['particular'] }}</td>
            <td class="text-end text-debit">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '' }}</td>
            <td class="text-end text-credit">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '' }}</td>
            <td class="text-end text-gold">{{ number_format(abs($row['balance']), 2) }}</td>
            <td><span style="color: {{ $row['dr_cr'] === 'Dr' ? '#f87171' : '#4ade80' }}">{{ $row['dr_cr'] }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center" style="color:#666;padding:20px">No transactions in this period.</td></tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr style="border-top:1px solid var(--border-gold)">
                <td colspan="6" class="text-gold form-label">Closing Balance</td>
                <td class="text-end text-gold"><strong>{{ number_format(abs($ledgerData['closing_balance']), 2) }}</strong></td>
                <td><span style="color: {{ $ledgerData['closing_balance'] >= 0 ? '#f87171' : '#4ade80' }}"><strong>{{ $ledgerData['closing_balance'] >= 0 ? 'Dr' : 'Cr' }}</strong></span></td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>
@endif
@endsection
