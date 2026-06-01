@extends('layouts.app')
@section('title', isset($warehouse) ? 'Edit Warehouse' : 'New Warehouse')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('warehouses.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">{{ isset($warehouse) ? 'Edit: '.$warehouse->name : 'New Warehouse' }}</h4>
  </div>
  @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ isset($warehouse) ? route('warehouses.update',$warehouse) : route('warehouses.store') }}">
    @csrf @if(isset($warehouse)) @method('PUT') @endif
    <div class="card bg-dark border-secondary p-3">
      <div class="row g-3">
        <div class="col-md-2">
          <label class="form-label text-light">Code <span class="text-danger">*</span></label>
          <input type="text" name="code" class="form-control bg-dark text-light border-secondary" value="{{ old('code',$warehouse->code??'') }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Name <span class="text-danger">*</span></label>
          <input type="text" name="name" class="form-control bg-dark text-light border-secondary" value="{{ old('name',$warehouse->name??'') }}" required>
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Type <span class="text-danger">*</span></label>
          <select name="type" class="form-select bg-dark text-light border-secondary" required>
            @foreach(['raw','finished','packing','cold'] as $t)
            <option value="{{ $t }}" {{ old('type',$warehouse->type??'finished')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">City</label>
          <input type="text" name="city" class="form-control bg-dark text-light border-secondary" value="{{ old('city',$warehouse->city??'') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">State</label>
          <input type="text" name="state" class="form-control bg-dark text-light border-secondary" value="{{ old('state',$warehouse->state??'') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Address</label>
          <textarea name="address" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('address',$warehouse->address??'') }}</textarea>
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Capacity</label>
          <input type="number" step="0.01" name="capacity" class="form-control bg-dark text-light border-secondary" value="{{ old('capacity',$warehouse->capacity??'') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Capacity Unit</label>
          <input type="text" name="capacity_unit" class="form-control bg-dark text-light border-secondary" placeholder="MT/sq ft" value="{{ old('capacity_unit',$warehouse->capacity_unit??'') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Temp Min (°C)</label>
          <input type="number" step="0.01" name="temperature_min" class="form-control bg-dark text-light border-secondary" value="{{ old('temperature_min',$warehouse->temperature_min??'') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Temp Max (°C)</label>
          <input type="number" step="0.01" name="temperature_max" class="form-control bg-dark text-light border-secondary" value="{{ old('temperature_max',$warehouse->temperature_max??'') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Branch</label>
          <select name="branch_id" class="form-select bg-dark text-light border-secondary">
            <option value="">-- None --</option>
            @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ old('branch_id',$warehouse->branch_id??'')==$b->id?'selected':'' }}>{{ $b->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Status</label>
          <select name="status" class="form-select bg-dark text-light border-secondary">
            <option value="1" {{ old('status',$warehouse->status??1)==1?'selected':'' }}>Active</option>
            <option value="0" {{ old('status',$warehouse->status??1)==0?'selected':'' }}>Inactive</option>
          </select>
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Save</button>
        <a href="{{ route('warehouses.index') }}" class="btn btn-secondary ms-2">Cancel</a>
      </div>
    </div>
  </form>
</div>
@endsection
