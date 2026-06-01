@extends('layouts.app')
@section('title', isset($dispatchOrder) ? 'Edit Dispatch Order' : 'New Dispatch Order')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('dispatch-orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">{{ isset($dispatchOrder) ? 'Edit: '.$dispatchOrder->do_no : 'New Dispatch Order ('.$slno.')' }}</h4>
  </div>
  @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ isset($dispatchOrder) ? route('dispatch-orders.update',$dispatchOrder) : route('dispatch-orders.store') }}">
    @csrf @if(isset($dispatchOrder)) @method('PUT') @endif
    <div class="card bg-dark border-secondary p-3 mb-3">
      <h6 class="text-warning">Header</h6>
      <div class="row g-3">
        <div class="col-md-2">
          <label class="form-label text-light">DO Date <span class="text-danger">*</span></label>
          <input type="date" name="do_date" class="form-control bg-dark text-light border-secondary" value="{{ old('do_date', $dispatchOrder->do_date?->format('Y-m-d') ?? today()->toDateString()) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Customer <span class="text-danger">*</span></label>
          <select name="customer_id" class="form-select bg-dark text-light border-secondary" id="customerId" onchange="setCustomerName(this)">
            <option value="">-- Select or type below --</option>
            @foreach($customers as $c)
            <option value="{{ $c->id }}" data-name="{{ $c->name }}" {{ old('customer_id',$dispatchOrder->customer_id??'')==$c->id?'selected':'' }}>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Customer Name <span class="text-danger">*</span></label>
          <input type="text" name="customer_name" id="customerName" class="form-control bg-dark text-light border-secondary" value="{{ old('customer_name',$dispatchOrder->customer_name??'') }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Sales Order</label>
          <select name="sales_order_id" class="form-select bg-dark text-light border-secondary">
            <option value="">-- None --</option>
            @foreach($salesOrders as $so)
            <option value="{{ $so->id }}" {{ old('sales_order_id',$dispatchOrder->sales_order_id??'')==$so->id?'selected':'' }}>{{ $so->so_no }} – {{ $so->customer_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Delivery Address</label>
          <textarea name="delivery_address" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('delivery_address',$dispatchOrder->delivery_address??'') }}</textarea>
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Driver Name</label>
          <input type="text" name="driver_name" class="form-control bg-dark text-light border-secondary" value="{{ old('driver_name',$dispatchOrder->driver_name??'') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Driver Phone</label>
          <input type="text" name="driver_phone" class="form-control bg-dark text-light border-secondary" value="{{ old('driver_phone',$dispatchOrder->driver_phone??'') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Dispatch Date</label>
          <input type="date" name="dispatch_date" class="form-control bg-dark text-light border-secondary" value="{{ old('dispatch_date',$dispatchOrder->dispatch_date?->format('Y-m-d')??'') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Narration</label>
          <input type="text" name="narration" class="form-control bg-dark text-light border-secondary" value="{{ old('narration',$dispatchOrder->narration??'') }}">
        </div>
      </div>
    </div>

    <div class="card bg-dark border-secondary p-3 mb-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="text-warning mb-0">Items</h6>
        <button type="button" class="btn btn-sm btn-outline-warning" id="addRow"><i class="bi bi-plus-lg me-1"></i>Add Item</button>
      </div>
      <div class="table-responsive">
        <table class="table table-dark table-sm" id="itemsTable">
          <thead class="text-warning">
            <tr><th>FG Item</th><th>Batch No</th><th>Warehouse</th><th>Unit</th><th class="text-end">Qty</th><th class="text-end">Cost Rate</th><th class="text-end">Cost Amt</th><th class="text-end">Sell Rate</th><th class="text-end">Sell Amt</th><th></th></tr>
          </thead>
          <tbody id="itemsBody">
            @php $existingItems = old('items', isset($dispatchOrder) ? $dispatchOrder->items->toArray() : [['fg_id'=>'','batch_no'=>'','warehouse_id'=>'','unit'=>'','qty'=>'','cost_rate'=>'','cost_amount'=>'','selling_rate'=>'','selling_amount'=>'']]); @endphp
            @foreach($existingItems as $i => $item)
            <tr class="item-row">
              <td>
                <select name="items[{{ $i }}][fg_id]" class="form-select form-select-sm bg-dark text-light border-secondary fg-select">
                  <option value="">-- FG --</option>
                  @foreach($fgList as $fg)
                  <option value="{{ $fg->id }}" data-rate="{{ $fg->cost_price }}" data-sell="{{ $fg->selling_price }}" data-unit="{{ $fg->unit?->name }}" {{ ($item['fg_id']??'')==$fg->id?'selected':'' }}>{{ $fg->code }} – {{ $fg->name }}</option>
                  @endforeach
                </select>
              </td>
              <td><input type="text" name="items[{{ $i }}][batch_no]" class="form-control form-control-sm bg-dark text-light border-secondary" value="{{ $item['batch_no']??'' }}"></td>
              <td>
                <select name="items[{{ $i }}][warehouse_id]" class="form-select form-select-sm bg-dark text-light border-secondary">
                  <option value="">-- WH --</option>
                  @foreach($warehouses as $w)
                  <option value="{{ $w->id }}" {{ ($item['warehouse_id']??'')==$w->id?'selected':'' }}>{{ $w->name }}</option>
                  @endforeach
                </select>
              </td>
              <td><input type="text" name="items[{{ $i }}][unit]" class="form-control form-control-sm bg-dark text-light border-secondary" style="width:80px" value="{{ $item['unit']??'' }}"></td>
              <td><input type="number" step="0.0001" name="items[{{ $i }}][qty]" class="form-control form-control-sm bg-dark text-light border-secondary item-qty text-end" style="width:90px" value="{{ $item['qty']??'' }}"></td>
              <td><input type="number" step="0.0001" name="items[{{ $i }}][cost_rate]" class="form-control form-control-sm bg-dark text-light border-secondary item-cost-rate text-end" style="width:100px" value="{{ $item['cost_rate']??'' }}"></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][cost_amount]" class="form-control form-control-sm bg-dark text-light border-secondary item-cost-amt text-end" style="width:100px" value="{{ $item['cost_amount']??'' }}" readonly></td>
              <td><input type="number" step="0.0001" name="items[{{ $i }}][selling_rate]" class="form-control form-control-sm bg-dark text-light border-secondary item-sell-rate text-end" style="width:100px" value="{{ $item['selling_rate']??'' }}"></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][selling_amount]" class="form-control form-control-sm bg-dark text-light border-secondary item-sell-amt text-end" style="width:100px" value="{{ $item['selling_amount']??'' }}" readonly></td>
              <td><button type="button" class="btn btn-xs btn-outline-danger remove-row"><i class="bi bi-x"></i></button></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <button class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Save</button>
    <a href="{{ route('dispatch-orders.index') }}" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>
