@extends('layouts.app')
@section('title','New Purchase Invoice')
@section('page-title','New Purchase Invoice')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ New Purchase Invoice &nbsp;<small style="font-size:0.75rem;color:var(--text-muted-gold)">{{ $nextSlno }}</small></h5>
        <a href="{{ route('purchase.index') }}" class="btn-outline-gold btn-sm-gold">← Back</a>
    </div>

    <form method="POST" action="{{ route('purchase.store') }}">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Invoice Date *</label>
                <input type="date" name="invoice_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Supplier *</label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">-- Select Supplier --</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}">{{ $s->code }} - {{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Supplier Bill No</label>
                <input type="text" name="supplier_bill_no" class="form-control" placeholder="Supplier invoice no">
            </div>
            <div class="col-md-2">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-select" onchange="toggleBankFields()">
                    <option value="credit">Credit</option>
                    <option value="cash">Cash</option>
                    <option value="bank">Bank</option>
                    <option value="cheque">Cheque</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3" id="bankFields" style="display:none">
            <div class="col-md-4">
                <label class="form-label">Bank Account</label>
                <select name="bank_account_id" class="form-select">
                    <option value="">-- Select Bank --</option>
                    @foreach($bankAccounts as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Cheque No</label>
                <input type="text" name="cheque_no" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Cheque Date</label>
                <input type="date" name="cheque_date" class="form-control">
            </div>
        </div>

        <div class="section-title mt-4">Line Items</div>
        <div class="table-responsive">
        <table class="items-table table-gold w-100">
            <thead>
                <tr><th>#</th><th>Item Name</th><th>HSN</th><th>Unit</th><th>Qty</th><th>Rate</th><th>Amount</th><th>Disc</th><th>SGST%</th><th>CGST%</th><th>IGST%</th><th>SGST</th><th>CGST</th><th>IGST</th><th></th></tr>
            </thead>
            <tbody id="itemsBody">
                <tr id="row_0">
                    <td>1</td>
                    <td><input type="text" name="items[0][item_name]" class="form-control" style="min-width:150px" required></td>
                    <td><input type="text" name="items[0][hsn_code]" class="form-control" style="width:80px"></td>
                    <td><input type="text" name="items[0][unit]" class="form-control" style="width:60px" value="KG"></td>
                    <td><input type="number" name="items[0][qty]" class="form-control item-qty" step="0.0001" style="width:80px" oninput="calcRow(0)" value="1"></td>
                    <td><input type="number" name="items[0][rate]" class="form-control item-rate" step="0.0001" style="width:100px" oninput="calcRow(0)" value="0"></td>
                    <td><input type="number" name="items[0][amount]" class="form-control item-amount" step="0.0001" style="width:110px" readonly value="0"></td>
                    <td><input type="number" name="items[0][discount]" class="form-control" step="0.0001" style="width:80px" value="0"></td>
                    <td><input type="number" name="items[0][sgst_pct]" class="form-control item-sgst-pct" step="0.01" style="width:70px" oninput="calcRow(0)" value="0"></td>
                    <td><input type="number" name="items[0][cgst_pct]" class="form-control item-cgst-pct" step="0.01" style="width:70px" oninput="calcRow(0)" value="0"></td>
                    <td><input type="number" name="items[0][igst_pct]" class="form-control item-igst-pct" step="0.01" style="width:70px" oninput="calcRow(0)" value="0"></td>
                    <td><input type="number" name="items[0][sgst]" class="form-control item-sgst" step="0.0001" style="width:90px" readonly value="0"></td>
                    <td><input type="number" name="items[0][cgst]" class="form-control item-cgst" step="0.0001" style="width:90px" readonly value="0"></td>
                    <td><input type="number" name="items[0][igst]" class="form-control item-igst" step="0.0001" style="width:90px" readonly value="0"></td>
                    <td><button type="button" class="btn-outline-gold btn-sm-gold" onclick="removeRow(0)" style="color:#f87171">✕</button></td>
                </tr>
            </tbody>
        </table>
        </div>
        <button type="button" class="btn-outline-gold btn-sm-gold mt-2" onclick="addRow()">+ Add Row</button>

        <div class="row g-3 mt-3 justify-content-end">
            <div class="col-md-4">
                <div style="background:rgba(212,175,55,0.05);border:1px solid var(--border-gold);border-radius:6px;padding:14px">
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">Taxable Amount</span><strong id="dispTaxable" class="text-gold">0.00</strong></div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">Discount</span><input type="number" name="discount" id="inp_discount" class="form-control" style="width:110px;text-align:right" value="0" step="0.0001" oninput="calcTotals()"></div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">SGST</span><strong id="dispSgst" class="text-gold">0.00</strong><input type="hidden" name="sgst" id="inp_sgst" value="0"></div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">CGST</span><strong id="dispCgst" class="text-gold">0.00</strong><input type="hidden" name="cgst" id="inp_cgst" value="0"></div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">IGST</span><strong id="dispIgst" class="text-gold">0.00</strong><input type="hidden" name="igst" id="inp_igst" value="0"></div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">TCS</span><input type="number" name="tcs" id="inp_tcs" class="form-control" style="width:110px;text-align:right" value="0" step="0.0001" oninput="calcTotals()"></div>
                    <div class="d-flex justify-content-between mb-1"><span class="form-label">Other Charges</span><input type="number" name="other_charges" id="inp_other" class="form-control" style="width:110px;text-align:right" value="0" step="0.0001" oninput="calcTotals()"></div>
                    <hr style="border-color:var(--border-gold)">
                    <div class="d-flex justify-content-between"><span class="form-label" style="font-size:0.85rem">NET AMOUNT</span><strong id="dispNet" style="color:var(--gold-light);font-size:1.1rem">0.00</strong></div>
                    <input type="hidden" name="taxable_amount" id="inp_taxable" value="0">
                    <input type="hidden" name="net_amount" id="inp_net" value="0">
                </div>
            </div>
        </div>

        <div class="row g-3 mt-2">
            <div class="col-md-3">
                <label class="form-label">Paid Amount</label>
                <input type="number" name="paid_amount" class="form-control" value="0" step="0.0001">
            </div>
            <div class="col-md-9">
                <label class="form-label">Narration</label>
                <input type="text" name="narration" class="form-control" placeholder="Remarks...">
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn-gold">Save Invoice</button>
            <a href="{{ route('purchase.index') }}" class="btn-outline-gold ms-2">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let rowCount = 1;
function addRow() {
    const i = rowCount++;
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + i;
    tr.innerHTML = `<td>${tbody.rows.length+1}</td>
        <td><input type="text" name="items[${i}][item_name]" class="form-control" style="min-width:150px"></td>
        <td><input type="text" name="items[${i}][hsn_code]" class="form-control" style="width:80px"></td>
        <td><input type="text" name="items[${i}][unit]" class="form-control" style="width:60px" value="KG"></td>
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
function removeRow(i) { const r = document.getElementById('row_'+i); if(r) r.remove(); calcTotals(); }
function calcRow(i) {
    const row = document.getElementById('row_'+i); if(!row) return;
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
    const tcs=parseFloat(document.getElementById('inp_tcs').value)||0;
    const o=parseFloat(document.getElementById('inp_other').value)||0;
    const n=t-d+s+c+ig+tcs+o;
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
