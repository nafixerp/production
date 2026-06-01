@extends('layouts.app')
@section('title', 'Salary Posting Detail')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-journal-text me-2"></i>Salary Posting — {{ $part?->slno ?? '' }}</h5>
    <a href="{{ route('salary-posting.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($part)
<div class="card-dark p-3 mb-3">
    <div class="row">
        <div class="col-md-3"><small class="text-muted">Slno</small><br><strong class="text-gold">{{ $part->slno }}</strong></div>
        <div class="col-md-3"><small class="text-muted">Date</small><br>{{ $part->tdate }}</div>
        <div class="col-md-6"><small class="text-muted">Particular</small><br>{{ $part->particular }}</div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-dark-gold table-sm">
        <thead>
            <tr>
                <th>Account Code</th>
                <th>Account Name</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
                <th>Particular</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $l)
            <tr>
                <td><span class="badge bg-secondary">{{ $l->account_code }}</span></td>
                <td>{{ $l->account_name }}</td>
                <td class="text-end text-danger">{{ $l->amount < 0 ? '₹'.number_format(abs($l->amount), 2) : '' }}</td>
                <td class="text-end text-success">{{ $l->amount > 0 ? '₹'.number_format($l->amount, 2) : '' }}</td>
                <td>{{ $l->particular }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top:2px solid var(--gold)">
                <th colspan="2" class="text-end text-gold">Total (should be 0)</th>
                <th class="text-end text-danger">₹{{ number_format($lines->filter(fn($l)=>$l->amount<0)->sum(fn($l)=>abs($l->amount)), 2) }}</th>
                <th class="text-end text-success">₹{{ number_format($lines->filter(fn($l)=>$l->amount>0)->sum('amount'), 2) }}</th>
                <th>Balance: ₹{{ number_format($lines->sum('amount'), 2) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
@else
<div class="alert alert-warning">Entry not found.</div>
@endif
@endsection
