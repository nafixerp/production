@extends('layouts.app')
@section('title', 'Stock Movements Ledger')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-arrow-left-right me-2"></i>Stock Movements Ledger</h4>
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
        <div class="col-md-2">
            <select name="movement_type" class="form-select form-select-sm form-erp">
                <option value="">All Movements</option>
                @foreach(['IN','OUT','TRANSFER','ADJUSTMENT','RETURN','WRITEOFF'] as $mt)
                    <option value="{{ $mt }}" {{ ($movType ?? '') === $mt ? 'selected' : '' }}>{{ $mt }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="warehouse_id" class="form-select form-select-sm form-erp">
                <option value="">All Warehouses</option>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}" {{ ($warehouseId ?? '') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="text" name="q" value="{{ $search ?? '' }}" class="form-control form-control-sm form-erp" placeholder="Item name..."></div>
        <div class="col-md-1"><input type="date" name="from" value="{{ $from ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-md-1"><input type="date" name="to" value="{{ $to ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-auto"><button class="btn btn-gold btn-sm"><i class="bi bi-search"></i></button><a href="{{ route('stock-movements-new.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a></div>
    </form>
</div>

<!-- Totals -->
<div class="row g-2 mb-3">
    <div class="col-md-3"><div class="card-erp p-2 text-center"><div class="small text-muted">Total IN Qty</div><div class="text-success fw-bold">{{ number_format($totals['in_qty'], 3) }}</div></div></div>
    <div class="col-md-3"><div class="card-erp p-2 text-center"><div class="small text-muted">Total OUT Qty</div><div class="text-danger fw-bold">{{ number_format($totals['out_qty'], 3) }}</div></div></div>
    <div class="col-md-3"><div class="card-erp p-2 text-center"><div class="small text-muted">IN Value</div><div class="text-success fw-bold">₹{{ number_format($totals['in_val'], 2) }}</div></div></div>
    <div class="col-md-3"><div class="card-erp p-2 text-center"><div class="small text-muted">OUT Value</div><div class="text-danger fw-bold">₹{{ number_format($totals['out_val'], 2) }}</div></div></div>
</div>

<div class="card-erp">
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead>
                <tr><th>Date</th><th>Type</th><th>Movement</th><th>Item</th><th>Warehouse</th><th>Batch</th><th class="text-end">Qty</th><th>Unit</th><th class="text-end">Cost Rate</th><th class="text-end">Cost Amount</th><th class="text-end">Running Balance</th><th>Ref</th><th>Narration</th></tr>
            </thead>
            <tbody>
            @forelse($movements as $m)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($m->movement_date)->format('d/m/Y') }}</td>
                    <td><span class="badge bg-secondary">{{ $m->item_type }}</span></td>
                    <td>
                        @php $mc = ['IN'=>'success','OUT'=>'danger','TRANSFER'=>'info','ADJUSTMENT'=>'warning','RETURN'=>'primary','WRITEOFF'=>'dark']; @endphp
                        <span class="badge bg-{{ $mc[$m->movement_type] ?? 'secondary' }}">{{ $m->movement_type }}</span>
                    </td>
                    <td><strong>{{ $m->item_name }}</strong><br><small class="text-muted">{{ $m->item_code }}</small></td>
                    <td>{{ $m->warehouse?->name ?? '—' }}</td>
                    <td><code>{{ $m->batch_no ?? '—' }}</code></td>
                    <td class="text-end {{ in_array($m->movement_type, ['IN','RETURN']) ? 'text-success' : 'text-danger' }} fw-bold">
                        {{ in_array($m->movement_type, ['IN','RETURN']) ? '+' : '-' }}{{ number_format($m->qty, 4) }}
                    </td>
                    <td>{{ $m->unit }}</td>
                    <td class="text-end">₹{{ number_format($m->cost_rate, 4) }}</td>
                    <td class="text-end">₹{{ number_format($m->cost_amount, 2) }}</td>
                    <td class="text-end text-gold">{{ number_format($m->running_balance, 4) }}</td>
                    <td><small>{{ $m->ref_type }}/{{ $m->ref_id }}</small></td>
                    <td class="small text-muted">{{ Str::limit($m->narration, 40) }}</td>
                </tr>
            @empty
                <tr><td colspan="13" class="text-center text-muted py-4">No movements found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $movements->links() }}</div>
</div>
@endsection
