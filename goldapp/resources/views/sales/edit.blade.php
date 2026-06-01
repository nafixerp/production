@extends('layouts.app')
@section('title','Edit Sales Invoice')
@section('page-title','Edit Sales Invoice')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ Edit Invoice: {{ $sale->invoice_no }}</h5>
        <a href="{{ route('sales.show', $sale) }}" class="btn-outline-gold btn-sm-gold">← Back</a>
    </div>

    <form method="POST" action="{{ route('sales.update', $sale) }}" id="salesForm">
        @csrf @method('PUT')

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Invoice Date *</label>
                <input type="date" name="invoice_date" class="form-control" value="{{ $sale->invoice_date }}" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Customer *</label>
                <select name="customer_id" class="form-select" required>
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ $sale->customer_id == $c->id ? 'selected' : '' }}>{{ $c->code }} - {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-select" onchange="toggleBankFields()">
                    @foreach(['credit','cash','bank','cheque'] as $mode)
                    <option value="{{ $mode }}" {{ $sale->payment_mode == $mode ? 'selected' : '' }}>{{ strtoupper($mode) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3" id="bankFields" style="{{ in_array($sale->payment_mode, ['bank','cheque']) ? '' : 'display:none' }}">
            <div class="col-md-4">
                <label class="form-label">Bank Account</label>
                <select name="bank_account_id" class="form-select">
                    <option value="">-- Select Bank --</option>
                    @foreach($bankAccounts as $b)
                    <option value="{{ $b->id }}" {{ $sale->bank_account_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cheque No</label>
                <input type="text" name="cheque_no" class="form-control" value="{{ $sale->cheque_no }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Cheque Date</label>
                <input type="date" name="cheque_date" class="form-control" value="{{ $sale->cheque_date }}">
            </div>
        </div>

        <div class="section-title mt-4">Line Items</div>
        <div class="table-responsive">
        <table class="items-table table-gold w-100" id="itemsTable">
            <thead>
                <tr><th>#</th><th>Item Name</th><th>HSN</th><th>Unit</th><th>Qty</th><th>Rate</th><th>Amount</th><th>Disc</th><th>SGST%</th><th>CGST%</th><th>IGST%</th><th>SGST</th><th>CGST</th><th>IGST</th><th></th></tr>
            </thead>
            <tbody id="itemsBody">
            @foreach($sale->details as $idx => $d)
            <tr id="row_{{ $idx }}">
                <td>{{ $idx+1 }}</td>
                <td><input type="text" name="items[{{ $idx }}][item_name]" class="form-control" style="min-width:150px" value="{{ $d->item_name }}"></td>
                <td><input type="text" name="items[{{ $idx }}][hsn_code]" class="form-control" style="width:80px" value="{{ $d->hsn_code }}"></td>
                <td><input type="text" name="items[{{ $idx }}][unit]" class="form-control" style="width:60px" value="{{ $d->unit }}"></td>
                <td><input type="number" name="items[{{ $idx }}][qty]" class="form-control item-qty" step="0.0001" style="width:80px" oninput="calcRow({{ $idx }})" value="{{ $d->qty }}"></td>
                <td><input type="number" name="items[{{ $idx }}][rate]" class="form-control item-rate" step="0.0001" style="width:100px" oninput="calcRow({{ $idx }})" value="{{ $d->rate }}"></td>
                <td><input type="number" name="items[{{ $idx }}][amount]" class="form-control item-amount" step="0.0001" style="width:110px" readonly value="{{ $d->amount }}"></td>
                <td><input type="number" name="items[{{ $idx }}][discount]" class="form-control" step="0.0001" style="width:80px" value="{{ $d->discount }}"></td>
                <td><input type="number" name="items[{{ $idx }}][sgst_pct]" class="form-control item-sgst-pct" step="0.01" style="width:70px" oninput="calcRow({{ $idx }})" value="{{ $d->sgst_pct }}"></td>
                <td><input type="number" name="items[{{ $idx }}][cgst_pct]" class="form-control item-cgst-pct" step="0.01" style="width:70px" oninput="calcRow({{ $idx }})" value="{{ $d->cgst_pct }}"></td>
                <td><input type="number" name="items[{{ $idx }}][igst_pct]" class="form-control item-igst-pct" step="0.01" style="width:70px" oninput="calcRow({{ $idx }})" value="{{ $d->igst_pct }}"></td>
                <td><input type="number" name="items[{{ $idx }}][sgst]" class="form-control item-sgst" step="0.0001" style="width:90px" readonly value="{{ $d->sgst }}"></td>
                <td><input type="number" name="items[{{ $idx }}][cgst]" class="form-control item-cgst" step="0.0001" style="width:90px" readonly value="{{ $d->cgst }}"></td>
                <td><input type="number" name="items[{{ $idx }}][igst]" class="form-control item-igst" step="0.0001" style="width:90px" readonly value="{{ $d->igst }}"></td>
                <td><button type="button" class="btn-outline-gold btn-sm-gold" onclick="removeRow({{ $idx }})" style="color:#f87171">✕</button></td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        <button type="button" class="btn-outline-gold btn-sm-gold mt-2" onclick="addRow()">+ Add Row</button>

        <div class="row g-3 mt-3 justify-content-end">
            <div class="col-md-4">
                <div style="background:rgba(212,175,55,0.05);border:1px solid var(--border-gold);border-radius:6px;padding:14px">
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">Taxable Amount</span><strong id="dispTaxable" class="text-gold">{{ number_format($sale->taxable_amount, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">Discount</span>
                        <input type="number" name="discount" id="inp_discount" class="form-control" style="width:110px;text-align:right" value="{{ $sale->discount }}" step="0.0001" oninput="calcTotals()">
                    </div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">SGST</span><strong id="dispSgst" class="text-gold">{{ number_format($sale->sgst, 2) }}</strong><input type="hidden" name="sgst" id="inp_sgst" value="{{ $sale->sgst }}"></div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">CGST</span><strong id="dispCgst" class="text-gold">{{ number_format($sale->cgst, 2) }}</strong><input type="hidden" name="cgst" id="inp_cgst" value="{{ $sale->cgst }}"></div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">IGST</span><strong id="dispIgst" class="text-gold">{{ number_format($sale->igst, 2) }}</strong><input type="hidden" name="igst" id="inp_igst" value="{{ $sale->igst }}"></div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">Other Charges</span>
                        <input type="number" name="other_charges" id="inp_other" class="form-control" style="width:110px;text-align:right" value="{{ $sale->other_charges }}" step="0.0001" oninput="calcTotals()">
                    </div>
                    <hr style="border-color:var(--border-gold)">
                    <div class="d-flex justify-content-between"><span class="form-label" style="font-size:0.85rem">NET AMOUNT</span><strong id="dispNet" style="color:var(--gold-light);font-size:1.1rem">{{ number_format($sale->net_amount, 2) }}</strong></div>
                    <input type="hidden" name="taxable_amount" id="inp_taxable" value="{{ $sale->taxable_amount }}">
                    <input type="hidden" name="net_amount" id="inp_net" value="{{ $sale->net_amount }}">
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <label class="form-label">Received Amount</label>
                <input type="number" name="received_amount" class="form-control" value="{{ $sale->received_amount }}" step="0.0001">
            </div>
            <div class="col-md-9">
                <label class="form-label">Narration</label>
                <input type="text" name="narration" class="form-control" value="{{ $sale->narration }}">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn-gold">Update Invoice</button>
            <a href="{{ route('sales.show', $sale) }}" class="btn-outline-gold ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let rowCount = {{ $sale->details->count() }};
function addRow() {
    const i = rowCount++;
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + i;
    tr.innerHTML = `<td>${tbody.rows.length+1}</td>
        <td><input type="text" name="items[${i}][item_name]" class="form-control" style="min-width:150px"></td>
        <td><input type="text" name="items[${i}][hsn_code]" class="form-control" style="width:80px"></td>
        <td><input type="text" name="items[${i}][unit]" class="form-control" style="width:60px" value="NOS"></td>
        <td><input type="number" name="items[${i}][qty]" class="form-control item-qty" step="0.0001" style="width:80px" oninput="calcRow(${i})" value="1"></td>
        <td><input type="number" name="items[${i}][rate]" class="form-control item-rate" step="0.0001" style="width:100px" oninput="calcRow(${i})" value="0"></td>
        <td><input type="number" name="items[${i}][amount]" class="form-control item-amount" step="0.0001" style="width:110px" readonly value="0"></td>
        <td><input type="number" name="items[${i}][discount]" class="form-control" step="0.0001" style="width:80px" value="0"></td>
        <td><input type="number" name="items[${i}][sgst_pct]" class="form-control item-sgst-pct" step="0.01" style="width:70px" oninput="calcRow(${i})" value="0"></td>
        <td><input type="number" name="items[${i}][cgst_pct]" class="form-control item-cgst-pct" step="0.01" style="width:70px" oninput="calcRow(${i})" value="0"></td>
        <td><input type="number" name="items[${i}][igst_pct]" class="form-control item-igst-pct" step="0.01" style="width:70px" oninput="calcRow(${i})" value="0"></td>
        <td><input type="number" name="items[${i}][sgst]" class="form-control item-sgst" step="0.0001" style="width:90px" readonly value="0"></td>
        <td><input type="number" name="items[${i}][cgst]" class="form-control item-cgst" step="0.0001" style="width:90px" readonly value="0"></td>
        <td><input type="number" name="items[${i}][igst]" class="form-control item-igst" step="0.0001" style="width:90px" readonly value="0"></td>
        <td><button type="button" class="btn-outline-gold btn-sm-gold" onclick="removeRow(${i})" style="color:#f87171">✕</button></td>`;
    tbody.appendChild(tr);
}
function removeRow(i) {
    const row = document.getElementById('row_'+i);
    if (row) row.remove();
    calcTotals();
}
function calcRow(i) {
    const row = document.getElementById('row_'+i);
    if (!row) return;
    const qty=parseFloat(row.querySelector('.item-qty')?.value)||0;
    const rate=parseFloat(row.querySelector('.item-rate')?.value)||0;
    const amt=qty*rate;
    const sp=parseFloat(row.querySelector('.item-sgst-pct')?.value)||0;
    const cp=parseFloat(row.querySelector('.item-cgst-pct')?.value)||0;
    const ip=parseFloat(row.querySelector('.item-igst-pct')?.value)||0;
    if(row.querySelector('.item-amount'))row.querySelector('.item-amount').value=amt.toFixed(4);
    if(row.querySelector('.item-sgst'))row.querySelector('.item-sgst').value=(amt*sp/100).toFixed(4);
    if(row.querySelector('.item-cgst'))row.querySelector('.item-cgst').value=(amt*cp/100).toFixed(4);
    if(row.querySelector('.item-igst'))row.querySelector('.item-igst').value=(amt*ip/100).toFixed(4);
    calcTotals();
}
function calcTotals() {
    let t=0,s=0,c=0,ig=0;
    document.querySelectorAll('#itemsBody tr').forEach(r=>{
        t+=parseFloat(r.querySelector('.item-amount')?.value)||0;
        s+=parseFloat(r.querySelector('.item-sgst')?.value)||0;
        c+=parseFloat(r.querySelector('.item-cgst')?.value)||0;
        ig+=parseFloat(r.querySelector('.item-igst')?.value)||0;
    });
    const d=parseFloat(document.getElementById('inp_discount').value)||0;
    const o=parseFloat(document.getElementById('inp_other').value)||0;
    const n=t-d+s+c+ig+o;
    document.getElementById('dispTaxable').textContent=t.toFixed(2);
    document.getElementById('dispSgst').textContent=s.toFixed(2);
    document.getElementById('dispCgst').textContent=c.toFixed(2);
    document.getElementById('dispIgst').textContent=ig.toFixed(2);
    document.getElementById('dispNet').textContent=n.toFixed(2);
    document.getElementById('inp_taxable').value=t.toFixed(4);
    document.getElementById('inp_sgst').value=s.toFixed(4);
    document.getElementById('inp_cgst').value=c.toFixed(4);
    document.getElementById('inp_igst').value=ig.toFixed(4);
    document.getElementById('inp_net').value=n.toFixed(4);
}
function toggleBankFields() {
    const mode=document.getElementById('payment_mode').value;
    document.getElementById('bankFields').style.display=(mode==='bank'||mode==='cheque')?'flex':'none';
}
</script>
@endpush
