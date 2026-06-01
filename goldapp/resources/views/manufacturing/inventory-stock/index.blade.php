@extends('layouts.app')
@section('title', 'Current Inventory Stock')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-boxes me-2"></i>Inventory Stock</h4>
    @if($expiryAlerts > 0)
        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>{{ $expiryAlerts }} items expiring in 30 days</span>
    @endif
</div>

<!-- Summary -->
<div class="row g-3 mb-3">
    @foreach($summary as $s)
    <div class="col">
        <div class="card-erp p-3 text-center">
            <div class="small text-muted">{{ $s->item_type }}</div>
            <div class="fw-bold text-gold">{{ number_format($s->total_qty, 0) }}</div>
            <div class="small text-muted">₹{{ number_format($s->total_value, 0) }}</div>
            <div class="x-small text-muted">{{ $s->items }} batches</div>
        </div>
    </div>
    @endforeach
</div>

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end p-2">
        <div class="col-md-2">
            <select name="item_type" class="form-select form-select-sm form-erp">
                <option value="">All Types</option>
                @foreach(['RM','PM','FG','SFG'] as $t)
                    <option value="{{ $t }}" {{ ($itemType ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="warehouse_id" class="form-select form-select-sm form-erp">
                <option value="">All Warehouses</option>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}" {{ ($warehouseId ?? '') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3"><input type="text" name="q" value="{{ $search ?? '' }}" class="form-control form-control-sm form-erp" placeholder="Item name / code..."></div>
        <div class="col-md-2">
            <select name="expiring" class="form-select form-select-sm form-erp">
                <option value="">Expiry Filter</option>
                <option value="7" {{ ($expiring ?? '') === '7' ? 'selected' : '' }}>Expiring in 7 days</option>
                <option value="30" {{ ($expiring ?? '') === '30' ? 'selected' : '' }}>Expiring in 30 days</option>
                <option value="90" {{ ($expiring ?? '') === '90' ? 'selected' : '' }}>Expiring in 90 days</option>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-gold btn-sm"><i class="bi bi-search"></i></button><a href="{{ route('inventory-stock-new.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a></div>
    </form>
</div>

<div class="card-erp">
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead>
                <tr><th>Type</th><th>Code</th><th>Item Name</th><th>Warehouse</th><th>Batch</th><th class="text-end">Qty In</th><th class="text-end">Qty Out</th><th class="text-end">Balance</th><th>Unit</th><th class="text-end">Cost Rate</th><th class="text-end">Stock Value</th><th>Expiry</th><th>Status</th></tr>
            </thead>
            <tbody>
            @forelse($stocks as $s)
                @php
                    $balance = $s->qty_in - $s->qty_out;
                    $expiryDate = $s->expiry_date;
                    $daysToExpiry = $expiryDate ? now()->diffInDays($expiryDate, false) : null;
                    $expClass = $daysToExpiry !== null ? ($daysToExpiry < 0 ? 'text-danger' : ($daysToExpiry <= 7 ? 'text-danger' : ($daysToExpiry <= 30 ? 'text-warning' : 'text-success'))) : '';
                @endphp
                <tr>
                    <td><span class="badge bg-secondary">{{ $s->item_type }}</span></td>
                    <td>{{ $s->item_code }}</td>
                    <td><strong>{{ $s->item_name }}</strong></td>
                    <td>{{ $s->warehouse?->name ?? '—' }}</td>
                    <td><code>{{ $s->batch_no ?? '—' }}</code></td>
                    <td class="text-end text-success">{{ number_format($s->qty_in, 3) }}</td>
                    <td class="text-end text-danger">{{ number_format($s->qty_out, 3) }}</td>
                    <td class="text-end fw-bold text-gold">{{ number_format($balance, 3) }}</td>
                    <td>{{ $s->unit }}</td>
                    <td class="text-end">₹{{ number_format($s->cost_rate, 4) }}</td>
                    <td class="text-end">₹{{ number_format($balance * $s->cost_rate, 2) }}</td>
                    <td class="{{ $expClass }}">
                        @if($expiryDate)
                            {{ $expiryDate->format('d/m/Y') }}
                            @if($daysToExpiry !== null && $daysToExpiry < 0)<br><small>EXPIRED</small>
                            @elseif($daysToExpiry !== null && $daysToExpiry <= 30)<br><small>{{ $daysToExpiry }}d</small>@endif
                        @else—@endif
                    </td>
                    <td>
                        @php $sc = ['available'=>'success','expired'=>'danger','on_hold'=>'warning','consumed'=>'secondary']; @endphp
                        <span class="badge bg-{{ $sc[$s->status] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$s->status)) }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="13" class="text-center text-muted py-4">No stock records found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $stocks->links() }}</div>
</div>
@endsection
