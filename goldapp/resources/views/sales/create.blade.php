@extends('layouts.app')
@section('title','New Sales Bill')
@section('page-title','New Sales Bill')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ New Sales Bill &nbsp;<small style="font-size:0.75rem;color:var(--text-muted-gold)">{{ $nextSlno }}</small></h5>
        <a href="{{ route('sales.index') }}" class="btn-outline-gold btn-sm-gold">← Back</a>
    </div>

    <form method="POST" action="{{ route('sales.store') }}" id="salesForm">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Bill Date *</label>
                <input type="date" name="billdate" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Customer *</label>
                <select name="customer_id" id="customer_id" class="form-select" required>
                    <option value="">-- Select Customer --</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" id="payment_mode" class="form-select" onchange="toggleBank()">
                    <option value="credit">Credit (No Payment)</option>
                    <option value="cash">Cash</option>
                    <option value="bank">Bank/Cheque</option>
                </select>
            </div>
            <div class="col-md-4" id="bankDiv" style="display:none">
                <label class="form-label">Bank Account</label>
                <select name="bank_account_id" class="form-select">
                    <option value="">-- Select Bank --</option>
                    @foreach($bankAccounts as $b)
                    <option value="{{ $b->id }}">{{ $b->code }} - {{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Line Items -->
        <div class="section-title">◆ Item Details</div>
        <div class="table-responsive">
        <table class="table-gold items-table w-100" id="itemsTable">
            <thead>
                <tr>
                    <th style="width:30px">#</th>
                    <th>Item Code</th>
                    <th style="min-width:180px">Item Name</th>
                    <th>Purity</th>
                    <th>Gross Wt</th>
                    <th>Net Wt</th>
                    <th>Rate/gm</th>
                    <th>HMC</th>
                    <th>Amount</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="itemsBody">
                <tr class="item-row">
                    <td class="row-num">1</td>
                    <td><input type="text" name="items[0][item_code]" class="form-control" placeholder="Code"></td>
                    <td><input type="text" name="items[0][item_name]" class="form-control" placeholder="Item Name"></td>
                    <td><input type="text" name="items[0][purity]" class="form-control" placeholder="22K"></td>
                    <td><input type="number" step="0.001" name="items[0][gross_wt]" class="form-control calc-field" placeholder="0.000" onchange="calcRow(this)"></td>
                    <td><input type="number" step="0.001" name="items[0][net_wt]" class="form-control" placeholder="0.000"></td>
                    <td><input type="number" step="0.01" name="items[0][rate]" class="form-control calc-field" placeholder="0.00" onchange="calcRow(this)"></td>
                    <td><input type="number" step="0.01" name="items[0][hmc]" class="form-control hmc-field" placeholder="0.00" onchange="calcTotals()"></td>
                    <td><input type="number" step="0.01" name="items[0][amount]" class="form-control amount-field" placeholder="0.00" readonly style="background:rgba(212,175,55,0.05)!important"></td>
                    <td><button type="button" onclick="removeRow(this)" style="background:rgba(248,113,113,0.12);color:#f87171;border:1px solid rgba(248,113,113,0.3);border-radius:4px;padding:4px 8px;cursor:pointer;font-size:0.75rem">✕</button></td>
                </tr>
            </tbody>
        </table>
        </div>
        <button type="button" class="btn-outline-gold btn-sm-gold mt-2" onclick="addRow()">+ Add Row</button>

        <!-- Totals -->
        <div class="row g-3 mt-3">
            <div class="col-md-6">
                <label class="form-label">Narration</label>
                <textarea name="narration" class="form-control" rows="2" placeholder="Optional note..."></textarea>
            </div>
            <div class="col-md-6">
                <table class="w-100" style="font-size:0.85rem">
                    <tr>
                        <td class="form-label py-1">Gross Amount</td>
                        <td class="text-end"><input type="number" step="0.01" name="gross_amount_display" id="grossDisplay" class="form-control text-end" readonly style="width:140px;float:right;background:rgba(212,175,55,0.05)!important"></td>
                    </tr>
                    <tr>
                        <td class="form-label py-1">HMC (Making Charges)</td>
                        <td class="text-end"><input type="number" step="0.01" name="hmc" id="hmcTotal" class="form-control text-end" style="width:140px;float:right" placeholder="0.00" onchange="calcNetAmount()"></td>
                    </tr>
                    <tr>
                        <td class="form-label py-1">Discount</td>
                        <td class="text-end"><input type="number" step="0.01" name="discount" id="discount" class="form-control text-end" style="width:140px;float:right" placeholder="0.00" onchange="calcNetAmount()"></td>
                    </tr>
                    <tr>
                        <td class="form-label py-1">SGST %</td>
                        <td class="text-end d-flex gap-1 justify-content-end">
                            <input type="number" step="0.01" id="sgstPct" class="form-control text-end" style="width:70px" placeholder="0" onchange="calcGst()">
                            <input type="number" step="0.01" name="sgst" id="sgst" class="form-control text-end" style="width:130px" placeholder="0.00" onchange="calcNetAmount()">
                        </td>
                    </tr>
                    <tr>
                        <td class="form-label py-1">CGST %</td>
                        <td class="text-end d-flex gap-1 justify-content-end">
                            <input type="number" step="0.01" id="cgstPct" class="form-control text-end" style="width:70px" placeholder="0" onchange="calcGst()">
                            <input type="number" step="0.01" name="cgst" id="cgst" class="form-control text-end" style="width:130px" placeholder="0.00" onchange="calcNetAmount()">
                        </td>
                    </tr>
                    <tr>
                        <td class="form-label py-1">IGST %</td>
                        <td class="text-end d-flex gap-1 justify-content-end">
                            <input type="number" step="0.01" id="igstPct" class="form-control text-end" style="width:70px" placeholder="0" onchange="calcGst()">
                            <input type="number" step="0.01" name="igst" id="igst" class="form-control text-end" style="width:130px" placeholder="0.00" onchange="calcNetAmount()">
                        </td>
                    </tr>
                    <tr>
                        <td class="form-label py-1">TCS</td>
                        <td class="text-end"><input type="number" step="0.01" name="tcs" id="tcs" class="form-control text-end" style="width:140px;float:right" placeholder="0.00" onchange="calcNetAmount()"></td>
                    </tr>
                    <tr>
                        <td class="form-label py-1">Exchange (Old Gold)</td>
                        <td class="text-end"><input type="number" step="0.01" name="exchange_amount" id="exchange" class="form-control text-end" style="width:140px;float:right" placeholder="0.00" onchange="calcNetAmount()"></td>
                    </tr>
                    <tr>
                        <td class="form-label py-1">Sales Return</td>
                        <td class="text-end"><input type="number" step="0.01" name="sales_return" id="salesReturn" class="form-control text-end" style="width:140px;float:right" placeholder="0.00" onchange="calcNetAmount()"></td>
                    </tr>
                    <tr style="border-top:1px solid var(--border-gold)">
                        <td class="py-2" style="color:var(--gold);font-weight:600">NET AMOUNT</td>
                        <td class="text-end"><input type="number" step="0.01" name="net_amount_display" id="netDisplay" class="form-control text-end fw-bold" readonly style="width:140px;float:right;color:var(--gold-light)!important;background:rgba(212,175,55,0.08)!important;font-size:1rem"></td>
                    </tr>
                    <tr>
                        <td class="form-label py-1">Received Amount</td>
                        <td class="text-end"><input type="number" step="0.01" name="received_amount" id="received" class="form-control text-end" style="width:140px;float:right" placeholder="0.00"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn-gold">◆ Save Sales Bill</button>
            <a href="{{ route('sales.index') }}" class="btn-outline-gold">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let rowCount = 1;

function toggleBank() {
    const mode = document.getElementById('payment_mode').value;
    document.getElementById('bankDiv').style.display = mode === 'bank' ? 'block' : 'none';
}

function calcRow(el) {
    const row = el.closest('tr');
    const grossWt = parseFloat(row.querySelector('[name*="gross_wt"]').value) || 0;
    const rate    = parseFloat(row.querySelector('[name*="rate"]').value) || 0;
    const amount  = grossWt * rate;
    row.querySelector('.amount-field').value = amount.toFixed(2);
    calcTotals();
}

function calcTotals() {
    let gross = 0, hmc = 0;
    document.querySelectorAll('.amount-field').forEach(f => gross += parseFloat(f.value)||0);
    document.querySelectorAll('.hmc-field').forEach(f => hmc += parseFloat(f.value)||0);
    document.getElementById('grossDisplay').value = gross.toFixed(2);
    const hmcInput = document.getElementById('hmcTotal');
    if (!hmcInput.value || hmcInput.value == 0) hmcInput.value = hmc.toFixed(2);
    calcNetAmount();
}

function calcGst() {
    const gross   = parseFloat(document.getElementById('grossDisplay').value) || 0;
    const sgstPct = parseFloat(document.getElementById('sgstPct').value) || 0;
    const cgstPct = parseFloat(document.getElementById('cgstPct').value) || 0;
    const igstPct = parseFloat(document.getElementById('igstPct').value) || 0;
    document.getElementById('sgst').value = (gross * sgstPct / 100).toFixed(2);
    document.getElementById('cgst').value = (gross * cgstPct / 100).toFixed(2);
    document.getElementById('igst').value = (gross * igstPct / 100).toFixed(2);
    calcNetAmount();
}

function calcNetAmount() {
    const gross    = parseFloat(document.getElementById('grossDisplay').value) || 0;
    const hmc      = parseFloat(document.getElementById('hmcTotal').value)    || 0;
    const disc     = parseFloat(document.getElementById('discount').value)    || 0;
    const sgst     = parseFloat(document.getElementById('sgst').value)        || 0;
    const cgst     = parseFloat(document.getElementById('cgst').value)        || 0;
    const igst     = parseFloat(document.getElementById('igst').value)        || 0;
    const tcs      = parseFloat(document.getElementById('tcs').value)         || 0;
    const exch     = parseFloat(document.getElementById('exchange').value)    || 0;
    const sret     = parseFloat(document.getElementById('salesReturn').value) || 0;
    const net      = gross + hmc - disc + sgst + cgst + igst + tcs - exch - sret;
    document.getElementById('netDisplay').value = Math.round(net).toFixed(2);
}

function addRow() {
    const tbody = document.getElementById('itemsBody');
    const idx   = rowCount++;
    const tr    = document.createElement('tr');
    tr.className = 'item-row';
    tr.innerHTML = `
        <td class="row-num">${tbody.querySelectorAll('tr').length + 1}</td>
        <td><input type="text" name="items[${idx}][item_code]" class="form-control" placeholder="Code"></td>
        <td><input type="text" name="items[${idx}][item_name]" class="form-control" placeholder="Item Name"></td>
        <td><input type="text" name="items[${idx}][purity]" class="form-control" placeholder="22K"></td>
        <td><input type="number" step="0.001" name="items[${idx}][gross_wt]" class="form-control calc-field" placeholder="0.000" onchange="calcRow(this)"></td>
        <td><input type="number" step="0.001" name="items[${idx}][net_wt]" class="form-control" placeholder="0.000"></td>
        <td><input type="number" step="0.01" name="items[${idx}][rate]" class="form-control calc-field" placeholder="0.00" onchange="calcRow(this)"></td>
        <td><input type="number" step="0.01" name="items[${idx}][hmc]" class="form-control hmc-field" placeholder="0.00" onchange="calcTotals()"></td>
        <td><input type="number" step="0.01" name="items[${idx}][amount]" class="form-control amount-field" placeholder="0.00" readonly style="background:rgba(212,175,55,0.05)!important"></td>
        <td><button type="button" onclick="removeRow(this)" style="background:rgba(248,113,113,0.12);color:#f87171;border:1px solid rgba(248,113,113,0.3);border-radius:4px;padding:4px 8px;cursor:pointer;font-size:0.75rem">✕</button></td>
    `;
    tbody.appendChild(tr);
    renumber();
}

function removeRow(btn) {
    btn.closest('tr').remove();
    renumber();
    calcTotals();
}

function renumber() {
    document.querySelectorAll('.row-num').forEach((el,i) => el.textContent = i+1);
}
</script>
@endpush
