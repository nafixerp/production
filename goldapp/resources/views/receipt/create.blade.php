@extends('layouts.app')
@section('title','New Receipt')
@section('page-title','New Receipt Voucher')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ New Receipt Voucher &nbsp;<small style="font-size:0.75rem;color:var(--text-muted-gold)">{{ $nextSlno }}</small></h5>
        <a href="{{ route('receipts.index') }}" class="btn-outline-gold btn-sm-gold">← Back</a>
    </div>

    <form method="POST" action="{{ route('receipts.store') }}">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-md-3"><label class="form-label">Voucher Date *</label><input type="date" name="vch_date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
            <div class="col-md-4">
                <label class="form-label">Party *</label>
                <select name="party_id" class="form-select" required>
                    <option value="">-- Select Party --</option>
                    @foreach($parties as $p)
                    <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }} ({{ $p->atype }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2"><label class="form-label">Amount *</label><input type="number" name="amount" class="form-control" value="0" step="0.0001" required min="0.01"></div>
            <div class="col-md-3"><label class="form-label">Discount</label><input type="number" name="discount" class="form-control" value="0" step="0.0001"></div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-select" onchange="toggleBankFields()">
                    <option value="cash">Cash</option>
                    <option value="bank">Bank</option>
                    <option value="cheque">Cheque</option>
                    <option value="pdc">PDC</option>
                </select>
            </div>
            <div class="col-md-3" id="bankAccountField" style="display:none">
                <label class="form-label">Bank Account</label>
                <select name="bank_account_id" class="form-select">
                    <option value="">-- Select Bank --</option>
                    @foreach($bankAccounts as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3" id="chequeNoField" style="display:none"><label class="form-label">Cheque No</label><input type="text" name="cheque_no" class="form-control"></div>
            <div class="col-md-3" id="chequeDateField" style="display:none"><label class="form-label">Cheque Date</label><input type="date" name="cheque_date" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">Bank Name</label><input type="text" name="bank_name" class="form-control" placeholder="Bank name"></div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-12"><label class="form-label">Narration</label><input type="text" name="narration" class="form-control" placeholder="Remarks..."></div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn-gold">Save Receipt</button>
            <a href="{{ route('receipts.index') }}" class="btn-outline-gold ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function toggleBankFields() {
    const mode = document.getElementById('payment_mode').value;
    const showBank = mode !== 'cash';
    document.getElementById('bankAccountField').style.display = showBank ? 'block' : 'none';
    document.getElementById('chequeNoField').style.display = (mode === 'cheque' || mode === 'pdc') ? 'block' : 'none';
    document.getElementById('chequeDateField').style.display = (mode === 'cheque' || mode === 'pdc') ? 'block' : 'none';
}
</script>
@endpush
