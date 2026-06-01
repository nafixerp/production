@extends('layouts.app')
@section('title', $order ? 'Edit Purchase Order' : 'New Purchase Order')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-cart-plus-fill me-2"></i>{{ $order ? 'Edit PO: '.$order->slno : 'New Purchase Order' }}</h4>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ $order ? route('purchase-orders.update', $order) : route('purchase-orders.store') }}" id="poForm">
    @csrf
    @if($order) @method('PUT') @endif

    <div class="card-erp p-3 mb-3">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label-erp">PO No</label>
                <input type="text" class="form-control form-erp" value="{{ $nextSlno }}" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">PO Date <span class="text-danger">*</span></label>
                <input type="date" name="po_date" value="{{ old('po_date', $order->po_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control form-erp" required>
            </div>
            <div class="col-md-4">
                <label class="form-label-erp">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" id="supplierSelect" class="form-select form-erp" required>
                    <option value="">-- Select Supplier --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" data-terms="{{ $s->payment_terms }}" data-credit="{{ $s->credit_days }}"
                            {{ old('supplier_id', $order->supplier_id ?? '') == $s->id ? 'selected' : '' }}>
                            {{ $s->code }} - {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Delivery Date</label>
                <input type="date" name="delivery_date" value="{{ old('delivery_date', $order->delivery_date?->format('Y-m-d') ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Currency</label>
                <input type="text" name="currency" value="{{ old('currency', $order->currency ?? 'INR') }}" class="form-control form-erp" maxlength="10">
            </div>
            <div class="col-md-4">
                <label class="form-label-erp">Payment Terms</label>
                <input type="text" name="payment_terms" id="paymentTerms" value="{{ old('payment_terms', $order->payment_terms ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-8">
                <label class="form-label-erp">Shipping Address</label>
                <input type="text" name="shipping_address" value="{{ old('shipping_address', $order->shipping_address ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-12">
                <label class="form-label-erp">Narration</label>
                <textarea name="narration" rows="1" class="form-control form-erp">{{ old('narration', $order->narration ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Line Items -->
    <div class="card-erp p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-gold mb-0">Order Items</h6>
            <button type="button" class="btn btn-sm btn-outline-gold" onclick="addRow()"><i class="bi bi-plus-lg me-1"></i>Add Row</button>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-erp" id="itemsTable">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Item Code</th>
                        <th>Item Name</th>
                        <th>HSN</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Disc%</th>
                        <th>SGST%</th>
                        <th>CGST%</th>
                        <th>IGST%</th>
                        <th>Net</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                @if(count($items) > 0)
                    @foreach($items as $idx => $item)
                    <tr>
                        <td><select name="items[{{ $idx }}][item_type]" class="form-select form-select-sm form-erp" style="width:70px"><option {{ ($item->item_type??'RM')==='RM'?'selected':'' }}>RM</option><option {{ ($item->item_type??'')==='PM'?'selected':'' }}>PM</option><option {{ ($item->item_type??'')==='FG'?'selected':'' }}>FG</option></select></td>
                        <td><input type="text" name="items[{{ $idx }}][item_code]" value="{{ $item->item_code??'' }}" class="form-control form-control-sm form-erp" style="width:90px"></td>
                        <td><input type="text" name="items[{{ $idx }}][item_name]" value="{{ $item->item_name??'' }}" class="form-control form-control-sm form-erp" required></td>
                        <td><input type="text" name="items[{{ $idx }}][hsn_code]" value="{{ $item->hsn_code??'' }}" class="form-control form-control-sm form-erp" style="width:80px"></td>
                        <td><input type="text" name="items[{{ $idx }}][unit]" value="{{ $item->unit??'KG' }}" class="form-control form-control-sm form-erp" style="width:65px"></td>
                        <td><input type="number" name="items[{{ $idx }}][qty]" value="{{ $item->qty??0 }}" class="form-control form-control-sm form-erp calc-field" step="0.0001" style="width:80px" oninput="calcRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][rate]" value="{{ $item->rate??0 }}" class="form-control form-control-sm form-erp calc-field" step="0.0001" style="width:90px" oninput="calcRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][amount]" value="{{ $item->amount??0 }}" class="form-control form-control-sm form-erp amount-field" step="0.01" style="width:90px" readonly></td>
                        <td><input type="number" name="items[{{ $idx }}][discount_pct]" value="{{ $item->discount_pct??0 }}" class="form-control form-control-sm form-erp calc-field" step="0.01" style="width:65px" oninput="calcRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][sgst_pct]" value="{{ $item->sgst_pct??0 }}" class="form-control form-control-sm form-erp gst-field" step="0.01" style="width:65px" oninput="calcRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][cgst_pct]" value="{{ $item->cgst_pct??0 }}" class="form-control form-control-sm form-erp gst-field" step="0.01" style="width:65px" oninput="calcRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][igst_pct]" value="{{ $item->igst_pct??0 }}" class="form-control form-control-sm form-erp gst-field" step="0.01" style="width:65px" oninput="calcRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][net_amount]" value="{{ $item->net_amount??0 }}" class="form-control form-control-sm form-erp net-field" step="0.01" style="width:90px" readonly></td>
                        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
                    </tr>
                    @endforeach
                @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-end text-muted small">Totals</td>
                        <td><input type="number" id="totalTaxable" class="form-control form-control-sm form-erp" readonly></td>
                        <td colspan="4"></td>
                        <td><input type="number" id="totalNet" name="net_amount_display" class="form-control form-control-sm form-erp" readonly></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card-erp p-3 mb-3">
        <div class="row g-3 justify-content-end">
            <div class="col-md-2">
                <label class="form-label-erp">Discount</label>
                <input type="number" name="discount_amount" id="discountAmt" value="{{ old('discount_amount', $order->discount_amount ?? 0) }}" class="form-control form-erp" step="0.01" oninput="calcTotals()">
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">TCS</label>
                <input type="number" name="tcs" value="{{ old('tcs', $order->tcs ?? 0) }}" class="form-control form-erp" step="0.01">
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Total SGST</label>
                <input type="number" name="sgst" id="totalSgst" value="{{ old('sgst', $order->sgst ?? 0) }}" class="form-control form-erp" step="0.01" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Total CGST</label>
                <input type="number" name="cgst" id="totalCgst" value="{{ old('cgst', $order->cgst ?? 0) }}" class="form-control form-erp" step="0.01" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Total IGST</label>
                <input type="number" name="igst" id="totalIgst" value="{{ old('igst', $order->igst ?? 0) }}" class="form-control form-erp" step="0.01" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label-erp fw-bold">Net Amount</label>
                <input type="number" name="net_amount" id="finalNet" value="{{ old('net_amount', $order->net_amount ?? 0) }}" class="form-control form-erp fw-bold text-gold" step="0.01" readonly>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>{{ $order ? 'Update' : 'Save' }} Purchase Order</button>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<template id="rowTemplate">
    <tr>
        <td><select name="items[__IDX__][item_type]" class="form-select form-select-sm form-erp" style="width:70px"><option>RM</option><option>PM</option><option>FG</option></select></td>
        <td><input type="text" name="items[__IDX__][item_code]" class="form-control form-control-sm form-erp" style="width:90px"></td>
        <td><input type="text" name="items[__IDX__][item_name]" class="form-control form-control-sm form-erp" required></td>
        <td><input type="text" name="items[__IDX__][hsn_code]" class="form-control form-control-sm form-erp" style="width:80px"></td>
        <td><input type="text" name="items[__IDX__][unit]" value="KG" class="form-control form-control-sm form-erp" style="width:65px"></td>
        <td><input type="number" name="items[__IDX__][qty]" value="0" class="form-control form-control-sm form-erp calc-field" step="0.0001" style="width:80px" oninput="calcRow(this)"></td>
        <td><input type="number" name="items[__IDX__][rate]" value="0" class="form-control form-control-sm form-erp calc-field" step="0.0001" style="width:90px" oninput="calcRow(this)"></td>
        <td><input type="number" name="items[__IDX__][amount]" value="0" class="form-control form-control-sm form-erp amount-field" step="0.01" style="width:90px" readonly></td>
        <td><input type="number" name="items[__IDX__][discount_pct]" value="0" class="form-control form-control-sm form-erp calc-field" step="0.01" style="width:65px" oninput="calcRow(this)"></td>
        <td><input type="number" name="items[__IDX__][sgst_pct]" value="0" class="form-control form-control-sm form-erp gst-field" step="0.01" style="width:65px" oninput="calcRow(this)"></td>
        <td><input type="number" name="items[__IDX__][cgst_pct]" value="0" class="form-control form-control-sm form-erp gst-field" step="0.01" style="width:65px" oninput="calcRow(this)"></td>
        <td><input type="number" name="items[__IDX__][igst_pct]" value="0" class="form-control form-control-sm form-erp gst-field" step="0.01" style="width:65px" oninput="calcRow(this)"></td>
        <td><input type="number" name="items[__IDX__][net_amount]" value="0" class="form-control form-control-sm form-erp net-field" step="0.01" style="width:90px" readonly></td>
        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="removeRow(this)"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>

@push('scripts')
<script>
let rowIdx = {{ count($items) }};

function addRow() {
    const tmpl = document.getElementById('rowTemplate').innerHTML.replace(/__IDX__/g, rowIdx++);
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', tmpl);
}

function removeRow(btn) {
    btn.closest('tr').remove();
    calcTotals();
}

function calcRow(input) {
    const row = input.closest('tr');
    const qty = parseFloat(row.querySelector('[name*="[qty]"]').value) || 0;
    const rate = parseFloat(row.querySelector('[name*="[rate]"]').value) || 0;
    const disc = parseFloat(row.querySelector('[name*="[discount_pct]"]').value) || 0;
    const sgstPct = parseFloat(row.querySelector('[name*="[sgst_pct]"]').value) || 0;
    const cgstPct = parseFloat(row.querySelector('[name*="[cgst_pct]"]').value) || 0;
    const igstPct = parseFloat(row.querySelector('[name*="[igst_pct]"]').value) || 0;

    const amount = qty * rate * (1 - disc / 100);
    const sgst = amount * sgstPct / 100;
    const cgst = amount * cgstPct / 100;
    const igst = amount * igstPct / 100;
    const net = amount + sgst + cgst + igst;

    row.querySelector('[name*="[amount]"]').value = amount.toFixed(2);
    row.querySelector('[name*="[net_amount]"]').value = net.toFixed(2);

    // Set hidden sgst/cgst/igst values
    setOrCreate(row, 'sgst', sgst);
    setOrCreate(row, 'cgst', cgst);
    setOrCreate(row, 'igst', igst);

    calcTotals();
}

function setOrCreate(row, field, val) {
    let el = row.querySelector(`[name*="[${field}]"]:not([name*="pct"])`);
    if (el) el.value = val.toFixed(2);
}

function calcTotals() {
    let taxable = 0, net = 0, sgst = 0, cgst = 0, igst = 0;
    document.querySelectorAll('#itemsBody tr').forEach(row => {
        taxable += parseFloat(row.querySelector('[name*="[amount]"]')?.value) || 0;
        net     += parseFloat(row.querySelector('[name*="[net_amount]"]')?.value) || 0;
        sgst    += parseFloat(row.querySelector('[name*="[sgst_pct]"]')?.value || 0) * (parseFloat(row.querySelector('[name*="[amount]"]')?.value) || 0) / 100;
        cgst    += parseFloat(row.querySelector('[name*="[cgst_pct]"]')?.value || 0) * (parseFloat(row.querySelector('[name*="[amount]"]')?.value) || 0) / 100;
        igst    += parseFloat(row.querySelector('[name*="[igst_pct]"]')?.value || 0) * (parseFloat(row.querySelector('[name*="[amount]"]')?.value) || 0) / 100;
    });
    const disc = parseFloat(document.getElementById('discountAmt')?.value) || 0;
    document.getElementById('totalTaxable').value = taxable.toFixed(2);
    document.getElementById('totalNet').value = net.toFixed(2);
    document.getElementById('totalSgst').value = sgst.toFixed(2);
    document.getElementById('totalCgst').value = cgst.toFixed(2);
    document.getElementById('totalIgst').value = igst.toFixed(2);
    document.getElementById('finalNet').value = (net - disc).toFixed(2);
}

document.getElementById('supplierSelect').addEventListener('change', function() {
    const opt = this.selectedOptions[0];
    document.getElementById('paymentTerms').value = opt.dataset.terms || '';
});

document.addEventListener('DOMContentLoaded', calcTotals);
</script>
@endpush
@endsection
