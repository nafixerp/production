@extends('layouts.app')
@section('title','FG Stock')
@section('content')
<div class="container-fluid px-4 py-3">
  <h4 class="text-warning mb-3"><i class="bi bi-stack me-2"></i>Finished Goods Stock</h4>
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-3">
      <input type="text" name="fg" value="{{ $fgSearch }}" class="form-control form-control-sm bg-dark text-light border-secondary" placeholder="Search FG name/code…">
    </div>
    <div class="col-md-3">
      <select name="warehouse_id" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All Warehouses</option>
        @foreach($warehouses as $w)
        <option value="{{ $w->id }}" {{ $warehouseId==$w->id?'selected':'' }}>{{ $w->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <select name="expiry_status" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All Expiry</option>
        <option value="ok" {{ $expiryStatus=='ok'?'selected':'' }}>OK (>30d)</option>
        <option value="expiring_soon" {{ $expiryStatus=='expiring_soon'?'selected':'' }}>Expiring Soon (≤30d)</option>
        <option value="expired" {{ $expiryStatus=='expired'?'selected':'' }}>Expired</option>
      </select>
    </div>
    <div class="col-auto"><button class="btn btn-warning btn-sm">Filter</button></div>
  </form>

  <div class="card bg-dark border-secondary">
    <div class="table-responsive">
      <table class="table table-dark table-hover table-sm mb-0">
        <thead class="text-warning">
          <tr>
            <th>FG Code</th><th>FG Name</th><th>Batch</th><th>Warehouse</th>
            <th>Mfg Date</th><th>Expiry Date</th>
            <th class="text-end">Qty In</th><th class="text-end">Qty Out</th>
            <th class="text-end">Reserved</th><th class="text-end">Available</th>
            <th class="text-end">Cost Rate</th><th class="text-end">FIFO Value</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          @php $totalValue = 0; @endphp
          @forelse($stocks->groupBy('fg_id') as $fgId => $rows)
          @php $fifo = $fgValuation[$fgId] ?? null; @endphp
          @foreach($rows as $s)
          @php
            $avail = $s->qty_in - $s->qty_out - $s->qty_reserved;
            $val   = $avail * $s->cost_rate;
            $totalValue += max(0, $val);
            $expiryBg = '';
            $expiryLabel = $s->expiry_date ? $s->expiry_date->format('d/m/Y') : '—';
            if ($s->expiry_date) {
              $days = now()->diffInDays($s->expiry_date, false);
              if ($days < 0) $expiryBg = 'table-danger';
              elseif ($days <= 30) $expiryBg = 'table-warning';
            }
          @endphp
          <tr class="{{ $expiryBg }}">
            <td><code>{{ $s->fg_code }}</code></td>
            <td>{{ $s->fg_name }}</td>
            <td><code>{{ $s->batch_no }}</code></td>
            <td>{{ $s->warehouse?->name }}</td>
            <td>{{ $s->mfg_date?->format('d/m/Y') }}</td>
            <td>
              {{ $expiryLabel }}
              @if($s->expiry_date)
              @php $d = now()->diffInDays($s->expiry_date, false) @endphp
              @if($d < 0)<span class="badge bg-danger ms-1">Expired</span>
              @elseif($d <= 30)<span class="badge bg-warning text-dark ms-1">{{ $d }}d left</span>
              @endif
              @endif
            </td>
            <td class="text-end">{{ number_format($s->qty_in,4) }}</td>
            <td class="text-end">{{ number_format($s->qty_out,4) }}</td>
            <td class="text-end">{{ number_format($s->qty_reserved,4) }}</td>
            <td class="text-end fw-bold {{ $avail > 0 ? 'text-success':'text-danger' }}">{{ number_format($avail,4) }}</td>
            <td class="text-end">{{ number_format($s->cost_rate,4) }}</td>
            <td class="text-end">{{ number_format(max(0,$val),2) }}</td>
            <td><span class="badge bg-{{ $s->status=='available'?'success':($s->status=='on_hold'?'warning':($s->status=='expired'?'danger':'secondary')) }}">{{ $s->status }}</span></td>
          </tr>
          @endforeach
          @empty
          <tr><td colspan="13" class="text-center text-muted py-4">No stock records found.</td></tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr class="text-warning fw-bold">
            <td colspan="11" class="text-end">Total FIFO Value:</td>
            <td class="text-end">{{ number_format($totalValue,2) }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
