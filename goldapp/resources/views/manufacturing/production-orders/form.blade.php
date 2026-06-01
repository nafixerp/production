@extends('layouts.app')
@section('title', $order ? 'Edit Production Order' : 'New Production Order')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-gear-fill me-2"></i>{{ $order ? 'Edit: '.$order->slno : 'New Production Order' }}</h4>
    <a href="{{ route('production-orders-new.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<form method="POST" action="{{ $order ? route('production-orders-new.update', $order) : route('production-orders-new.store') }}">
    @csrf
    @if($order) @method('PUT') @endif

    <div class="card-erp p-3 mb-3">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label-erp">Order No</label>
                <input type="text" class="form-control form-erp" value="{{ $nextSlno }}" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Order Date <span class="text-danger">*</span></label>
                <input type="date" name="order_date" value="{{ old('order_date', $order?->order_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" class="form-control form-erp" required>
            </div>
            <div class="col-md-4">
                <label class="form-label-erp">Finished Good <span class="text-danger">*</span></label>
                <select name="fg_id" id="fgSelect" class="form-select form-erp" required onchange="loadRecipe()">
                    <option value="">-- Select FG --</option>
                    @foreach($fgList as $fg)
                        <option value="{{ $fg->id }}" data-code="{{ $fg->code }}" data-unit="{{ $fg->unit ?? 'KG' }}"
                            {{ old('fg_id', $order?->fg_id ?? '') == $fg->id ? 'selected' : '' }}>
                            {{ $fg->code }} - {{ $fg->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Order Qty <span class="text-danger">*</span></label>
                <input type="number" name="order_qty" value="{{ old('order_qty', $order?->order_qty ?? '') }}" class="form-control form-erp" step="0.0001" min="0.0001" required onchange="scaleBOM()">
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Unit</label>
                <input type="text" name="unit" id="fgUnit" value="{{ old('unit', $order?->unit ?? 'KG') }}" class="form-control form-erp">
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Planned Start</label>
                <input type="date" name="planned_start" value="{{ old('planned_start', $order?->planned_start?->format('Y-m-d') ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Planned End</label>
                <input type="date" name="planned_end" value="{{ old('planned_end', $order?->planned_end?->format('Y-m-d') ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Priority</label>
                <select name="priority" class="form-select form-erp">
                    @foreach(['low','normal','high','urgent'] as $p)
                        <option value="{{ $p }}" {{ old('priority', $order?->priority ?? 'normal') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Recipe / BOM</label>
                <select name="recipe_id" id="recipeSelect" class="form-select form-erp">
                    <option value="">-- Manual BOM Entry --</option>
                    @foreach($recipes as $r)
                        <option value="{{ $r->id }}" {{ old('recipe_id', $order?->recipe_id ?? '') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-erp">Batch No</label>
                <input type="text" name="batch_no" value="{{ old('batch_no', $order?->batch_no ?? '') }}" class="form-control form-erp" placeholder="Auto-generated if blank">
            </div>
            <div class="col-md-2">
                <label class="form-label-erp">Cost Centre</label>
                <input type="text" name="cost_centre" value="{{ old('cost_centre', $order?->cost_centre ?? '') }}" class="form-control form-erp">
            </div>
            <div class="col-12">
                <label class="form-label-erp">Narration</label>
                <textarea name="narration" rows="1" class="form-control form-erp">{{ old('narration', $order?->narration ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card-erp p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="text-gold mb-0">Bill of Materials (BOM)</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-info me-1" onclick="loadRecipeBOM()"><i class="bi bi-arrow-clockwise me-1"></i>Load Recipe BOM</button>
                <button type="button" class="btn btn-sm btn-outline-gold" onclick="addBomRow()"><i class="bi bi-plus-lg me-1"></i>Add Row</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-erp">
                <thead><tr><th>Type</th><th>Code</th><th>Item Name</th><th>Unit</th><th>Required Qty</th><th>Wastage %</th><th>Cost Rate</th><th>Cost Amount</th><th></th></tr></thead>
                <tbody id="bomBody">
                @foreach($bom as $idx => $b)
                    <tr>
                        <td><select name="bom[{{ $idx }}][item_type]" class="form-select form-select-sm form-erp" style="width:70px"><option {{ ($b->item_type??'RM')==='RM'?'selected':'' }}>RM</option><option {{ ($b->item_type??'')==='PM'?'selected':'' }}>PM</option><option {{ ($b->item_type??'')==='SFG'?'selected':'' }}>SFG</option></select></td>
                        <td><input type="text" name="bom[{{ $idx }}][item_code]" value="{{ $b->item_code??'' }}" class="form-control form-control-sm form-erp" style="width:85px"><input type="hidden" name="bom[{{ $idx }}][item_id]" value="{{ $b->item_id??'' }}"></td>
                        <td><input type="text" name="bom[{{ $idx }}][item_name]" value="{{ $b->item_name??'' }}" class="form-control form-control-sm form-erp"></td>
                        <td><input type="text" name="bom[{{ $idx }}][unit]" value="{{ $b->unit??'KG' }}" class="form-control form-control-sm form-erp" style="width:65px"></td>
                        <td><input type="number" name="bom[{{ $idx }}][required_qty]" value="{{ $b->required_qty??0 }}" class="form-control form-control-sm form-erp" step="0.0001" oninput="calcBomRow(this)"></td>
                        <td><input type="number" name="bom[{{ $idx }}][wastage_pct]" value="{{ $b->wastage_pct??0 }}" class="form-control form-control-sm form-erp" step="0.01" style="width:75px"></td>
                        <td><input type="number" name="bom[{{ $idx }}][cost_rate]" value="{{ $b->cost_rate??0 }}" class="form-control form-control-sm form-erp" step="0.0001" oninput="calcBomRow(this)"></td>
                        <td><input type="number" name="bom[{{ $idx }}][cost_amount]" value="{{ $b->cost_amount??0 }}" class="form-control form-control-sm form-erp" step="0.01" readonly></td>
                        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove();calcBomTotal()"><i class="bi bi-trash"></i></button></td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr><td colspan="7" class="text-end text-muted small fw-bold">Total Planned Cost:</td>
                    <td><input type="number" id="totalBomCost" class="form-control form-control-sm form-erp" readonly></td><td></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <button type="submit" class="btn btn-gold"><i class="bi bi-check-lg me-1"></i>{{ $order ? 'Update' : 'Create' }} Production Order</button>
    <a href="{{ route('production-orders-new.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
</form>

<template id="bomRowTmpl">
    <tr>
        <td><select name="bom[__IDX__][item_type]" class="form-select form-select-sm form-erp" style="width:70px"><option>RM</option><option>PM</option><option>SFG</option></select></td>
        <td><input type="text" name="bom[__IDX__][item_code]" class="form-control form-control-sm form-erp" style="width:85px"><input type="hidden" name="bom[__IDX__][item_id]" value=""></td>
        <td><input type="text" name="bom[__IDX__][item_name]" class="form-control form-control-sm form-erp"></td>
        <td><input type="text" name="bom[__IDX__][unit]" value="KG" class="form-control form-control-sm form-erp" style="width:65px"></td>
        <td><input type="number" name="bom[__IDX__][required_qty]" value="0" class="form-control form-control-sm form-erp" step="0.0001" oninput="calcBomRow(this)"></td>
        <td><input type="number" name="bom[__IDX__][wastage_pct]" value="0" class="form-control form-control-sm form-erp" step="0.01" style="width:75px"></td>
        <td><input type="number" name="bom[__IDX__][cost_rate]" value="0" class="form-control form-control-sm form-erp" step="0.0001" oninput="calcBomRow(this)"></td>
        <td><input type="number" name="bom[__IDX__][cost_amount]" value="0" class="form-control form-control-sm form-erp" step="0.01" readonly></td>
        <td><button type="button" class="btn btn-xs btn-outline-danger" onclick="this.closest('tr').remove();calcBomTotal()"><i class="bi bi-trash"></i></button></td>
    </tr>
</template>

@push('scripts')
<script>
let bomIdx = {{ count($bom) }};
function addBomRow() {
    const t = document.getElementById('bomRowTmpl').innerHTML.replace(/__IDX__/g, bomIdx++);
    document.getElementById('bomBody').insertAdjacentHTML('beforeend', t);
}
function calcBomRow(inp) {
    const row = inp.closest('tr');
    const qty  = parseFloat(row.querySelector('[name*="[required_qty]"]').value)||0;
    const rate = parseFloat(row.querySelector('[name*="[cost_rate]"]').value)||0;
    row.querySelector('[name*="[cost_amount]"]').value = (qty*rate).toFixed(2);
    calcBomTotal();
}
function calcBomTotal() {
    let total = 0;
    document.querySelectorAll('#bomBody tr').forEach(row => {
        total += parseFloat(row.querySelector('[name*="[cost_amount]"]')?.value)||0;
    });
    document.getElementById('totalBomCost').value = total.toFixed(2);
}
function loadRecipeBOM() {
    const recipeId = document.getElementById('recipeSelect').value;
    const orderQty = parseFloat(document.querySelector('[name="order_qty"]').value)||1;
    if (!recipeId) { alert('Please select a recipe first.'); return; }
    // AJAX stub — in production, call /api/recipe/{id}/bom
    fetch(`/api/recipe-bom/${recipeId}?qty=${orderQty}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('bomBody').innerHTML = '';
            bomIdx = 0;
            (data.items||[]).forEach(item => {
                addBomRow();
                const lastRow = document.getElementById('bomBody').lastElementChild;
                lastRow.querySelector('[name*="[item_type]"]').value = item.item_type||'RM';
                lastRow.querySelector('[name*="[item_code]"]').value = item.item_code||'';
                lastRow.querySelector('[name*="[item_id]"]').value  = item.item_id||'';
                lastRow.querySelector('[name*="[item_name]"]').value = item.item_name||'';
                lastRow.querySelector('[name*="[unit]"]').value     = item.unit||'KG';
                lastRow.querySelector('[name*="[required_qty]"]').value = item.required_qty||0;
                lastRow.querySelector('[name*="[wastage_pct]"]').value  = item.wastage_pct||0;
                lastRow.querySelector('[name*="[cost_rate]"]').value    = item.cost_rate||0;
                lastRow.querySelector('[name*="[cost_amount]"]').value  = ((item.required_qty||0)*(item.cost_rate||0)).toFixed(2);
            });
            calcBomTotal();
        }).catch(() => alert('Could not load BOM. Enter manually.'));
}
function loadRecipe() {}
function scaleBOM() {}
document.addEventListener('DOMContentLoaded', calcBomTotal);
</script>
@endpush
@endsection
