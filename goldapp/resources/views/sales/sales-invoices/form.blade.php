@extends('layouts.app')
@section('title', isset($salesInvoice) ? 'Edit Invoice' : 'New Invoice')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('sales-invoices.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">{{ isset($salesInvoice) ? 'Edit: '.$salesInvoice->invoice_no : 'New Sales Invoice ('.$slno.')' }}</h4>
  </div>
  @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ isset($salesInvoice) ? route('sales-invoices.update',$salesInvoice) : route('sales-invoices.store') }}">
    @csrf @if(isset($salesInvoice)) @method('PUT') @endif
    <div class="card bg-dark border-secondary p-3 mb-3">
      <div class="row g-3">
        <div class="col-md-2"><label class="form-label text-light">Invoice Date <span class="text-danger">*</span></label>
          <input type="date" name="invoice_date" class="form-control bg-dark text-light border-secondary" value="{{ old('invoice_date',$salesInvoice->invoice_date?->format('Y-m-d')??today()->toDateString()) }}" required></div>
        <div class="col-md-3"><label class="form-label text-light">Customer</label>
          <select name="customer_id" class="form-select bg-dark text-light border-secondary" onchange="fillCustName(this)">
            <option value="">-- Select --</option>
            @foreach($customers as $c)
            <option value="{{ $c->id }}" data-name="{{ $c->name }}" {{ old('customer_id',$salesInvoice->customer_id??'')==$c->id?'selected':'' }}>{{ $c->name }}</option>
            @endforeach
          </select></div>
        <div class="col-md-3"><label class="form-label text-light">Customer Name <span class="text-danger">*</span></label>
          <input type="text" name="customer_name" id="custName" class="form-control bg-dark text-light border-secondary" value="{{ old('customer_name',$salesInvoice->customer_name??'') }}" required></div>
        <div class="col-md-2"><label class="form-label text-light">Channel</label>
          <select name="channel" class="form-select bg-dark text-light border-secondary">
            @foreach(['retail','wholesale','distributor','online','export'] as $ch)
            <option value="{{ $ch }}" {{ old('channel',$salesInvoice->channel??'retail')==$ch?'selected':'' }}>{{ ucfirst($ch) }}</option>
            @endforeach
          </select></div>
        <div class="col-md-2"><label class="form-label text-light">Sales Order</label>
          <select name="so_id" class="form-select bg-dark text-light border-secondary">
            <option value="">-- None --</option>
            @foreach($salesOrders as $so)
            <option value="{{ $so->id }}" {{ old('so_id',$salesInvoice->so_id??'')==$so->id?'selected':'' }}>{{ $so->so_no }} – {{ $so->customer_name }}</option>
            @endforeach
          </select></div>
        <div class="col-md-4"><label class="form-label text-light">Billing Address</label>
          <textarea name="billing_address" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('billing_address',$salesInvoice->billing_address??'') }}</textarea></div>
        <div class="col-md-4"><label class="form-label text-light">Shipping Address</label>
          <textarea name="shipping_address" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('shipping_address',$salesInvoice->shipping_address??'') }}</textarea></div>
        <div class="col-md-2"><label class="form-label text-light">Payment Mode</label>
          <select name="payment_mode" class="form-select bg-dark text-light border-secondary" id="payMode">
            @foreach(['cash','bank','cheque','credit','upi'] as $pm)
            <option value="{{ $pm }}" {{ old('payment_mode',$salesInvoice->payment_mode??'credit')==$pm?'selected':'' }}>{{ ucfirst($pm) }}</option>
            @endforeach
          </select></div>
        <div class="col-md-3" id="bankAccountDiv" style="display:none">
          <label class="form-label text-light">Bank Account</label>
          <select name="bank_account_id" class="form-select bg-dark text-light border-secondary">
            <option value="">-- Select --</option>
            @foreach($bankAccounts as $ba)
            <option value="{{ $ba->id }}" {{ old('bank_account_id',$salesInvoice->bank_account_id??'')==$ba->id?'selected':'' }}>{{ $ba->name }}</option>
            @endforeach
          </select></div>
        <div class="col-md-3" id="chequeDiv" style="display:none">
          <label class="form-label text-light">Cheque No</label>
          <input type="text" name="cheque_no" class="form-control bg-dark text-light border-secondary" value="{{ old('cheque_no',$salesInvoice->cheque_no??'') }}"></div>
        <div class="col-md-3"><label class="form-label text-light">UTR No</label>
          <input type="text" name="utr_no" class="form-control bg-dark text-light border-secondary" value="{{ old('utr_no',$salesInvoice->utr_no??'') }}"></div>
        <div class="col-md-3"><label class="form-label text-light">IRN No (e-Invoice)</label>
          <input type="text" name="irn_no" class="form-control bg-dark text-light border-secondary" value="{{ old('irn_no',$salesInvoice->irn_no??'') }}"></div>
        <div class="col-md-3"><label class="form-label text-light">E-Way Bill No</label>
          <input type="text" name="eway_bill_no" class="form-control bg-dark text-light border-secondary" value="{{ old('eway_bill_no',$salesInvoice->eway_bill_no??'') }}"></div>
        <div class="col-md-4"><label class="form-label text-light">Narration</label>
          <input type="text" name="narration" class="form-control bg-dark text-light border-secondary" value="{{ old('narration',$salesInvoice->narration??'') }}"></div>
      </div>
    </div>

    {{-- Items table includes batch_no and cost_rate extra cols --}}
    <div class="card bg-dark border-secondary p-3 mb-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="text-warning mb-0">Items</h6>
        <button type="button" class="btn btn-sm btn-outline-warning" id="addItemRow"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
      </div>
      <div class="table-responsive">
        <table class="table table-dark table-sm" id="salesItemsTable">
          <thead class="text-warning text-xs">
            <tr>
              <th>FG Item</th><th>Batch</th><th>HSN</th><th>Unit</th>
              <th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th>
              <th class="text-end">Disc%</th><th class="text-end">Disc Amt</th>
              <th class="text-end">SGST%</th><th class="text-end">SGST</th>
              <th class="text-end">CGST%</th><th class="text-end">CGST</th>
              <th class="text-end">IGST%</th><th class="text-end">IGST</th>
              <th class="text-end">Net</th><th class="text-end">Cost Rate</th><th></th>
            </tr>
          </thead>
          <tbody id="salesItemsBody">
            @php $invItems = old('items', isset($salesInvoice) ? $salesInvoice->items->toArray() : [[]]); @endphp
            @foreach($invItems as $i => $item)
            <tr class="sales-item-row">
              <td><select name="items[{{ $i }}][fg_id]" class="form-select form-select-sm bg-dark text-light border-secondary fg-sel" style="min-width:180px">
                <option value="">-- FG --</option>
                @foreach($fgList as $fg)
                <option value="{{ $fg->id }}" data-hsn="{{ $fg->hsn_code }}" data-sell="{{ $fg->selling_price }}" data-cost="{{ $fg->cost_price }}" data-unit="{{ $fg->unit?->name }}" data-sgst="9" data-cgst="9" data-igst="0" {{ ($item['fg_id']??'')==$fg->id?'selected':'' }}>{{ $fg->code }} – {{ $fg->name }}</option>
                @endforeach
              </select></td>
              <td><input type="text" name="items[{{ $i }}][batch_no]" class="form-control form-control-sm bg-dark text-light border-secondary" style="width:90px" value="{{ $item['batch_no']??'' }}"></td>
              <td><input type="text" name="items[{{ $i }}][hsn_code]" class="form-control form-control-sm bg-dark text-light border-secondary" style="width:70px" value="{{ $item['hsn_code']??'' }}"></td>
              <td><input type="text" name="items[{{ $i }}][unit]" class="form-control form-control-sm bg-dark text-light border-secondary si-unit" style="width:55px" value="{{ $item['unit']??'' }}"></td>
              <td><input type="number" step="0.0001" name="items[{{ $i }}][qty]" class="form-control form-control-sm bg-dark text-light border-secondary si-qty text-end" style="width:75px" value="{{ $item['qty']??'' }}"></td>
              <td><input type="number" step="0.0001" name="items[{{ $i }}][rate]" class="form-control form-control-sm bg-dark text-light border-secondary si-rate text-end" style="width:80px" value="{{ $item['rate']??'' }}"></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][amount]" class="form-control form-control-sm bg-dark text-light border-secondary si-amount text-end" style="width:80px" value="{{ $item['amount']??0 }}" readonly></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][discount_pct]" class="form-control form-control-sm bg-dark text-light border-secondary si-discpct text-end" style="width:55px" value="{{ $item['discount_pct']??0 }}"></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][discount_amount]" class="form-control form-control-sm bg-dark text-light border-secondary si-discamt text-end" style="width:75px" value="{{ $item['discount_amount']??0 }}" readonly></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][sgst_pct]" class="form-control form-control-sm bg-dark text-light border-secondary si-sgstpct text-end" style="width:50px" value="{{ $item['sgst_pct']??9 }}"></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][sgst]" class="form-control form-control-sm bg-dark text-light border-secondary si-sgst text-end" style="width:70px" value="{{ $item['sgst']??0 }}" readonly></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][cgst_pct]" class="form-control form-control-sm bg-dark text-light border-secondary si-cgstpct text-end" style="width:50px" value="{{ $item['cgst_pct']??9 }}"></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][cgst]" class="form-control form-control-sm bg-dark text-light border-secondary si-cgst text-end" style="width:70px" value="{{ $item['cgst']??0 }}" readonly></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][igst_pct]" class="form-control form-control-sm bg-dark text-light border-secondary si-igstpct text-end" style="width:50px" value="{{ $item['igst_pct']??0 }}"></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][igst]" class="form-control form-control-sm bg-dark text-light border-secondary si-igst text-end" style="width:70px" value="{{ $item['igst']??0 }}" readonly></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][net_amount]" class="form-control form-control-sm bg-dark text-light border-secondary si-net text-end" style="width:80px" value="{{ $item['net_amount']??0 }}" readonly></td>
              <td><input type="number" step="0.0001" name="items[{{ $i }}][cost_rate]" class="form-control form-control-sm bg-dark text-light border-secondary si-cost text-end" style="width:80px" value="{{ $item['cost_rate']??0 }}"></td>
              <td><button type="button" class="btn btn-xs btn-outline-danger remove-si-row"><i class="bi bi-x"></i></button></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="card bg-dark border-secondary p-3 mb-3">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label text-light">TCS Amount</label>
          <input type="number" step="0.01" name="tcs" id="tcsInput" class="form-control bg-dark text-light border-secondary" value="{{ old('tcs',$salesInvoice->tcs??0) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label text-light">Other Charges</label>
          <input type="number" step="0.01" name="other_charges" id="otherInput" class="form-control bg-dark text-light border-secondary" value="{{ old('other_charges',$salesInvoice->other_charges??0) }}">
        </div>
        <div class="col-md-3">
          <label class="form-label text-light">Amount Received</label>
          <input type="number" step="0.01" name="received_amount" id="receivedInput" class="form-control bg-dark text-light border-secondary" value="{{ old('received_amount',$salesInvoice->received_amount??0) }}">
        </div>
        <div class="col-md-3">
          <div class="p-2 border border-warning rounded text-end">
            <div class="text-muted small">Taxable: <span id="sumTaxable">0.00</span></div>
            <div class="text-muted small">Discount: <span id="sumDiscount">0.00</span></div>
            <div class="text-muted small">SGST: <span id="sumSgst">0.00</span></div>
            <div class="text-muted small">CGST: <span id="sumCgst">0.00</span></div>
            <div class="text-muted small">IGST: <span id="sumIgst">0.00</span></div>
            <div class="text-warning fw-bold">Net: ₹<span id="sumNet">0.00</span></div>
          </div>
        </div>
      </div>
    </div>

    <button class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Post Invoice</button>
    <a href="{{ route('sales-invoices.index') }}" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>
@include('sales._item_scripts')
<script>
// Add cost-rate auto-fill to fg-sel
document.querySelectorAll('.fg-sel').forEach(sel => {
  sel.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const row = this.closest('tr');
    const costEl = row?.querySelector('.si-cost');
    if (costEl) costEl.value = opt.dataset.cost || '0';
  });
});
document.getElementById('payMode')?.addEventListener('change', function() {
  const val = this.value;
  document.getElementById('bankAccountDiv').style.display = ['bank','upi','cheque'].includes(val) ? '' : 'none';
  document.getElementById('chequeDiv').style.display = val === 'cheque' ? '' : 'none';
});
document.getElementById('payMode')?.dispatchEvent(new Event('change'));
</script>
@endsection