<script>
let rowIdx = {{ count($existingItems ?? []) }};
document.getElementById('addRow').addEventListener('click', function() {
  const tbody = document.getElementById('itemsBody');
  const firstRow = tbody.querySelector('tr');
  const newRow = firstRow.cloneNode(true);
  newRow.querySelectorAll('input').forEach(i => i.value = '');
  newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
  newRow.querySelectorAll('[name]').forEach(el => {
    el.name = el.name.replace(/\[\d+\]/, '[' + rowIdx + ']');
  });
  rowIdx++;
  tbody.appendChild(newRow);
  bindRowEvents(newRow);
});

function calcRow(row) {
  const qty  = parseFloat(row.querySelector('.item-qty')?.value) || 0;
  const cr   = parseFloat(row.querySelector('.item-cost-rate')?.value) || 0;
  const sr   = parseFloat(row.querySelector('.item-sell-rate')?.value) || 0;
  const ca   = row.querySelector('.item-cost-amt');
  const sa   = row.querySelector('.item-sell-amt');
  if (ca) ca.value = (qty * cr).toFixed(2);
  if (sa) sa.value = (qty * sr).toFixed(2);
}

function bindRowEvents(row) {
  row.querySelectorAll('.item-qty,.item-cost-rate,.item-sell-rate').forEach(el => {
    el.addEventListener('input', () => calcRow(row));
  });
  row.querySelector('.fg-select')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const costRate = row.querySelector('.item-cost-rate');
    const sellRate = row.querySelector('.item-sell-rate');
    const unit     = row.querySelector('input[name*="[unit]"]');
    if (costRate) costRate.value = opt.dataset.rate || '';
    if (sellRate) sellRate.value = opt.dataset.sell || '';
    if (unit) unit.value = opt.dataset.unit || '';
    calcRow(row);
  });
  row.querySelector('.remove-row')?.addEventListener('click', () => row.remove());
}

document.querySelectorAll('.item-row').forEach(bindRowEvents);
document.querySelectorAll('.item-qty,.item-cost-rate,.item-sell-rate').forEach(el => {
  el.addEventListener('input', () => calcRow(el.closest('tr')));
});

function setCustomerName(sel) {
  const opt = sel.options[sel.selectedIndex];
  const inp = document.getElementById('customerName');
  if (inp && opt.dataset.name) inp.value = opt.dataset.name;
}
</script>
@endsection
