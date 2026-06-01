@extends('layouts.app')
@section('title','Warehouse')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('warehouses.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">{{ $warehouse->name }}</h4>
    <a href="{{ route('warehouses.edit',$warehouse) }}" class="btn btn-sm btn-warning ms-auto"><i class="bi bi-pencil me-1"></i>Edit</a>
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <table class="table table-sm table-dark mb-0">
            <tr><td class="text-muted">Code</td><td>{{ $warehouse->code }}</td></tr>
            <tr><td class="text-muted">Type</td><td><span class="badge bg-info">{{ strtoupper($warehouse->type) }}</span></td></tr>
            <tr><td class="text-muted">City</td><td>{{ $warehouse->city }}</td></tr>
            <tr><td class="text-muted">State</td><td>{{ $warehouse->state }}</td></tr>
            <tr><td class="text-muted">Address</td><td>{{ $warehouse->address }}</td></tr>
            <tr><td class="text-muted">Capacity</td><td>{{ $warehouse->capacity ? number_format($warehouse->capacity,2).' '.$warehouse->capacity_unit : '—' }}</td></tr>
            <tr><td class="text-muted">Temp Range</td><td>{{ $warehouse->temperature_min !== null ? $warehouse->temperature_min.'°C – '.$warehouse->temperature_max.'°C' : '—' }}</td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <h6 class="text-warning">Stock Summary</h6>
          <div class="table-responsive">
            <table class="table table-dark table-sm mb-0">
              <thead class="text-warning"><tr><th>FG Code</th><th>FG Name</th><th class="text-end">Net Qty</th><th class="text-end">Total Value</th></tr></thead>
              <tbody>
                @forelse($stockSummary as $s)
                <tr>
                  <td><code>{{ $s->fg_code }}</code></td>
                  <td>{{ $s->fg_name }}</td>
                  <td class="text-end">{{ number_format($s->net_qty,4) }}</td>
                  <td class="text-end">₹{{ number_format($s->total_value,2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-muted text-center py-3">No stock.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
