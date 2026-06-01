@extends('layouts.app')
@section('title', $invoice ? 'Edit Purchase Invoice' : 'New Purchase Invoice')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-receipt me-2"></i>{{ $invoice ? 'Edit Invoice: '.$invoice->slno : 'New Purchase Invoice' }}</h4>
    <a href="{{ route('purchase-invoices-new.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ $invoice ? route('purchase-invoices-new.update', $invoice) : route('purchase-invoices-new.store') }}">
    @csrf
    @if($invoice) @method('PUT') @endif

    <div class="card-erp p-3 mb-3">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label-erp">Invoice No</label>
                <input type="text" class="form-control form-erp" value="{{ $nextSlno }}" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Invoice Date <span class="text-danger">*</span></label>
                <input type="date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control form-erp" required>
            </div>
            <div class="col-md-4">
                <label class="form-label-erp">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-select form-erp" required>
                    <option value="">-- Select Supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id', $invoice->supplier_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->code }} - {{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Supplier Bill No</label>
                <input type="text" name="supplier_bill_no" value="{{ old('supplier_bill_no', $invoice->supplier_bill_no ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Supplier Bill Date</label>
                <input type="date" name="supplier_bill_date" value="{{ old('supplier_bill_date', $invoice->supplier_bill_date?->format('Y-m-d') ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-4">
                <label class="form-label-erp">Link to GRN</label>
                <select name="grn_id" class="form-select form-erp">
                    <option value="">-- None --</option>
                    @foreach($openGRNs as $g)
                        <option value="{{ $g->id }}" {{ old('grn_id', $invoice->grn_id ?? '') == $g->id ? 'selected' : '' }}>{{ $g->slno }} - {{ $g->supplier_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label-erp">Payment Mode</label>
                <select name="payment_mode" id="payMode" class="form-select form-erp" onchange="togglePayFields()">
                    @foreach(['credit','cash','bank','cheque'] as $pm)
                        <option value="{{ $pm }}" {{ old('payment_mode', $invoice->payment_mode ?? 'credit') === $pm ? 'selected' : '' }}>{{ ucfirst($pm) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4" id="bankField" style="display:none">
                <label class="form-label-erp">Bank Account</label>
                <select name="bank_account_id" class="form-select form-erp">
                    <option value="">-- Select Bank --</option>
                    @foreach($bankAccounts as $b)
                        <option value="{{ $b->id }}" {{ old('bank_account_id', $invoice->bank_account_id ?? '') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2" id="chequeNoField" style="display:none">
                <label class="form-label-erp">Cheque No</label>
                <input type="text" name="cheque_no" value="{{ old('cheque_no', $invoice->cheque_no ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-2" id="chequeDateField" style="display:none">
                <label class="form-label-erp">Cheque Date</label>
                <input type="date" name="cheque_date" value="{{ old('cheque_date', $invoice->cheque_date?->format('Y-m-d') ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-12">
                <label class="form-label-erp">Narration</label>
                <textarea name="narration" rows="1" class="form-control form-erp">{{ old('narration', $invoice->narration ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card-erp p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-gold mb-0">Invoice Items</h6>
            <button type="button" class="btn btn-sm btn-outline-gold" onclick="addPinvRow()"><i class="bi bi-plus-lg me-1"></i>Add Row</button>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-erp">
                <thead><tr><th>Type</th><th>Code</th><th>Item Name</th><th>HSN</th><th>Unit</th><th>Qty</th><th>Rate</th><th>Amount</th><th>SGST%</th><th>CGST%</th><th>IGST%</th><th>Net</th><th></th></tr></thead>
                <tbody id="pinvBody">
                @foreach($items as $idx => $item)
                    <tr>
                        <td><select name="items[{{ $idx }}][item_type]" class="form-select form-select-sm form-erp" style="width:70px"><option {{ ($item->item_type??'RM')==='RM'?'selected':'' }}>RM</option><option {{ ($item->item_type??'')==='PM'?'selected':'' }}>PM</option></select></td>
                        <td><input type="text" name="items[{{ $idx }}][item_code]" value="{{ $item->item_code??'' }}" class="form-control form-control-sm form-erp" style="width:85px"></td>
                        <td><input type="text" name="items[{{ $idx }}][item_name]" value="{{ $item->item_name??'' }}" class="form-control form-control-sm form-erp"></td>
                        <td><input type="text" name="items[{{ $idx }}][hsn_code]" value="{{ $item->hsn_code??'' }}" class="form-control form-control-sm form-erp" style="width:80px"></td>
                        <td><input type="text" name="items[{{ $idx }}][unit]" value="{{ $item->unit??'KG' }}" class="form-control form-control-sm form-erp" style="width:65px"></td>
                        <td><input type="number" name="items[{{ $idx }}][qty]" value="{{ $item->qty??0 }}" class="form-control form-control-sm form-erp" step="0.0001" style="width:80px" oninput="calcPinvRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][rate]" value="{{ $item->rate??0 }}" class="form-control form-control-sm form-erp" step="0.0001" style="width:90px" oninput="calcPinvRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][amount]" value="{{ $item->amount??0 }}" class="form-control form-control-sm form-erp" step="0.01" style="width:90px" readonly></td>
                        <td><input type="number" name="items[{{ $idx }}][sgst_pct]" value="0" class="form-control form-control-sm form-erp" step="0.01" style="width:65px" oninput="calcPinvRow(this)">
                            <input type="hidden" name="items[{{ $idx }}][sgst]" class="sgst-val" value="{{ $item->sgst??0 }}"></td>
                        <td><input type="number" name="items[{{ $idx }}][cgst_pct]" value="0" class="form-control form-control-sm form-erp" step="0.01" style="width:65px" oninput="calcPinvRow(this)">
                            <input type="hidden" name="items[{{ $idx }}][cgst]" class="cgst-val" value="{{ $item->cgst??0 }}"></td>
                        <td><input type="number" name="items[{{ $idx }}][igst_pct]" value="0" class="form-control form-control-sm form-erp" step="0.01" style="width:65px" oninput="calcPinvRow(this)">
                            <input type="hidden" name="items[{{ $idx }}][igst]" class="igst-val" value="{{ $item->igst??0 }}"></td>
                        <td><input type="number" name="items[{{ $idx }}][net_amount]" value="{{ $item->net_amount??0 }}" class="form-control form-control-sm form-erp" step="0.01" style="width:90px" readonly></td>
                        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove();calcPinvTotals()"><i class="bi bi-trash"></i></button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-erp p-3 mb-3">
        <div class="row g-3 justify-content-end">
            <div class="col-md-2"><label class="form-label-erp">Discount</label><input type="number" name="discount" id="discField" value="{{ old('discount', $invoice->discount ?? 0) }}" class="form-control form-erp" step="0.01" oninput="calcPinvTotals()"></div>
            <div class="col-md-2"><label class="form-label-erp">Other Charges</label><input type="number" name="other_charges" id="otherField" value="{{ old('other_charges', $invoice->other_charges ?? 0) }}" class="form-control form-erp" step="0.01" oninput="calcPinvTotals()"></div>
            <div class="col-md-2"><label class="form-label-erp">TCS</label><input type="number" name="tcs" value="{{ old('tcs', $invoice->tcs ?? 0) }}" class="form-control form-erp" step="0.01"></div>
            <div class="col-md-2"><label class="form-label-erp">SGST</label><input type="number" name="sgst" id="piSgst" value="{{ old('sgst', $invoice->sgst ?? 0) }}" class="form-control form-erp" step="0.01" readonly></div>
            <div class="col-md-2"><label class="form-label-erp">CGST</label><input type="number" name="cgst" id="piCgst" value="{{ old('cgst', $invoice->cgst ?? 0) }}" class="form-control form-erp" step="0.01" readonly></div>
            <div class="col-md-2"><label class="form-label-erp">IGST</label><input type="number" name="igst" id="piIgst" value="{{ old('igst', $invoice->igst ?? 0) }}" class="form-control form-erp" step="0.01" readonly></div>
            <div class="col-md-2"><label class="form-label-erp">Net Amount</label><input type="number" name="net_amount" id="piNet" value="{{ old('net_amount', $invoice->net_amount ?? 0) }}" class="form-control form-erp fw-bold text-gold" step="0.01" readonly></div>
            <div class="col-md-2"><label class="form-label-erp">Amount Paid Now</label><input type="number" name="paid_amount" id="piPaid" value="{{ old('paid_amount', $invoice->paid_amount ?? 0) }}" class="form-control form-erp" step="0.01" oninput="calcPinvTotals()"></div>
            <div class="col-md-2"><label class="form-label-erp">Balance</label><input type="number" id="piBalance" value="0" class="form-control form-erp" step="0.01" readonly></div>
        </div>
    </div>

    <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>{{ $invoice ? 'Update' : 'Post' }} Invoice</button>
    <a href="{{ route('purchase-invoices-new.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
</form>

<template id="pinvRowTmpl">
    <tr>
        <td><select name="items[__IDX__][item_type]" class="form-select form-select-sm form-erp" style="width:70px"><option>RM</option><option>PM</option></select></td>
        <td><input type="text" name="items[__IDX__][item_code]" class="form-control form-control-sm form-erp" style="width:85px"></td>
        <td><input type="text" name="items[__IDX__][item_name]" class="form-control form-control-sm form-erp"></td>
        <td><input type="text" name="items[__IDX__][hsn_code]" class="form-control form-control-sm form-erp" style="width:80px"></td>
        <td><input type="text" name="items[__IDX__][unit]" value="KG" class="form-control form-control-sm form-erp" style="width:65px"></td>
        <td><input type="number" name="items[__IDX__][qty]" value="0" class="form-control form-control-sm form-erp" step="0.0001" style="width:80px" oninput="calcPinvRow(this)"></td>
        <td><input type="number" name="items[__IDX__][rate]" value="0" class="form-control form-control-sm form-erp" step="0.0001" style="width:90px" oninput="calcPinvRow(this)"></td>
        <td><input type="number" name="items[__IDX__][amount]" value="0" class="form-control form-control-sm form-erp" step="0.01" style="width:90px" readonly></td>
        <td><input type="number" name="items[__IDX__][sgst_pct]" value="0" class="form-control form-control-sm form-erp" step="0.01" style="width:65px" oninput="calcPinvRow(this)"><input type="hidden" name="items[__IDX__][sgst]" class="sgst-val" value="0"></td>
        <td><input type="number" name="items[__IDX__][cgst_pct]" value="0" class="form-control form-control-sm form-erp" step="0.01" style="width:65px" oninput="calcPinvRow(this)"><input type="hidden" name="items[__IDX__][cgst]" class="cgst-val" value="0"></td>
        <td><input type="number" name="items[__IDX__][igst_pct]" value="0" class="form-control form-control-sm form-erp" step="0.01" style="width:65px" oninput="calcPinvRow(this)"><input type="hidden" name="items[__IDX__][igst]" class="igst-val" value="0"></td>
        <td><input type="number" name="items[__IDX__][net_amount]" value="0" class="form-control form-control-sm form-erp" step="0.01" style="width:90px" readonly></td>
        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove();calcPinvTotals()"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>

@push('scripts')
<script>
let pinvIdx = {{ count($items) }};
function addPinvRow() {
    const t = document.getElementById('pinvRowTmpl').innerHTML.replace(/__IDX__/g, pinvIdx++);
    document.getElementById('pinvBody').insertAdjacentHTML('beforeend', t);
}
function calcPinvRow(inp) {
    const row = inp.closest('tr');
    const qty  = parseFloat(row.querySelector('[name*="[qty]"]').value)||0;
    const rate = parseFloat(row.querySelector('[name*="[rate]"]').value)||0;
    const sp   = parseFloat(row.querySelector('[name*="[sgst_pct]"]').value)||0;
    const cp   = parseFloat(row.querySelector('[name*="[cgst_pct]"]').value)||0;
    const ip   = parseFloat(row.querySelector('[name*="[igst_pct]"]').value)||0;
    const amt  = qty*rate;
    const sg   = amt*sp/100, cg = amt*cp/100, ig = amt*ip/100;
    row.querySelector('[name*="[amount]"]').value = amt.toFixed(2);
    row.querySelector('[name*="[net_amount]"]').value = (amt+sg+cg+ig).toFixed(2);
    row.querySelector('.sgst-val').value = sg.toFixed(2);
    row.querySelector('.cgst-val').value = cg.toFixed(2);
    row.querySelector('.igst-val').value = ig.toFixed(2);
    calcPinvTotals();
}
function calcPinvTotals() {
    let taxable=0, net=0, sg=0, cg=0, ig=0;
    document.querySelectorAll('#pinvBody tr').forEach(row => {
        taxable += parseFloat(row.querySelector('[name*="[amount]"]')?.value)||0;
        net     += parseFloat(row.querySelector('[name*="[net_amount]"]')?.value)||0;
        sg      += parseFloat(row.querySelector('.sgst-val')?.value)||0;
        cg      += parseFloat(row.querySelector('.cgst-val')?.value)||0;
        ig      += parseFloat(row.querySelector('.igst-val')?.value)||0;
    });
    const disc  = parseFloat(document.getElementById('discField').value)||0;
    const other = parseFloat(document.getElementById('otherField').value)||0;
    const paid  = parseFloat(document.getElementById('piPaid').value)||0;
    const total = net - disc + other;
    document.getElementById('piSgst').value = sg.toFixed(2);
    document.getElementById('piCgst').value = cg.toFixed(2);
    document.getElementById('piIgst').value = ig.toFixed(2);
    document.getElementById('piNet').value  = total.toFixed(2);
    document.getElementById('piBalance').value = (total - paid).toFixed(2);
}
function togglePayFields() {
    const mode = document.getElementById('payMode').value;
    document.getElementById('bankField').style.display = (mode==='bank'||mode==='cheque')?'':'none';
    document.getElementById('chequeNoField').style.display = mode==='cheque'?'':'none';
    document.getElementById('chequeDateField').style.display = mode==='cheque'?'':'none';
}
document.addEventListener('DOMContentLoaded', () => { calcPinvTotals(); togglePayFields(); });
</script>
@endpush
@endsection
