@extends('layouts.app')
@section('title', 'JV: '.$voucher->slno)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-journal-text me-2"></i>{{ $voucher->slno }} <span class="badge bg-{{ $voucher->status==='posted'?'success':($voucher->status==='approved'?'info':'warning') }} ms-2">{{ ucfirst($voucher->status) }}</span></h5>
    <div class="d-flex gap-2">
        @if($voucher->status !== 'posted')
            <a href="{{ route('journal-vouchers.edit',$voucher->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
            <form method="POST" action="{{ route('journal-vouchers.post',$voucher->id) }}" class="d-inline" onsubmit="return confirm('Post to daybook?')">
                @csrf <button class="btn btn-sm btn-success"><i class="bi bi-send me-1"></i>Post to Daybook</button>
            </form>
        @endif
        <a href="{{ route('journal-vouchers.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card-dark p-3">
            <table class="table table-sm table-borderless mb-0">
                <tr><th class="text-muted" width="35%">Voucher No.</th><td class="text-gold fw-bold">{{ $voucher->slno }}</td></tr>
                <tr><th class="text-muted">Date</th><td>{{ $voucher->jv_date->format('d M Y') }}</td></tr>
                <tr><th class="text-muted">Type</th><td><span class="badge bg-secondary">{{ $voucher->voucher_type }}</span></td></tr>
                <tr><th class="text-muted">Narration</th><td>{{ $voucher->narration }}</td></tr>
                <tr><th class="text-muted">Total Debit</th><td class="text-danger fw-bold">₹{{ number_format($voucher->total_debit,2) }}</td></tr>
                <tr><th class="text-muted">Total Credit</th><td class="text-success fw-bold">₹{{ number_format($voucher->total_credit,2) }}</td></tr>
                <tr><th class="text-muted">Balanced</th><td>
                    @if(abs($voucher->total_debit - $voucher->total_credit) < 0.01)
                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Yes</span>
                    @else
                        <span class="text-danger"><i class="bi bi-x-circle-fill"></i> No</span>
                    @endif
                </td></tr>
            </table>
        </div>
    </div>
</div>

<div class="card-dark p-3 mb-3">
    <h6 class="text-gold mb-3">Journal Lines</h6>
    <div class="table-responsive">
    <table class="table table-dark-erp">
        <thead>
            <tr><th>Account</th><th>Particular</th><th>Cost Centre</th><th class="text-end">Debit (Dr)</th><th class="text-end">Credit (Cr)</th></tr>
        </thead>
        <tbody>
            @foreach($voucher->lines as $line)
            <tr>
                <td><strong>{{ $line->account_code }}</strong> - {{ $line->account_name }}</td>
                <td>{{ $line->particular }}</td>
                <td>{{ $line->cost_centre ?? '-' }}</td>
                <td class="text-end text-danger">{{ $line->amount < 0 ? number_format(abs($line->amount),4) : '-' }}</td>
                <td class="text-end text-success">{{ $line->amount > 0 ? number_format($line->amount,4) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Totals:</td>
                <td class="text-end text-danger">{{ number_format($voucher->total_debit,2) }}</td>
                <td class="text-end text-success">{{ number_format($voucher->total_credit,2) }}</td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>

@if($daybookEntries->count() > 0)
<div class="card-dark p-3">
    <h6 class="text-gold mb-3"><i class="bi bi-book me-1"></i>Posted to Daybook</h6>
    <div class="table-responsive">
    <table class="table table-dark-erp table-sm">
        <thead><tr><th>Date</th><th>Account</th><th>Particular</th><th class="text-end">Dr</th><th class="text-end">Cr</th></tr></thead>
        <tbody>
            @foreach($daybookEntries as $d)
            <tr>
                <td>{{ $d->tdate }}</td>
                <td>{{ $d->account_name }}</td>
                <td>{{ $d->particular }}</td>
                <td class="text-end text-danger">{{ $d->amount < 0 ? number_format(abs($d->amount),2) : '-' }}</td>
                <td class="text-end text-success">{{ $d->amount > 0 ? number_format($d->amount,2) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif
@endsection
