@extends('layouts.app')
@section('title','POS Billing')
@section('content')
<div class="container-fluid px-3 py-2">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="text-warning mb-0"><i class="bi bi-cash-register me-2"></i>POS – Session {{ $posSession->session_no }}</h5>
    <div class="d-flex gap-2">
      <span class="text-success small">Total Sales: ₹{{ number_format($posSession->total_sales,2) }}</span>
      <form method="POST" action="{{ route('pos.close',$posSession) }}">
        @csrf
        <input type="number" step="0.01" name="closing_cash" class="form-control form-control-sm bg-dark text-light border-secondary d-inline" style="width:110px" placeholder="Closing Cash">
        <button class="btn btn-sm btn-warning">Close</button>
      </form>
    </div>
  </div>
  @if(session('success'))<div class="alert alert-success py-1 small">{{ session('success') }}</div>@endif

  <div class="row g-3">
    <div class="col-md-7">
      <div class="card bg-dark border-secondary">
        <div class="card-body p-2">
          <form method="POST" action="{{ route('pos.save-bill',$posSession) }}" id="billForm">
            @csrf
            <div class="row g-2 mb-2">
              <div class="col-md-5">
                <input type="text" name="customer_name" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Customer Name (optional)" value="Walk-in">
              </div>
              <div class="col-md-3">
                <select name="payment_mode" class="form-select form-select-sm bg-dark text-light border-secondary">
                  <option value="cash">Cash</option>
                  <option value="card">Card</option>
                  <option value="upi">UPI</option>
                  <option value="split">Split</option>
                </select>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-dark table-sm" id="posTable">
                <thead class="text-warning"><tr><th>Item</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Disc%</th><th class="text-end">SGST%</th><th class="text-end">CGST%</th><th class="text-end">Net</th><th></th></tr></thead>
                <tbody id="posBody">
                  <tr class="pos-row">
                    <td><select name="items[0][fg_id]" class="form-select form-select-sm bg-dark text-light border-secondary pos-fg">
                      <option value="">-- Item --</option>
                      @foreach($fgList as $fg)
                      <option value="{{ $fg->id }}" data-sell="{{ $fg->selling_price }}" data-unit="{{ $fg->unit?->name }}">{{ $fg->code }} – {{ $fg->name }}</option>
                      @endforeach
                    </select></td>
                    <td><input type="number" step="0.001" name="items[0][qty]" class="form-control form-control-sm bg-dark text-light border-secondary pos-qty text-end" style="width:70px" value="1"></td>
                    <td><input type="number" step="0.01" name="items[0][rate]" class="form-control form-control-sm bg-dark text-light border-secondary pos-rate text-end" style="width:80px"></td>
                    <td><input type="number" step="0.01" name="items[0][discount_pct]" class="form-control form-control-sm bg-dark text-light border-secondary pos-disc text-end" style="width:55px" value="0"></td>
                    <td><input type="number" step="0.01" name="items[0][sgst_pct]" class="form-control form-control-sm bg-dark text-light border-secondary pos-sgst text-end" style="width:55px" value="9"></td>
                    <td><input type="number" step="0.01" name="items[0][cgst_pct]" class="form-control form-control-sm bg-dark text-light border-secondary pos-cgst text-end" style="width:55px" value="9"></td>
                    <td><span class="pos-net text-end d-block">0.00</span></td>
                    <td><button type="button" class="btn btn-xs btn-outline-danger remove-pos"><i class="bi bi-x"></i></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <button type="button" class="btn btn-sm btn-outline-warning mb-2" id="addPosRow"><i class="bi bi-plus-lg me-1"></i>Add Item</button>

            <div class="row g-2 align-items-end mt-1">
              <div class="col-3">
                <label class="form-label text-light small">Discount</label>
                <input type="number" step="0.01" name="discount" id="posDiscount" class="form-control form-control-sm bg-dark text-light border-secondary" value="0">
              </div>
              <div class="col-3">
                <label class="form-label text-light small">Amount Received</label>
                <input type="number" step="0.01" name="received_amount" id="posReceived" class="form-control form-control-sm bg-dark text-light border-secondary" value="0">
              </div>
              <div class="col-6">
                <div class="bg-dark border border-warning rounded p-2 text-end">
                  <div class="text-muted small">Taxable: ₹<span id="posTaxable">0.00</span></div>
                  <div class="text-muted small">Tax: ₹<span id="posTax">0.00</span></div>
                  <div class="text-warning fw-bold">Net: ₹<span id="posNet">0.00</span></div>
                  <div class="text-success small">Change: ₹<span id="posChange">0.00</span></div>
                </div>
              </div>
              <div class="col-12 mt-2">
                <button type="submit" class="btn btn-success w-100 btn-lg"><i class="bi bi-check-circle me-1"></i>COMPLETE SALE</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <div class="card bg-dark border-secondary">
        <div class="card-header text-warning small">Today's Bills ({{ $bills->count() }})</div>
        <div class="card-body p-1">
          @forelse($bills as $b)
          <div class="d-flex justify-content-between border-bottom border-secondary py-1 small">
            <span><code>{{ $b->bill_no }}</code> {{ $b->customer_name }}</span>
            <span class="text-warning">₹{{ number_format($b->net_amount,2) }}</span>
          </div>
          @empty
          <p class="text-muted small text-center py-2">No bills yet.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let posIdx = 1;
