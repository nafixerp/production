@extends('layouts.app')
@section('title', ($return ?? null) ? 'Edit Return' : 'New Purchase Return')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-arrow-return-left me-2"></i>{{ ($return ?? null) ? 'Edit Return: '.$return->slno : 'New Purchase Return' }}</h4>
    <a href="{{ route('purchase-returns-new.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ ($return ?? null) ? route('purchase-returns-new.update', $return) : route('purchase-returns-new.store') }}">
    @csrf
    @if($return ?? null) @method('PUT') @endif

    <div class="card-erp p-3 mb-3">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label-erp">Return No</label>
                <input type="text" class="form-control form-erp" value="{{ $nextSlno }}" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Return Date <span class="text-danger">*</span></label>
                <input type="date" name="return_date" value="{{ old('return_date', ($return ?? null)?->return_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control form-erp" required>
            </div>
            <div class="col-md-4">
                <label class="form-label-erp">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-select form-erp" required>
                    <option value="">-- Select --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id', ($return ?? null)?->supplier_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->code }} - {{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label-erp">Original Invoice</label>
                <select name="invoice_id" class="form-select form-erp">
                    <option value="">-- None --</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}" {{ old('invoice_id', ($return ?? null)?->invoice_id ?? '') == $inv->id ? 'selected' : '' }}>{{ $inv->slno }} - {{ $inv->supplier_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12">
                <label class="form-label-erp">Reason for Return <span class="text-danger">*</span></label>
                <textarea name="reason" rows="2" class="form-control form-erp" required>{{ old('reason', ($return ?? null)?->reason ?? '') }}</textarea>
            </div>
            <div class="col-md-12">
                <label class="form-label-erp">Narration</label>
                <input type="text" name="narration" value="{{ old('narration', ($return ?? null)?->narration ?? '') }}" class="form-control form-erp">
            </div>
        </div>
    </div>

    <div class="card-erp p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-gold mb-0">Return Items</h6>
            <button type="button" class="btn btn-sm btn-outline-gold" onclick="addRetRow()"><i class="bi bi-plus-lg me-1"></i>Add Row</button>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-erp">
                <thead><tr><th>Type</th><th>Code</th><th>Item Name</th><th>Unit</th><th>Qty</th><th>Rate</th><th>Amount</th><th>Batch No</th><th></th></tr></thead>
                <tbody id="retBody">
                @foreach($items as $idx => $item)
                    <tr>
                        <td><select name="items[{{ $idx }}][item_type]" class="form-select form-select-sm form-erp" style="width:70px"><option {{ ($item->item_type??'RM')==='RM'?'selected':'' }}>RM</option><option {{ ($item->item_type??'')==='PM'?'selected':'' }}>PM</option></select></td>
                        <td><input type="text" name="items[{{ $idx }}][item_code]" value="{{ $item->item_code??'' }}" class="form-control form-control-sm form-erp" style="width:85px"></td>
                        <td><input type="text" name="items[{{ $idx }}][item_name]" value="{{ $item->item_name??'' }}" class="form-control form-control-sm form-erp"></td>
                        <td><input type="text" name="items[{{ $idx }}][unit]" value="{{ $item->unit??'KG' }}" class="form-control form-control-sm form-erp" style="width:65px"></td>
                        <td><input type="number" name="items[{{ $idx }}][qty]" value="{{ $item->qty??0 }}" class="form-control form-control-sm form-erp" step="0.0001" oninput="calcRetRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][rate]" value="{{ $item->rate??0 }}" class="form-control form-control-sm form-erp" step="0.0001" oninput="calcRetRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][amount]" value="{{ $item->amount??0 }}" class="form-control form-control-sm form-erp" step="0.01" readonly></td>
                        <td><input type="text" name="items[{{ $idx }}][batch_no]" value="{{ $item->batch_no??'' }}" class="form-control form-control-sm form-erp"></td>
                        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>Save Return</button>
    <a href="{{ route('purchase-returns-new.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
</form>

<template id="retRowTmpl">
    <tr>
        <td><select name="items[__IDX__][item_type]" class="form-select form-select-sm form-erp" style="width:70px"><option>RM</option><option>PM</option></select></td>
        <td><input type="text" name="items[__IDX__][item_code]" class="form-control form-control-sm form-erp" style="width:85px"></td>
        <td><input type="text" name="items[__IDX__][item_name]" class="form-control form-control-sm form-erp"></td>
        <td><input type="text" name="items[__IDX__][unit]" value="KG" class="form-control form-control-sm form-erp" style="width:65px"></td>
        <td><input type="number" name="items[__IDX__][qty]" value="0" class="form-control form-control-sm form-erp" step="0.0001" oninput="calcRetRow(this)"></td>
        <td><input type="number" name="items[__IDX__][rate]" value="0" class="form-control form-control-sm form-erp" step="0.0001" oninput="calcRetRow(this)"></td>
        <td><input type="number" name="items[__IDX__][amount]" value="0" class="form-control form-control-sm form-erp" step="0.01" readonly></td>
        <td><input type="text" name="items[__IDX__][batch_no]" class="form-control form-control-sm form-erp"></td>
        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>
@push('scripts')
<script>
let retIdx = {{ count($items) }};
function addRetRow() {
    const t = document.getElementById('retRowTmpl').innerHTML.replace(/__IDX__/g, retIdx++);
    document.getElementById('retBody').insertAdjacentHTML('beforeend', t);
}
function calcRetRow(inp) {
    const row = inp.closest('tr');
    const qty  = parseFloat(row.querySelector('[name*="[qty]"]').value)||0;
    const rate = parseFloat(row.querySelector('[name*="[rate]"]').value)||0;
    row.querySelector('[name*="[amount]"]').value = (qty*rate).toFixed(2);
}
</script>
@endpush
@endsection
