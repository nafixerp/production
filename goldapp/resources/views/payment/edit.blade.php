@extends('layouts.app')
@section('title','Edit Payment')
@section('page-title','Edit Payment Voucher')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ Edit Payment: {{ $payment->vch_no }}</h5>
        <a href="{{ route('payments.show', $payment) }}" class="btn-outline-gold btn-sm-gold">← Back</a>
    </div>

    <form method="POST" action="{{ route('payments.update', $payment) }}">
        @csrf @method('PUT')

        <div class="row g-3 mb-3">
            <div class="col-md-3"><label class="form-label">Voucher Date *</label><input type="date" name="vch_date" class="form-control" value="{{ $payment->vch_date }}" required></div>
            <div class="col-md-4">
                <label class="form-label">Party *</label>
                <select name="party_id" class="form-select" required>
                    <option value="">-- Select Party --</option>
                    @foreach($parties as $p)
                    <option value="{{ $p->id }}" {{ $payment->party_id == $p->id ? 'selected' : '' }}>{{ $p->code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">Amount *</label><input type="number" name="amount" class="form-control" value="{{ $payment->amount }}" step="0.0001" required></div>
            <div class="col-md-3">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-select" onchange="toggleBankFields()">
                    @foreach(['cash','bank','cheque','pdc'] as $mode)
                    <option value="{{ $mode }}" {{ $payment->payment_mode == $mode ? 'selected' : '' }}>{{ strtoupper($mode) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label">Bank Account</label>
                <select name="bank_account_id" class="form-select">
                    <option value="">-- Select Bank --</option>
                    @foreach($bankAccounts as $b)
                    <option value="{{ $b->id }}" {{ $payment->bank_account_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4"><label class="form-label">Cheque No</label><input type="text" name="cheque_no" class="form-control" value="{{ $payment->cheque_no }}"></div>
            <div class="col-md-4"><label class="form-label">Bank Name</label><input type="text" name="bank_name" class="form-control" value="{{ $payment->bank_name }}"></div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-12"><label class="form-label">Narration</label><input type="text" name="narration" class="form-control" value="{{ $payment->narration }}"></div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn-gold">Update Payment</button>
            <a href="{{ route('payments.index') }}" class="btn-outline-gold ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
