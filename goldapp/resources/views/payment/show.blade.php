@extends('layouts.app')
@section('title','Payment - '.$payment->vch_no)
@section('page-title','Payment Voucher')

@section('content')
<div class="card-gold mb-3">
    <div class="card-header-gold">
        <h5>◆ Payment: {{ $payment->vch_no }}</h5>
        <div>
            <a href="{{ route('payments.edit', $payment) }}" class="btn-outline-gold btn-sm-gold">Edit</a>
            <a href="{{ route('payments.index') }}" class="btn-outline-gold btn-sm-gold ms-2">← Back</a>
        </div>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-md-3"><label class="form-label">Voucher No</label><div class="text-gold">{{ $payment->vch_no }}</div></div>
        <div class="col-md-3"><label class="form-label">Date</label><div>{{ $payment->vch_date }}</div></div>
        <div class="col-md-3"><label class="form-label">Party</label><div>{{ $payment->party_name }}</div></div>
        <div class="col-md-3"><label class="form-label">Mode</label><div>{{ strtoupper($payment->payment_mode) }}</div></div>
        <div class="col-md-3"><label class="form-label">Amount</label><div class="text-gold" style="font-size:1.1rem"><strong>{{ number_format($payment->amount, 2) }}</strong></div></div>
        @if($payment->cheque_no)<div class="col-md-3"><label class="form-label">Cheque No</label><div>{{ $payment->cheque_no }}</div></div>@endif
        @if($payment->narration)<div class="col-md-6"><label class="form-label">Narration</label><div>{{ $payment->narration }}</div></div>@endif
    </div>
</div>

<div class="card-gold">
    <div class="card-header-gold"><h5>◆ Daybook Entries</h5></div>
    <div class="table-responsive">
    <table class="table-gold w-100">
        <thead><tr><th>Account</th><th>Particular</th><th class="text-end text-debit">Debit</th><th class="text-end text-credit">Credit</th></tr></thead>
        <tbody>
        @foreach($daybookEntries as $e)
        <tr>
            <td>{{ $e->account_code }} - {{ $e->account_name }}</td>
            <td>{{ $e->particular }}</td>
            <td class="text-end text-debit">{{ $e->amount < 0 ? number_format(abs($e->amount), 2) : '' }}</td>
            <td class="text-end text-credit">{{ $e->amount > 0 ? number_format($e->amount, 2) : '' }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection
