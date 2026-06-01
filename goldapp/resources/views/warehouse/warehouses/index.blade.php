@extends('layouts.app')
@section('title','Warehouses')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-warning mb-0"><i class="bi bi-building me-2"></i>Warehouses</h4>
    <a href="{{ route('warehouses.create') }}" class="btn btn-warning btn-sm"><i class="bi bi-plus-lg me-1"></i>New Warehouse</a>
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
  <div class="card bg-dark border-secondary">
    <div class="table-responsive">
      <table class="table table-dark table-hover table-sm mb-0">
        <thead class="text-warning">
          <tr><th>Code</th><th>Name</th><th>Type</th><th>City</th><th>State</th><th>Capacity</th><th>Temp Range</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @forelse($warehouses as $w)
          <tr>
            <td><code>{{ $w->code }}</code></td>
            <td>{{ $w->name }}</td>
            <td><span class="badge bg-info text-dark">{{ strtoupper($w->type) }}</span></td>
            <td>{{ $w->city }}</td>
            <td>{{ $w->state }}</td>
            <td>{{ $w->capacity ? number_format($w->capacity,2).' '.$w->capacity_unit : '—' }}</td>
            <td>{{ $w->temperature_min !== null ? $w->temperature_min.'°C to '.$w->temperature_max.'°C' : '—' }}</td>
            <td><span class="badge bg-{{ $w->status?'success':'secondary' }}">{{ $w->status?'Active':'Inactive' }}</span></td>
            <td>
              <a href="{{ route('warehouses.show',$w) }}" class="btn btn-xs btn-outline-info py-0 px-1"><i class="bi bi-eye"></i></a>
              <a href="{{ route('warehouses.edit',$w) }}" class="btn btn-xs btn-outline-warning py-0 px-1"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="{{ route('warehouses.destroy',$w) }}" class="d-inline" onsubmit="return confirm('Deactivate?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center text-muted py-4">No warehouses found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">{{ $warehouses->links() }}</div>
</div>
@endsection
