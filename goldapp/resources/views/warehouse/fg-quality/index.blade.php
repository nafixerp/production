@extends('layouts.app')
@section('title','FG Quality Checks')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-warning mb-0"><i class="bi bi-clipboard-check me-2"></i>FG Quality Checks</h4>
    <a href="{{ route('fg-quality.create') }}" class="btn btn-warning btn-sm"><i class="bi bi-plus-lg me-1"></i>New Check</a>
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input type="text" name="batch_no" value="{{ request('batch_no') }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Batch No…"></div>
    <div class="col-md-2">
      <select name="result" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All Results</option>
        <option value="pass" {{ request('result')=='pass'?'selected':'' }}>Pass</option>
        <option value="fail" {{ request('result')=='fail'?'selected':'' }}>Fail</option>
        <option value="partial" {{ request('result')=='partial'?'selected':'' }}>Partial</option>
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-warning btn-sm">Filter</button></div>
  </form>
  <div class="card bg-dark border-secondary">
    <div class="table-responsive">
      <table class="table table-dark table-hover table-sm mb-0">
        <thead class="text-warning">
          <tr><th>ID</th><th>FG Name</th><th>Batch No</th><th>Check Date</th><th>Checked By</th><th>Result</th><th>Temp</th><th>Moisture%</th><th>Release Date</th><th>Actions</th></tr>
        </thead>
        <tbody>
          @forelse($checks as $c)
          <tr>
            <td>{{ $c->id }}</td>
            <td>{{ $c->finishedGood?->name }}</td>
            <td><code>{{ $c->batch_no }}</code></td>
            <td>{{ $c->check_date->format('d/m/Y') }}</td>
            <td>{{ $c->checker?->name ?? '—' }}</td>
            <td>
              <span class="badge bg-{{ $c->result=='pass'?'success':($c->result=='fail'?'danger':'warning') }}">{{ strtoupper($c->result) }}</span>
            </td>
            <td>{{ $c->temperature ?? '—' }}</td>
            <td>{{ $c->moisture_pct ?? '—' }}</td>
            <td>{{ $c->release_date?->format('d/m/Y') ?? '—' }}</td>
            <td>
              <a href="{{ route('fg-quality.edit',$c) }}" class="btn btn-xs btn-outline-warning py-0 px-1"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="{{ route('fg-quality.destroy',$c) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="10" class="text-center text-muted py-4">No records.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">{{ $checks->links() }}</div>
</div>
@endsection
