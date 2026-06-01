@extends('layouts.app')
@section('title', 'Production Costing Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-calculator-fill me-2"></i>Production Costing — Variance Report</h4>
</div>

<div class="card-erp mb-3">
    <form method="GET" class="row g-2 align-items-end p-2">
        <div class="col-md-3"><input type="text" name="q" value="{{ $search ?? '' }}" class="form-control form-control-sm form-erp" placeholder="FG Name..."></div>
        <div class="col-md-2"><input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-md-2"><input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm form-erp"></div>
        <div class="col-auto"><button class="btn btn-gold btn-sm"><i class="bi bi-search"></i></button></div>
    </form>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-3">
    <div class="col-md-2"><div class="card-erp p-3 text-center"><div class="small text-muted">Orders</div><div class="text-gold fw-bold fs-4">{{ $summary['total_orders'] }}</div></div></div>
    <div class="col-md-2"><div class="card-erp p-3 text-center"><div class="small text-muted">RM Cost</div><div class="text-warning fw-bold">₹{{ number_format($summary['total_rm_cost'], 0) }}</div></div></div>
    <div class="col-md-2"><div class="card-erp p-3 text-center"><div class="small text-muted">PM Cost</div><div class="text-warning fw-bold">₹{{ number_format($summary['total_pm_cost'], 0) }}</div></div></div>
    <div class="col-md-2"><div class="card-erp p-3 text-center"><div class="small text-muted">Labour</div><div class="text-info fw-bold">₹{{ number_format($summary['total_labour'], 0) }}</div></div></div>
    <div class="col-md-2"><div class="card-erp p-3 text-center"><div class="small text-muted">Total Cost</div><div class="text-gold fw-bold">₹{{ number_format($summary['total_cost'], 0) }}</div></div></div>
    <div class="col-md-2">
        <div class="card-erp p-3 text-center">
            <div class="small text-muted">Variance</div>
            <div class="fw-bold {{ $summary['total_variance'] > 0 ? 'text-danger' : 'text-success' }}">
                ₹{{ number_format(abs($summary['total_variance']), 0) }}
                {{ $summary['total_variance'] > 0 ? '(over)' : '(under)' }}
            </div>
        </div>
    </div>
</div>

<div class="card-erp">
    <div class="table-responsive">
        <table class="table table-erp table-hover mb-0">
            <thead>
                <tr>
                    <th>Order No</th>
                    <th>Finished Good</th>
                    <th>Batch</th>
                    <th class="text-end">Order Qty</th>
                    <th class="text-end">Produced</th>
                    <th class="text-end">RM Cost</th>
                    <th class="text-end">PM Cost</th>
                    <th class="text-end">Labour</th>
                    <th class="text-end">Overhead</th>
                    <th class="text-end">Total Cost</th>
                    <th class="text-end">Cost/Unit</th>
                    <th class="text-end">Variance</th>
                </tr>
            </thead>
            <tbody>
            @forelse($costings as $c)
                <tr>
                    <td><a href="{{ route('production-orders-new.show', $c->production_order_id) }}" class="text-gold-light small">{{ $c->productionOrder?->slno }}</a></td>
                    <td>{{ $c->fg_name }}</td>
                    <td><code>{{ $c->batch_no }}</code></td>
                    <td class="text-end">{{ number_format($c->order_qty, 3) }}</td>
                    <td class="text-end">{{ number_format($c->produced_qty, 3) }}</td>
                    <td class="text-end">₹{{ number_format($c->rm_cost, 2) }}</td>
                    <td class="text-end">₹{{ number_format($c->pm_cost, 2) }}</td>
                    <td class="text-end">₹{{ number_format($c->labour_cost, 2) }}</td>
                    <td class="text-end">₹{{ number_format($c->overhead_cost, 2) }}</td>
                    <td class="text-end fw-bold text-gold">₹{{ number_format($c->total_cost, 2) }}</td>
                    <td class="text-end">₹{{ number_format($c->cost_per_unit, 4) }}</td>
                    <td class="text-end {{ $c->variance > 0 ? 'text-danger' : ($c->variance < 0 ? 'text-success' : 'text-muted') }}">
                        {{ $c->variance != 0 ? ($c->variance > 0 ? '+' : '') . '₹'.number_format($c->variance, 2) : '—' }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="12" class="text-center text-muted py-4">No costing records found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">{{ $costings->links() }}</div>
</div>
@endsection
