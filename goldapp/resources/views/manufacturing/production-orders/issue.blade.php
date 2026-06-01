@extends('layouts.app')
@section('title', 'Issue Materials to Production')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-box-arrow-right me-2"></i>Issue Materials — {{ $order->slno }}</h4>
    <a href="{{ route('production-orders-new.show', $order) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card-erp p-3 mb-3">
    <div class="row g-2 small">
        <div class="col-4"><span class="text-muted">Order No:</span> <strong class="text-gold-light">{{ $order->slno }}</strong></div>
        <div class="col-4"><span class="text-muted">Finished Good:</span> <strong>{{ $order->fg_name }}</strong></div>
        <div class="col-4"><span class="text-muted">Order Qty:</span> {{ number_format($order->order_qty, 3) }} {{ $order->unit }}</div>
    </div>
</div>

<form method="POST" action="{{ route('production-orders-new.issue', $order->id) }}">
    @csrf
    <div class="card-erp p-3 mb-3">
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label-erp">Issue Date</label>
                <input type="date" name="issue_date" value="{{ now()->format('Y-m-d') }}" class="form-control form-erp">
            </div>
        </div>

        <h6 class="text-gold mb-2">BOM Items to Issue</h6>
        <div class="table-responsive">
            <table class="table table-sm table-erp mb-0">
                <thead><tr><th>Type</th><th>Code</th><th>Item Name</th><th>Unit</th><th class="text-end">Required</th><th class="text-end">Already Issued</th><th class="text-end">Pending</th><th class="text-end">Issue Qty Now</th></tr></thead>
                <tbody>
                @foreach($order->bom as $b)
                    @php $pending = max(0, $b->required_qty - $b->issued_qty); @endphp
                    <tr>
                        <td><span class="badge bg-secondary">{{ $b->item_type }}</span></td>
                        <td>{{ $b->item_code }}</td>
                        <td>{{ $b->item_name }}</td>
                        <td>{{ $b->unit }}</td>
                        <td class="text-end">{{ number_format($b->required_qty, 4) }}</td>
                        <td class="text-end text-success">{{ number_format($b->issued_qty, 4) }}</td>
                        <td class="text-end {{ $pending > 0 ? 'text-warning' : 'text-muted' }}">{{ number_format($pending, 4) }}</td>
                        <td>
                            <input type="number" name="issue_qty[{{ $b->id }}]" value="{{ $pending }}"
                                class="form-control form-control-sm form-erp text-end"
                                step="0.0001" min="0" max="{{ $pending }}"
                                {{ $pending <= 0 ? 'disabled' : '' }}>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert alert-warning d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>This will deduct materials from inventory using FIFO method and post daybook entries (WIP Debit, RM Stock Credit).</span>
    </div>

    <button type="submit" class="btn btn-gold btn-lg"><i class="bi bi-box-arrow-right me-1"></i>Issue Materials to Production</button>
    <a href="{{ route('production-orders-new.show', $order) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
</form>
@endsection
