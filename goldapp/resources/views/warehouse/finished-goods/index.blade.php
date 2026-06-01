@extends('layouts.app')
@section('title','Finished Goods')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-warning mb-0"><i class="bi bi-box-seam me-2"></i>Finished Goods</h4>
    <a href="{{ route('finished-goods.create') }}" class="btn btn-warning btn-sm"><i class="bi bi-plus-lg me-1"></i>New Item</a>
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-4"><input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Search code / name…"></div>
    <div class="col-auto"><button class="btn btn-secondary btn-sm">Search</button></div>
  </form>
  <div class="card bg-dark border-secondary">
    <div class="table-responsive">
      <table class="table table-dark table-hover table-sm mb-0">
        <thead class="text-warning">
          <tr>
            <th>Code</th><th>Name</th><th>Category</th><th>Unit</th>
            <th class="text-end">MRP</th><th class="text-end">Selling Price</th>
            <th class="text-end">Cost</th><th class="text-end">Stock</th>
            <th>Nearest Expiry</th><th>Status</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $fg)
          @php
            $st = $stocks[$fg->id] ?? null;
            $netQty = $st ? $st->net_qty : 0;
            $expiry = $st?->nearest_expiry;
            $expiryClass = 'text-success';
            $expiryLabel = $expiry ? \Carbon\Carbon::parse($expiry)->format('d/m/Y') : '—';
            if ($expiry) {
              $days = now()->diffInDays(\Carbon\Carbon::parse($expiry), false);
              if ($days < 0) $expiryClass = 'text-danger fw-bold';
              elseif ($days <= 30) $expiryClass = 'text-warning fw-bold';
            }
          @endphp
          <tr>
            <td><code>{{ $fg->code }}</code></td>
            <td>{{ $fg->name }}</td>
            <td>{{ $fg->category }}</td>
            <td>{{ $fg->unit?->name }}</td>
            <td class="text-end">{{ number_format($fg->mrp,2) }}</td>
            <td class="text-end">{{ number_format($fg->selling_price,2) }}</td>
            <td class="text-end">{{ number_format($fg->cost_price,2) }}</td>
            <td class="text-end fw-bold {{ $netQty <= 0 ? 'text-danger' : 'text-success' }}">{{ number_format($netQty,2) }}</td>
            <td class="{{ $expiryClass }}">{{ $expiryLabel }}</td>
            <td>
              @if($fg->status == 1)<span class="badge bg-success">Active</span>
              @else<span class="badge bg-secondary">Inactive</span>@endif
            </td>
            <td>
              <a href="{{ route('finished-goods.show', $fg) }}" class="btn btn-xs btn-outline-info py-0 px-1"><i class="bi bi-eye"></i></a>
              <a href="{{ route('finished-goods.edit', $fg) }}" class="btn btn-xs btn-outline-warning py-0 px-1"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="{{ route('finished-goods.destroy',$fg) }}" class="d-inline" onsubmit="return confirm('Deactivate?')">
                @csrf @method('DELETE')
                <button class="btn btn-xs btn-outline-danger py-0 px-1"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="11" class="text-center text-muted py-4">No finished goods found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="mt-3">{{ $items->links() }}</div>
</div>
@endsection
