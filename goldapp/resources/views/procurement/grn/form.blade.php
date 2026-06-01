@extends('layouts.app')
@section('title', $grn ? 'Edit GRN' : 'New GRN')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-box-arrow-in-down me-2"></i>{{ $grn ? 'Edit GRN: '.$grn->slno : 'New Goods Receipt Note' }}</h4>
    <a href="{{ route('grn.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ $grn ? route('grn.update', $grn) : route('grn.store') }}">
    @csrf
    @if($grn) @method('PUT') @endif

    <div class="card-erp p-3 mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label-erp">GRN Date <span class="text-danger">*</span></label>
                <input type="date" name="grn_date" value="{{ old('grn_date', $grn->grn_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control form-erp" required>
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-select form-erp" required>
                    <option value="">-- Select --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ old('supplier_id', $grn->supplier_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->code }} - {{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Link to PO</label>
                <select name="po_id" class="form-select form-erp">
                    <option value="">-- No PO (Direct GRN) --</option>
                    @foreach($openPOs as $p)
                        <option value="{{ $p->id }}" {{ old('po_id', $grn->po_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->slno }} - {{ $p->supplier_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Supplier Invoice No</label>
                <input type="text" name="supplier_invoice_no" value="{{ old('supplier_invoice_no', $grn->supplier_invoice_no ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Supplier Invoice Date</label>
                <input type="date" name="supplier_invoice_date" value="{{ old('supplier_invoice_date', $grn->supplier_invoice_date?->format('Y-m-d') ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-9">
                <label class="form-label-erp">Narration</label>
                <input type="text" name="narration" value="{{ old('narration', $grn->narration ?? '') }}" class="form-control form-erp">
            </div>
        </div>
    </div>

    <div class="card-erp p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-gold mb-0">Received Items</h6>
            <button type="button" class="btn btn-sm btn-outline-gold" onclick="addGrnRow()"><i class="bi bi-plus-lg me-1"></i>Add Row</button>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-erp" id="grnTable">
                <thead>
                    <tr><th>Type</th><th>Code</th><th>Item Name</th><th>Unit</th><th>Ordered</th><th>Received</th><th>Accepted</th><th>Rejected</th><th>Rate</th><th>Amount</th><th>Batch No</th><th>Mfg Date</th><th>Expiry</th><th></th></tr>
                </thead>
                <tbody id="grnBody">
                @foreach($items as $idx => $item)
                    <tr>
                        <input type="hidden" name="items[{{ $idx }}][po_item_id]" value="{{ $item->po_item_id ?? '' }}">
                        <input type="hidden" name="items[{{ $idx }}][item_id]" value="{{ $item->item_id ?? '' }}">
                        <td><select name="items[{{ $idx }}][item_type]" class="form-select form-select-sm form-erp" style="width:70px"><option {{ ($item->item_type??'RM')==='RM'?'selected':'' }}>RM</option><option {{ ($item->item_type??'')==='PM'?'selected':'' }}>PM</option></select></td>
                        <td><input type="text" name="items[{{ $idx }}][item_code]" value="{{ $item->item_code??'' }}" class="form-control form-control-sm form-erp" style="width:85px"></td>
                        <td><input type="text" name="items[{{ $idx }}][item_name]" value="{{ $item->item_name??'' }}" class="form-control form-control-sm form-erp"></td>
                        <td><input type="text" name="items[{{ $idx }}][unit]" value="{{ $item->unit??'KG' }}" class="form-control form-control-sm form-erp" style="width:65px"></td>
                        <td><input type="number" name="items[{{ $idx }}][ordered_qty]" value="{{ $item->ordered_qty??0 }}" class="form-control form-control-sm form-erp" step="0.0001" style="width:80px" readonly></td>
                        <td><input type="number" name="items[{{ $idx }}][received_qty]" value="{{ $item->received_qty??0 }}" class="form-control form-control-sm form-erp" step="0.0001" style="width:80px" oninput="syncAccepted(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][accepted_qty]" value="{{ $item->accepted_qty??0 }}" class="form-control form-control-sm form-erp accepted-qty" step="0.0001" style="width:80px" oninput="calcGrnRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][rejected_qty]" value="{{ $item->rejected_qty??0 }}" class="form-control form-control-sm form-erp" step="0.0001" style="width:80px" readonly></td>
                        <td><input type="number" name="items[{{ $idx }}][rate]" value="{{ $item->rate??0 }}" class="form-control form-control-sm form-erp" step="0.0001" style="width:85px" oninput="calcGrnRow(this)"></td>
                        <td><input type="number" name="items[{{ $idx }}][amount]" value="{{ $item->amount??0 }}" class="form-control form-control-sm form-erp" step="0.01" style="width:85px" readonly></td>
                        <td><input type="text" name="items[{{ $idx }}][batch_no]" value="{{ $item->batch_no??'' }}" class="form-control form-control-sm form-erp" style="width:100px"></td>
                        <td><input type="date" name="items[{{ $idx }}][mfg_date]" value="{{ $item->mfg_date?->format('Y-m-d')??'' }}" class="form-control form-control-sm form-erp" style="width:130px"></td>
                        <td><input type="date" name="items[{{ $idx }}][expiry_date]" value="{{ $item->expiry_date?->format('Y-m-d')??'' }}" class="form-control form-control-sm form-erp" style="width:130px"></td>
                        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>Save GRN</button>
    <a href="{{ route('grn.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
</form>

<template id="grnRowTmpl">
    <tr>
        <input type="hidden" name="items[__IDX__][po_item_id]" value=""><input type="hidden" name="items[__IDX__][item_id]" value="">
        <td><select name="items[__IDX__][item_type]" class="form-select form-select-sm form-erp" style="width:70px"><option>RM</option><option>PM</option></select></td>
        <td><input type="text" name="items[__IDX__][item_code]" class="form-control form-control-sm form-erp" style="width:85px"></td>
        <td><input type="text" name="items[__IDX__][item_name]" class="form-control form-control-sm form-erp"></td>
        <td><input type="text" name="items[__IDX__][unit]" value="KG" class="form-control form-control-sm form-erp" style="width:65px"></td>
        <td><input type="number" name="items[__IDX__][ordered_qty]" value="0" class="form-control form-control-sm form-erp" step="0.0001" style="width:80px" readonly></td>
        <td><input type="number" name="items[__IDX__][received_qty]" value="0" class="form-control form-control-sm form-erp" step="0.0001" style="width:80px" oninput="syncAccepted(this)"></td>
        <td><input type="number" name="items[__IDX__][accepted_qty]" value="0" class="form-control form-control-sm form-erp accepted-qty" step="0.0001" style="width:80px" oninput="calcGrnRow(this)"></td>
        <td><input type="number" name="items[__IDX__][rejected_qty]" value="0" class="form-control form-control-sm form-erp" step="0.0001" style="width:80px" readonly></td>
        <td><input type="number" name="items[__IDX__][rate]" value="0" class="form-control form-control-sm form-erp" step="0.0001" style="width:85px" oninput="calcGrnRow(this)"></td>
        <td><input type="number" name="items[__IDX__][amount]" value="0" class="form-control form-control-sm form-erp" step="0.01" style="width:85px" readonly></td>
        <td><input type="text" name="items[__IDX__][batch_no]" class="form-control form-control-sm form-erp" style="width:100px"></td>
        <td><input type="date" name="items[__IDX__][mfg_date]" class="form-control form-control-sm form-erp" style="width:130px"></td>
        <td><input type="date" name="items[__IDX__][expiry_date]" class="form-control form-control-sm form-erp" style="width:130px"></td>
        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>

@push('scripts')
<script>
let grnIdx = {{ count($items) }};
function addGrnRow() {
    const t = document.getElementById('grnRowTmpl').innerHTML.replace(/__IDX__/g, grnIdx++);
    document.getElementById('grnBody').insertAdjacentHTML('beforeend', t);
}
function syncAccepted(inp) {
    const row = inp.closest('tr');
    const acceptedEl = row.querySelector('[name*="[accepted_qty]"]');
    acceptedEl.value = inp.value;
    calcGrnRow(acceptedEl);
}
function calcGrnRow(inp) {
    const row = inp.closest('tr');
    const received = parseFloat(row.querySelector('[name*="[received_qty]"]').value) || 0;
    const accepted = parseFloat(row.querySelector('[name*="[accepted_qty]"]').value) || 0;
    const rate = parseFloat(row.querySelector('[name*="[rate]"]').value) || 0;
    row.querySelector('[name*="[rejected_qty]"]').value = Math.max(0, received - accepted).toFixed(4);
    row.querySelector('[name*="[amount]"]').value = (accepted * rate).toFixed(2);
}
</script>
@endpush
@endsection
