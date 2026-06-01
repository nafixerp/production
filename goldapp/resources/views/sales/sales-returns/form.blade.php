@extends('layouts.app')
@section('title', isset($salesReturn) ? 'Edit Return' : 'New Sales Return')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('sales-returns.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">{{ isset($salesReturn) ? 'Edit: '.$salesReturn->return_no : 'New Sales Return ('.$slno.')' }}</h4>
  </div>
  @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ isset($salesReturn) ? route('sales-returns.update',$salesReturn) : route('sales-returns.store') }}">
    @csrf @if(isset($salesReturn)) @method('PUT') @endif
    <div class="card bg-dark border-secondary p-3 mb-3">
      <div class="row g-3">
        <div class="col-md-2"><label class="form-label text-light">Return Date <span class="text-danger">*</span></label>
          <input type="date" name="return_date" class="form-control bg-dark text-light border-secondary" value="{{ old('return_date',$salesReturn->return_date?->format('Y-m-d')??today()->toDateString()) }}" required></div>
        <div class="col-md-3"><label class="form-label text-light">Customer</label>
          <select name="customer_id" class="form-select bg-dark text-light border-secondary" onchange="fillCustName(this)">
            <option value="">-- Select --</option>
            @foreach($customers as $c)
            <option value="{{ $c->id }}" data-name="{{ $c->name }}" {{ old('customer_id',$salesReturn->customer_id??'')==$c->id?'selected':'' }}>{{ $c->name }}</option>
            @endforeach
          </select></div>
        <div class="col-md-3"><label class="form-label text-light">Customer Name <span class="text-danger">*</span></label>
          <input type="text" name="customer_name" id="custName" class="form-control bg-dark text-light border-secondary" value="{{ old('customer_name',$salesReturn->customer_name??'') }}" required></div>
        <div class="col-md-2"><label class="form-label text-light">Against Invoice</label>
          <select name="invoice_id" class="form-select bg-dark text-light border-secondary">
            <option value="">-- None --</option>
            @foreach($invoices as $inv)
            <option value="{{ $inv->id }}" {{ old('invoice_id',$salesReturn->invoice_id??'')==$inv->id?'selected':'' }}>{{ $inv->invoice_no }}</option>
            @endforeach
          </select></div>
        <div class="col-md-2"><label class="form-label text-light">Channel</label>
          <select name="channel" class="form-select bg-dark text-light border-secondary">
            @foreach(['retail','wholesale','distributor','online','export'] as $ch)
            <option value="{{ $ch }}" {{ old('channel',$salesReturn->channel??'retail')==$ch?'selected':'' }}>{{ ucfirst($ch) }}</option>
            @endforeach
          </select></div>
        <div class="col-md-2"><label class="form-label text-light">Refund Mode</label>
          <select name="refund_mode" class="form-select bg-dark text-light border-secondary">
            @foreach(['credit_note','cash_refund','bank_refund'] as $rm)
            <option value="{{ $rm }}" {{ old('refund_mode',$salesReturn->refund_mode??'credit_note')==$rm?'selected':'' }}>{{ str_replace('_',' ',ucfirst($rm)) }}</option>
            @endforeach
          </select></div>
        <div class="col-md-6"><label class="form-label text-light">Reason <span class="text-danger">*</span></label>
          <textarea name="reason" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('reason',$salesReturn->reason??'') }}</textarea></div>
        <div class="col-md-6"><label class="form-label text-light">Narration</label>
          <textarea name="narration" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('narration',$salesReturn->narration??'') }}</textarea></div>
      </div>
    </div>

    <div class="card bg-dark border-secondary p-3 mb-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="text-warning mb-0">Return Items</h6>
        <button type="button" class="btn btn-sm btn-outline-warning" id="addReturnRow"><i class="bi bi-plus-lg me-1"></i>Add</button>
      </div>
      <div class="table-responsive">
        <table class="table table-dark table-sm">
          <thead class="text-warning"><tr><th>FG Item</th><th>Batch No</th><th>Unit</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th><th></th></tr></thead>
          <tbody id="returnItemsBody">
            @php $retItems = old('items', isset($salesReturn) ? $salesReturn->items->toArray() : [[]]); @endphp
            @foreach($retItems as $i => $item)
            <tr class="ret-row">
              <td><select name="items[{{ $i }}][fg_id]" class="form-select form-select-sm bg-dark text-light border-secondary ret-fg">
                <option value="">-- FG --</option>
                @foreach($fgList as $fg)
                <option value="{{ $fg->id }}" data-sell="{{ $fg->selling_price }}" data-unit="{{ $fg->unit?->name }}" {{ ($item['fg_id']??'')==$fg->id?'selected':'' }}>{{ $fg->code }} – {{ $fg->name }}</option>
                @endforeach
              </select></td>
              <td><input type="text" name="items[{{ $i }}][batch_no]" class="form-control form-control-sm bg-dark text-light border-secondary" value="{{ $item['batch_no']??'' }}" style="width:90px"></td>
              <td><input type="text" name="items[{{ $i }}][unit]" class="form-control form-control-sm bg-dark text-light border-secondary ret-unit" style="width:60px" value="{{ $item['unit']??'' }}"></td>
              <td><input type="number" step="0.0001" name="items[{{ $i }}][qty]" class="form-control form-control-sm bg-dark text-light border-secondary ret-qty text-end" style="width:80px" value="{{ $item['qty']??'' }}"></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][rate]" class="form-control form-control-sm bg-dark text-light border-secondary ret-rate text-end" style="width:80px" value="{{ $item['rate']??'' }}"></td>
              <td><input type="number" step="0.01" name="items[{{ $i }}][amount]" class="form-control form-control-sm bg-dark text-light border-secondary ret-amt text-end" style="width:90px" value="{{ $item['amount']??0 }}" readonly></td>
              <td><button type="button" class="btn btn-xs btn-outline-danger remove-ret"><i class="bi bi-x"></i></button></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <button class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Save</button>
    <a href="{{ route('sales-returns.index') }}" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>
