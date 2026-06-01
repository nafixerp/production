@extends('layouts.app')
@section('title','POS Sessions')
@section('content')
<div class="container-fluid px-4 py-3">
  <h4 class="text-warning mb-3"><i class="bi bi-cash-register me-2"></i>Point of Sale – Sessions</h4>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert alert-danger py-2">{{ session('error') }}</div>@endif

  <div class="row g-3 mb-3">
    <div class="col-md-5">
      <div class="card bg-dark border-warning h-100">
        <div class="card-body">
          @if($openSession)
          <h6 class="text-success">Session Open: {{ $openSession->session_no }}</h6>
          <p class="text-muted small">Terminal: {{ $openSession->terminal }} | Opened: {{ $openSession->opened_at->format('d/m/Y H:i') }}</p>
          <p class="text-warning">Total Sales: ₹{{ number_format($openSession->total_sales,2) }}</p>
          <div class="d-flex gap-2">
            <a href="{{ route('pos.bill',$openSession) }}" class="btn btn-success"><i class="bi bi-receipt me-1"></i>Continue Billing</a>
            <form method="POST" action="{{ route('pos.close',$openSession) }}">
              @csrf
              <div class="input-group input-group-sm">
                <input type="number" step="0.01" name="closing_cash" class="form-control bg-dark text-light border-secondary" placeholder="Closing Cash">
                <button class="btn btn-warning">Close Session</button>
              </div>
            </form>
          </div>
          @else
          <h6 class="text-warning">Open New Session</h6>
          <form method="POST" action="{{ route('pos.store') }}">
            @csrf
            <div class="row g-2">
              <div class="col-6">
                <label class="form-label text-light small">Opening Cash</label>
                <input type="number" step="0.01" name="opening_cash" class="form-control form-control-sm bg-dark text-light border-secondary" value="0" required>
              </div>
              <div class="col-6">
                <label class="form-label text-light small">Terminal</label>
                <input type="text" name="terminal" class="form-control form-control-sm bg-dark text-light border-secondary" value="MAIN">
              </div>
              <div class="col-12">
                <button class="btn btn-success w-100"><i class="bi bi-power me-1"></i>Open Session</button>
              </div>
            </div>
          </form>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="card bg-dark border-secondary">
    <div class="card-header text-warning">Recent Sessions</div>
    <div class="table-responsive">
      <table class="table table-dark table-hover table-sm mb-0">
        <thead class="text-warning"><tr><th>Session No</th><th>Cashier</th><th>Terminal</th><th>Opened At</th><th>Closed At</th><th class="text-end">Opening Cash</th><th class="text-end">Total Sales</th><th class="text-end">Closing Cash</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
          @forelse($sessions as $s)
          <tr>
            <td><code>{{ $s->session_no }}</code></td>
            <td>{{ $s->cashier?->name }}</td>
            <td>{{ $s->terminal }}</td>
            <td>{{ $s->opened_at->format('d/m/Y H:i') }}</td>
            <td>{{ $s->closed_at?->format('d/m/Y H:i') ?? '—' }}</td>
            <td class="text-end">₹{{ number_format($s->opening_cash,2) }}</td>
            <td class="text-end">₹{{ number_format($s->total_sales,2) }}</td>
            <td class="text-end">{{ $s->closing_cash !== null ? '₹'.number_format($s->closing_cash,2) : '—' }}</td>
            <td><span class="badge bg-{{ $s->status=='open'?'success':'secondary' }}">{{ ucfirst($s->status) }}</span></td>
            <td>
              @if($s->status=='open')
              <a href="{{ route('pos.bill',$s) }}" class="btn btn-xs btn-outline-success py-0 px-1"><i class="bi bi-receipt"></i></a>
              @else
              <a href="{{ route('pos.show',$s) }}" class="btn btn-xs btn-outline-info py-0 px-1"><i class="bi bi-eye"></i></a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="10" class="text-center text-muted py-4">No sessions.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">{{ $sessions->links() }}</div>
</div>
@endsection
