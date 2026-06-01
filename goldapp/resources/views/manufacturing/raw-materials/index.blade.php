@extends('layouts.app')
@section('title', 'Raw Materials')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-box-seam me-2"></i>Raw Materials</h4>
    <a href="{{ route('raw-materials-new.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg me-1"></i>New Raw Material</a>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><input type="text" name="q" value="{{ $q ?? '' }}" class="form-control form-control-sm form-erp" placeholder="Name / Code..."></div>
        <div class="col-md-2">
            <select name="category" class="form-select form-select-sm form-erp">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ ($category ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm form-erp">
                <option value="">All Status</option>
                <option value="1" {{ ($status ?? '') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ ($status ?? '') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-auto"><button class="btn btn-gold btn-sm"><i class="bi bi-search"></i></button><a href="{{ route('raw-materials-new.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a></div>
    </form>
</div>

<div class="card-erp">
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead>
                <tr><th>Code</th><th>Name</th><th>Category</th><th>Unit</th><th>HSN</th><th class="text-end">Reorder Qty</th><th class="text-end">Min Stock</th><th class="text-end">Current Stock</th><th>FSSAI</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($materials as $m)
                @php
                    $stock = $stocks[$m->id] ?? 0;
                    $belowReorder = $stock <= $m->reorder_qty;
                    $belowMin = $stock < $m->min_stock;
                @endphp
                <tr>
                    <td><code class="text-gold-light">{{ $m->code }}</code></td>
                    <td><strong>{{ $m->name }}</strong></td>
                    <td>{{ $m->category }}</td>
                    <td>{{ $m->unit?->name ?? '—' }}</td>
                    <td>{{ $m->hsn_code }}</td>
                    <td class="text-end">{{ number_format($m->reorder_qty, 3) }}</td>
                    <td class="text-end">{{ number_format($m->min_stock, 3) }}</td>
                    <td class="text-end fw-bold {{ $belowMin ? 'text-danger' : ($belowReorder ? 'text-warning' : 'text-success') }}">
                        {{ number_format($stock, 3) }}
                        @if($belowMin)<i class="bi bi-exclamation-triangle-fill ms-1" title="Below minimum!"></i>@elseif($belowReorder)<i class="bi bi-exclamation-circle ms-1 text-warning" title="Reorder needed"></i>@endif
                    </td>
                    <td>{{ $m->is_fssai_regulated ? '<span class="badge bg-info">FSSAI</span>' : '—' }}</td>
                    <td><span class="badge bg-{{ $m->status ? 'success' : 'secondary' }}">{{ $m->status ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <a href="{{ route('raw-materials-new.edit', $m) }}" class="btn btn-xs btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('raw-materials-new.destroy', $m) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="11" class="text-center text-muted py-4">No raw materials found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $materials->links() }}</div>
</div>
@endsection