<script>
let retIdx = {{ count($retItems ?? []) }};
function calcRet(row) {
  const qty = parseFloat(row.querySelector('.ret-qty')?.value)||0;
  const rate = parseFloat(row.querySelector('.ret-rate')?.value)||0;
  const amt = row.querySelector('.ret-amt');
  if (amt) amt.value = (qty*rate).toFixed(2);
}
function bindRet(row) {
  row.querySelectorAll('.ret-qty,.ret-rate').forEach(el => el.addEventListener('input', () => calcRet(row)));
  row.querySelector('.ret-fg')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const r   = row.querySelector('.ret-rate');
    const u   = row.querySelector('.ret-unit');
    if (r) r.value = opt.dataset.sell||'';
    if (u) u.value = opt.dataset.unit||'';
    calcRet(row);
  });
  row.querySelector('.remove-ret')?.addEventListener('click', () => row.remove());
}
document.querySelectorAll('.ret-row').forEach(bindRet);
document.getElementById('addReturnRow')?.addEventListener('click', function() {
  const tbody = document.getElementById('returnItemsBody');
  const tmpl  = tbody.querySelector('.ret-row');
  const newRow = tmpl.cloneNode(true);
  newRow.querySelectorAll('input').forEach(i => i.value = i.readOnly ? '0' : '');
  newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
  newRow.querySelectorAll('[name]').forEach(el => { el.name = el.name.replace(/\[\d+\]/, '[' + retIdx + ']'); });
  retIdx++;
  tbody.appendChild(newRow);
  bindRet(newRow);
});
function fillCustName(sel) {
  const opt = sel.options[sel.selectedIndex];
  const el  = document.getElementById('custName');
  if (el && opt.dataset.name) el.value = opt.dataset.name;
}
</script>
@endsection
