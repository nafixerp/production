@extends('layouts.app')
@section('title', isset($finishedGood) ? 'Edit Finished Good' : 'New Finished Good')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex align-items-center mb-3 gap-2">
    <a href="{{ route('finished-goods.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">{{ isset($finishedGood) ? 'Edit: '.$finishedGood->name : 'New Finished Good' }}</h4>
  </div>
  @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif

  <form method="POST" action="{{ isset($finishedGood) ? route('finished-goods.update',$finishedGood) : route('finished-goods.store') }}" enctype="multipart/form-data">
    @csrf
    @if(isset($finishedGood)) @method('PUT') @endif

    <ul class="nav nav-tabs border-secondary mb-3" id="fgTabs">
      <li class="nav-item"><a class="nav-link active text-warning" data-bs-toggle="tab" href="#basic">Basic</a></li>
      <li class="nav-item"><a class="nav-link text-light" data-bs-toggle="tab" href="#pricing">Pricing</a></li>
      <li class="nav-item"><a class="nav-link text-light" data-bs-toggle="tab" href="#storage">Storage</a></li>
      <li class="nav-item"><a class="nav-link text-light" data-bs-toggle="tab" href="#compliance">Compliance</a></li>
    </ul>

    <div class="tab-content">
      <!-- BASIC TAB -->
      <div class="tab-pane fade show active" id="basic">
        <div class="card bg-dark border-secondary p-3">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label text-light">Item Code <span class="text-danger">*</span></label>
              <input type="text" name="code" class="form-control bg-dark text-light border-secondary" value="{{ old('code',$finishedGood->code??'') }}" required>
            </div>
            <div class="col-md-5">
              <label class="form-label text-light">Item Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control bg-dark text-light border-secondary" value="{{ old('name',$finishedGood->name??'') }}" required>
            </div>
            <div class="col-md-2">
              <label class="form-label text-light">Category</label>
              <input type="text" name="category" class="form-control bg-dark text-light border-secondary" value="{{ old('category',$finishedGood->category??'') }}">
            </div>
            <div class="col-md-2">
              <label class="form-label text-light">Sub-Category</label>
              <input type="text" name="sub_category" class="form-control bg-dark text-light border-secondary" value="{{ old('sub_category',$finishedGood->sub_category??'') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label text-light">Unit</label>
              <select name="unit_id" class="form-select bg-dark text-light border-secondary">
                <option value="">-- Select Unit --</option>
                @foreach($units as $u)
                <option value="{{ $u->id }}" {{ old('unit_id', $finishedGood->unit_id??'') == $u->id ? 'selected':'' }}>{{ $u->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label text-light">HSN Code</label>
              <input type="text" name="hsn_code" class="form-control bg-dark text-light border-secondary" value="{{ old('hsn_code',$finishedGood->hsn_code??'') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label text-light">Tax / GST</label>
              <select name="tax_id" class="form-select bg-dark text-light border-secondary">
                <option value="">-- Select Tax --</option>
                @foreach($taxes as $t)
                <option value="{{ $t->id }}" {{ old('tax_id', $finishedGood->tax_id??'') == $t->id ? 'selected':'' }}>{{ $t->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label text-light">Barcode</label>
              <input type="text" name="barcode" class="form-control bg-dark text-light border-secondary" value="{{ old('barcode',$finishedGood->barcode??'') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label text-light">SKU</label>
              <input type="text" name="sku" class="form-control bg-dark text-light border-secondary" value="{{ old('sku',$finishedGood->sku??'') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label text-light">Description</label>
              <textarea name="description" rows="3" class="form-control bg-dark text-light border-secondary">{{ old('description',$finishedGood->description??'') }}</textarea>
            </div>
            <div class="col-md-3">
              <label class="form-label text-light">Image</label>
              <input type="file" name="image" class="form-control bg-dark text-light border-secondary">
            </div>
            <div class="col-md-3">
              <label class="form-label text-light">Status</label>
              <select name="status" class="form-select bg-dark text-light border-secondary">
                <option value="1" {{ old('status',$finishedGood->status??1)==1?'selected':'' }}>Active</option>
                <option value="0" {{ old('status',$finishedGood->status??1)==0?'selected':'' }}>Inactive</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- PRICING TAB -->
      <div class="tab-pane fade" id="pricing">
        <div class="card bg-dark border-secondary p-3">
          <div class="row g-3">
            @foreach([['mrp','MRP'],['selling_price','Selling Price'],['wholesale_price','Wholesale Price'],['distributor_price','Distributor Price'],['online_price','Online Price'],['cost_price','Cost Price']] as [$field,$label])
            <div class="col-md-2">
              <label class="form-label text-light">{{ $label }}</label>
              <input type="number" step="0.01" name="{{ $field }}" class="form-control bg-dark text-light border-secondary" value="{{ old($field,$finishedGood?->$field??0) }}">
            </div>
            @endforeach
            <div class="col-md-2">
              <label class="form-label text-light">Reorder Qty</label>
              <input type="number" step="0.0001" name="reorder_qty" class="form-control bg-dark text-light border-secondary" value="{{ old('reorder_qty',$finishedGood->reorder_qty??0) }}">
            </div>
          </div>
        </div>
      </div>

      <!-- STORAGE TAB -->
      <div class="tab-pane fade" id="storage">
        <div class="card bg-dark border-secondary p-3">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label text-light">Shelf Life (days)</label>
              <input type="number" name="shelf_life_days" class="form-control bg-dark text-light border-secondary" value="{{ old('shelf_life_days',$finishedGood->shelf_life_days??'') }}">
            </div>
            <div class="col-md-3">
              <label class="form-label text-light">Storage Temp</label>
              <input type="text" name="storage_temp" class="form-control bg-dark text-light border-secondary" placeholder="e.g. 2-8°C" value="{{ old('storage_temp',$finishedGood->storage_temp??'') }}">
            </div>
            <div class="col-md-2">
              <label class="form-label text-light">Weight</label>
              <input type="number" step="0.001" name="weight" class="form-control bg-dark text-light border-secondary" value="{{ old('weight',$finishedGood->weight??'') }}">
            </div>
            <div class="col-md-2">
              <label class="form-label text-light">Weight Unit</label>
              <input type="text" name="weight_unit" class="form-control bg-dark text-light border-secondary" placeholder="kg/g" value="{{ old('weight_unit',$finishedGood->weight_unit??'') }}">
            </div>
            <div class="col-md-2">
              <label class="form-label text-light">Perishable?</label>
              <select name="is_perishable" class="form-select bg-dark text-light border-secondary">
                <option value="0" {{ old('is_perishable',$finishedGood->is_perishable??0)==0?'selected':'' }}>No</option>
                <option value="1" {{ old('is_perishable',$finishedGood->is_perishable??0)==1?'selected':'' }}>Yes</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- COMPLIANCE TAB -->
      <div class="tab-pane fade" id="compliance">
        <div class="card bg-dark border-secondary p-3">
          <p class="text-muted small">Allergen IDs (comma-separated or multi-select)</p>
          <div class="col-md-6">
            <label class="form-label text-light">Allergen IDs (JSON)</label>
            <input type="text" name="allergen_ids" class="form-control bg-dark text-light border-secondary" placeholder='e.g. [1,2,3]' value="{{ old('allergen_ids', is_array($finishedGood->allergen_ids??null) ? implode(',', $finishedGood->allergen_ids) : ($finishedGood->allergen_ids??'')) }}">
          </div>
        </div>
      </div>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Save</button>
      <a href="{{ route('finished-goods.index') }}" class="btn btn-secondary ms-2">Cancel</a>
    </div>
  </form>
</div>
@endsection
