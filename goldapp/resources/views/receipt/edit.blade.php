@extends('layouts.app')
@section('title','Edit Receipt')
@section('page-title','Edit Receipt Voucher')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ Edit Receipt: {{ $receipt->vch_no }}</h5>
        <a href="{{ route('receipts.show', $receipt) }}" class="btn-outline-gold btn-sm-gold">← Back</a>
    </div>

    <form method="POST" action="{{ route('receipts.update', $receipt) }}">
        @csrf @method('PUT')

        <div class="row g-3 mb-3">
            <div class="col-md-3"><label class="form-label">Voucher Date *</label><input type="date" name="vch_date" class="form-control" value="{{ $receipt->vch_date }}" required></div>
            <div class="col-md-4">
                <label class="form-label">Party *</label>
                <select name="party_id" class="form-select" required>
                    <option value="">-- Select Party --</option>
                    @foreach($parties as $p)
                    <option value="{{ $p->id }}" {{ $receipt->party_id == $p->id ? 'selected' : '' }}>{{ $p->code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">Amount *</label><input type="number" name="amount" class="form-control" value="{{ $receipt->amount }}" step="0.0001" required></div>
            <div class="col-md-3"><label class="form-label">Discount</label><input type="number" name="discount" class="form-control" value="{{ $receipt->discount }}" step="0.0001"></div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-select" onchange="toggleBankFields()">
                    @foreach(['cash','bank','cheque','pdc'] as $mode)
                    <option value="{{ $mode }}" {{ $receipt->payment_mode == $mode ? 'selected' : '' }}>{{ strtoupper($mode) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">Bank Account</label>
                <select name="bank_account_id" class="form-select">
                    <option value="">-- Select Bank --</option>
                    @foreach($bankAccounts as $b)
                    <option value="{{ $b->id }}" {{ $receipt->bank_account_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><label class="form-label">Cheque No</label><input type="text" name="cheque_no" class="form-control" value="{{ $receipt->cheque_no }}"></div>
            <div class="col-md-3"><label class="form-label">Bank Name</label><input type="text" name="bank_name" class="form-control" value="{{ $receipt->bank_name }}"></div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-12"><label class="form-label">Narration</label><input type="text" name="narration" class="form-control" value="{{ $receipt->narration }}"></div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn-gold">Update Receipt</button>
            <a href="{{ route('receipts.index') }}" class="btn-outline-gold ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection
