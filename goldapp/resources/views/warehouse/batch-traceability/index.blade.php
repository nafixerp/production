@extends('layouts.app')
@section('title','Batch Traceability')
@section('content')
<div class="container-fluid px-4 py-3">
  <h4 class="text-warning mb-3"><i class="bi bi-diagram-3 me-2"></i>Batch Traceability</h4>
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input type="text" name="batch_no" value="{{ request('batch_no') }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Batch No…"></div>
    <div class="col-md-3">
      <select name="fg_id" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All FG</option>
        @foreach($fgList as $fg)
        <option value="{{ $fg->id }}" {{ request('fg_id')==$fg->id?'selected':'' }}>{{ $fg->code }} – {{ $fg->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <select name="qc_status" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All QC</option>
        <option value="pending" {{ request('qc_status')=='pending'?'selected':'' }}>Pending</option>
        <option value="pass" {{ request('qc_status')=='pass'?'selected':'' }}>Pass</option>
        <option value="fail" {{ request('qc_status')=='fail'?'selected':'' }}>Fail</option>
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-warning btn-sm">Filter</button></div>
  </form>
  <div class="card bg-dark border-secondary">
    <div class="table-responsive">
      <table class="table table-dark table-hover table-sm mb-0">
        <thead class="text-warning">
          <tr><th>Batch No</th><th>FG Name</th><th>Mfg Date</th><th>Expiry</th>
            <th class="text-end">Produced</th><th class="text-end">Dispatched</th>
            <th class="text-end">Returned</th><th class="text-end">Available</th>
            <th>QC Status</th><th>Recall</th><th>Action</th></tr>
        </thead>
        <tbody>
          @forelse($batches as $b)
          <tr>
            <td><a href="{{ route('batch-trace.show',$b->batch_no) }}" class="text-warning"><code>{{ $b->batch_no }}</code></a></td>
            <td>{{ $b->finishedGood?->name ?? $b->fg_name }}</td>
            <td>{{ $b->mfg_date?->format('d/m/Y') ?? '—' }}</td>
            <td>
              @if($b->expiry_date)
              @php $days = now()->diffInDays($b->expiry_date,false) @endphp
              <span class="{{ $days<0?'text-danger':($days<=30?'text-warning':'text-success') }}">
                {{ $b->expiry_date->format('d/m/Y') }}
              </span>
              @else—@endif
            </td>
            <td class="text-end">{{ number_format($b->qty_produced,4) }}</td>
            <td class="text-end">{{ number_format($b->qty_dispatched,4) }}</td>
            <td class="text-end">{{ number_format($b->qty_returned,4) }}</td>
            <td class="text-end fw-bold {{ $b->qty_available>0?'text-success':'text-danger' }}">{{ number_format($b->qty_available,4) }}</td>
            <td><span class="badge bg-{{ $b->qc_status=='pass'?'success':($b->qc_status=='fail'?'danger':'warning') }}">{{ strtoupper($b->qc_status) }}</span></td>
            <td>{{ $b->recall_flag ? '<span class="badge bg-danger">RECALL</span>' : '—' }}</td>
            <td><a href="{{ route('batch-trace.show',$b->batch_no) }}" class="btn btn-xs btn-outline-info py-0 px-1"><i class="bi bi-eye"></i></a></td>
          </tr>
          @empty
          <tr><td colspan="11" class="text-center text-muted py-4">No batch records.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">{{ $batches->links() }}</div>
</div>
@endsection
