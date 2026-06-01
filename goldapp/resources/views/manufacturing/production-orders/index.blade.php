@extends('layouts.app')
@section('title', 'Production Orders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-gear-fill me-2"></i>Production Orders</h4>
    <a href="{{ route('production-orders-new.create') }}" class="btn btn-gold btn-sm"><i class="bi bi-plus-lg me-1"></i>New Order</a>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3"><input type="text" name="q" value="{{ $q ?? '' }}" class="form-control form-control-sm form-erp" placeholder="FG Name / Order No..."></div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm form-erp">
                <option value="">All Status</option>
                @foreach(['draft','released','in_progress','completed','cancelled'] as $st)
                    <option value="{{ $st }}" {{ ($status ?? '') === $st ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$st)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="priority" class="form-select form-select-sm form-erp">
                <option value="">All Priority</option>
                @foreach(['low','normal','high','urgent'] as $p)
                    <option value="{{ $p }}" {{ ($priority ?? '') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="from" value="{{ $from ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-md-1"><input type="date" name="to" value="{{ $to ?? '' }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-auto"><button class="btn btn-gold btn-sm"><i class="bi bi-search"></i></button><a href="{{ route('production-orders-new.index') }}" class="btn btn-outline-secondary btn-sm ms-1">Clear</a></div>
    </form>
</div>

<div class="card-erp">
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead>
                <tr><th>Order No</th><th>Date</th><th>Finished Good</th><th>Batch</th><th class="text-end">Order Qty</th><th class="text-end">Produced</th><th>Progress</th><th>Priority</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse($orders as $o)
                @php
                    $pct = $o->order_qty > 0 ? min(100, round(($o->produced_qty / $o->order_qty) * 100)) : 0;
                    $priColors = ['low'=>'secondary','normal'=>'info','high'=>'warning','urgent'=>'danger'];
                    $statusColors = ['draft'=>'secondary','released'=>'primary','in_progress'=>'warning','completed'=>'success','cancelled'=>'danger'];
                @endphp
                <tr>
                    <td><a href="{{ route('production-orders-new.show', $o) }}" class="text-gold-light fw-bold">{{ $o->slno }}</a></td>
                    <td>{{ \Carbon\Carbon::parse($o->order_date)->format('d/m/Y') }}</td>
                    <td><strong>{{ $o->fg_name }}</strong><br><small class="text-muted">{{ $o->fg_code }}</small></td>
                    <td><code>{{ $o->batch_no }}</code></td>
                    <td class="text-end">{{ number_format($o->order_qty, 3) }} {{ $o->unit }}</td>
                    <td class="text-end">{{ number_format($o->produced_qty, 3) }}</td>
                    <td style="min-width:100px">
                        <div class="progress" style="height:6px;background:#2a2a40">
                            <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : ($pct > 50 ? 'bg-warning' : 'bg-info') }}" style="width:{{ $pct }}%"></div>
                        </div>
                        <small class="text-muted">{{ $pct }}%</small>
                    </td>
                    <td><span class="badge bg-{{ $priColors[$o->priority] ?? 'secondary' }}">{{ ucfirst($o->priority) }}</span></td>
                    <td><span class="badge bg-{{ $statusColors[$o->status] ?? 'secondary' }}">{{ ucwords(str_replace('_',' ',$o->status)) }}</span></td>
                    <td>
                        <a href="{{ route('production-orders-new.show', $o) }}" class="btn btn-xs btn-outline-info me-1"><i class="bi bi-eye"></i></a>
                        @if(in_array($o->status, ['draft','released']))
                            <a href="{{ route('production-orders-new.edit', $o) }}" class="btn btn-xs btn-outline-warning me-1"><i class="bi bi-pencil"></i></a>
                        @endif
                        @if(in_array($o->status, ['released','in_progress']))
                            <a href="{{ route('production-orders-new.issueForm', $o->id) }}" class="btn btn-xs btn-outline-success me-1" title="Issue Materials"><i class="bi bi-box-arrow-right"></i></a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted py-4">No production orders found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $orders->links() }}</div>
</div>
@endsection