function calcPosRow(row) {
  const qty  = parseFloat(row.querySelector('.pos-qty')?.value)||0;
  const rate = parseFloat(row.querySelector('.pos-rate')?.value)||0;
  const disc = parseFloat(row.querySelector('.pos-disc')?.value)||0;
  const sgst = parseFloat(row.querySelector('.pos-sgst')?.value)||0;
  const cgst = parseFloat(row.querySelector('.pos-cgst')?.value)||0;
  const amt  = qty * rate * (1 - disc/100);
  const net  = amt * (1 + (sgst+cgst)/100);
  const el   = row.querySelector('.pos-net');
  if (el) el.textContent = net.toFixed(2);
  updatePosTotals();
}
function updatePosTotals() {
  let taxable=0, tax=0, net=0;
  document.querySelectorAll('.pos-row').forEach(row => {
    const qty  = parseFloat(row.querySelector('.pos-qty')?.value)||0;
    const rate = parseFloat(row.querySelector('.pos-rate')?.value)||0;
    const disc = parseFloat(row.querySelector('.pos-disc')?.value)||0;
    const sgst = parseFloat(row.querySelector('.pos-sgst')?.value)||0;
    const cgst = parseFloat(row.querySelector('.pos-cgst')?.value)||0;
    const amt  = qty * rate * (1 - disc/100);
    const t    = amt * (sgst+cgst)/100;
    taxable += amt; tax += t; net += amt + t;
  });
  const extraDisc = parseFloat(document.getElementById('posDiscount')?.value)||0;
  net -= extraDisc;
  document.getElementById('posTaxable').textContent = taxable.toFixed(2);
  document.getElementById('posTax').textContent     = tax.toFixed(2);
  document.getElementById('posNet').textContent     = net.toFixed(2);
  const recv = parseFloat(document.getElementById('posReceived')?.value)||0;
  document.getElementById('posChange').textContent  = Math.max(0, recv - net).toFixed(2);
}
function bindPosRow(row) {
  row.querySelectorAll('.pos-qty,.pos-rate,.pos-disc,.pos-sgst,.pos-cgst').forEach(el => el.addEventListener('input', () => calcPosRow(row)));
  row.querySelector('.pos-fg')?.addEventListener('change', function() {
    const opt  = this.options[this.selectedIndex];
    const rEl  = row.querySelector('.pos-rate');
    if (rEl) rEl.value = opt.dataset.sell||'';
    calcPosRow(row);
  });
  row.querySelector('.remove-pos')?.addEventListener('click', () => { row.remove(); updatePosTotals(); });
}
document.querySelectorAll('.pos-row').forEach(bindPosRow);
document.getElementById('addPosRow')?.addEventListener('click', function() {
  const tbody = document.getElementById('posBody');
  const tmpl  = tbody.querySelector('.pos-row').cloneNode(true);
  tmpl.querySelectorAll('input').forEach(i => { if (!i.readOnly) i.value = i.type==='number'&&i.classList.contains('pos-qty')?'1':''; });
  tmpl.querySelectorAll('[name]').forEach(el => { el.name = el.name.replace(/\[\d+\]/, '['+posIdx+']'); });
  tmpl.querySelector('.pos-net').textContent = '0.00';
  posIdx++;
  tbody.appendChild(tmpl);
  bindPosRow(tmpl);
});
document.getElementById('posDiscount')?.addEventListener('input', updatePosTotals);
document.getElementById('posReceived')?.addEventListener('input', updatePosTotals);
</script>
@endsection
