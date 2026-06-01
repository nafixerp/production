@extends('layouts.app')
@section('title', isset($fgQuality) ? 'Edit QC' : 'New FG QC')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('fg-quality.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">{{ isset($fgQuality) ? 'Edit QC Check' : 'New FG Quality Check' }}</h4>
  </div>
  @if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ isset($fgQuality) ? route('fg-quality.update',$fgQuality) : route('fg-quality.store') }}">
    @csrf @if(isset($fgQuality)) @method('PUT') @endif
    <div class="card bg-dark border-secondary p-3">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label text-light">Finished Good <span class="text-danger">*</span></label>
          <select name="fg_id" class="form-select bg-dark text-light border-secondary" required>
            <option value="">-- Select --</option>
            @foreach($fgList as $fg)
            <option value="{{ $fg->id }}" {{ old('fg_id', $fgQuality->fg_id??'') == $fg->id ? 'selected':'' }}>{{ $fg->code }} – {{ $fg->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label text-light">Batch No <span class="text-danger">*</span></label>
          <input type="text" name="batch_no" class="form-control bg-dark text-light border-secondary" value="{{ old('batch_no',$fgQuality->batch_no??'') }}" required>
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Check Date <span class="text-danger">*</span></label>
          <input type="date" name="check_date" class="form-control bg-dark text-light border-secondary" value="{{ old('check_date', $fgQuality->check_date?->format('Y-m-d') ?? today()->toDateString()) }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label text-light">Result <span class="text-danger">*</span></label>
          <select name="result" class="form-select bg-dark text-light border-secondary" required>
            <option value="pass" {{ old('result',$fgQuality->result??'')=='pass'?'selected':'' }}>Pass</option>
            <option value="fail" {{ old('result',$fgQuality->result??'')=='fail'?'selected':'' }}>Fail</option>
            <option value="partial" {{ old('result',$fgQuality->result??'')=='partial'?'selected':'' }}>Partial</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Temperature (°C)</label>
          <input type="number" step="0.01" name="temperature" class="form-control bg-dark text-light border-secondary" value="{{ old('temperature',$fgQuality->temperature??'') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Moisture %</label>
          <input type="number" step="0.01" name="moisture_pct" class="form-control bg-dark text-light border-secondary" value="{{ old('moisture_pct',$fgQuality->moisture_pct??'') }}">
        </div>
        <div class="col-md-2">
          <label class="form-label text-light">Release Date</label>
          <input type="date" name="release_date" class="form-control bg-dark text-light border-secondary" value="{{ old('release_date', $fgQuality->release_date?->format('Y-m-d')??'') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Hold Reason (if Fail)</label>
          <input type="text" name="hold_reason" class="form-control bg-dark text-light border-secondary" value="{{ old('hold_reason',$fgQuality->hold_reason??'') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Visual Check</label>
          <textarea name="visual_check" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('visual_check',$fgQuality->visual_check??'') }}</textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Taste Check</label>
          <textarea name="taste_check" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('taste_check',$fgQuality->taste_check??'') }}</textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label text-light">Micro Check</label>
          <textarea name="micro_check" rows="2" class="form-control bg-dark text-light border-secondary">{{ old('micro_check',$fgQuality->micro_check??'') }}</textarea>
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-warning"><i class="bi bi-check-lg me-1"></i>Save</button>
        <a href="{{ route('fg-quality.index') }}" class="btn btn-secondary ms-2">Cancel</a>
      </div>
    </div>
  </form>
</div>
@endsection
