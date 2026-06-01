@extends('layouts.app')
@section('title', 'PO: '.$purchaseOrder->slno)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-file-earmark-text me-2"></i>{{ $purchaseOrder->slno }}</h4>
    <div>
        @if($purchaseOrder->status === 'draft')
            <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-sm btn-outline-warning me-1"><i class="bi bi-pencil me-1"></i>Edit</a>
            <form method="POST" action="{{ route('purchase-orders.approve', $purchaseOrder->id) }}" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-success me-1"><i class="bi bi-check-circle me-1"></i>Approve</button>
            </form>
        @endif
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

<div class="row g-3 mb-3">
    <div class="col-md-8">
        <div class="card-erp p-3">
            <div class="row g-2 small">
                <div class="col-4"><span class="text-muted">PO No:</span> <strong class="text-gold-light">{{ $purchaseOrder->slno }}</strong></div>
                <div class="col-4"><span class="text-muted">Date:</span> {{ \Carbon\Carbon::parse($purchaseOrder->po_date)->format('d/m/Y') }}</div>
                <div class="col-4"><span class="text-muted">Delivery:</span> {{ $purchaseOrder->delivery_date ? \Carbon\Carbon::parse($purchaseOrder->delivery_date)->format('d/m/Y') : '—' }}</div>
                <div class="col-4"><span class="text-muted">Supplier:</span> <strong>{{ $purchaseOrder->supplier_name }}</strong></div>
                <div class="col-4"><span class="text-muted">Currency:</span> {{ $purchaseOrder->currency }}</div>
                <div class="col-4"><span class="text-muted">Payment Terms:</span> {{ $purchaseOrder->payment_terms }}</div>
                <div class="col-12"><span class="text-muted">Shipping Address:</span> {{ $purchaseOrder->shipping_address }}</div>
                @if($purchaseOrder->narration)<div class="col-12"><span class="text-muted">Narration:</span> {{ $purchaseOrder->narration }}</div>@endif
                @if($purchaseOrder->approved_by)
                    <div class="col-12 text-success small"><i class="bi bi-check-circle me-1"></i>Approved on {{ $purchaseOrder->approved_at }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-erp p-3">
            <table class="table table-sm text-end mb-0">
                <tr><td class="text-muted">Taxable Amount:</td><td class="fw-bold">₹{{ number_format($purchaseOrder->taxable_amount, 2) }}</td></tr>
                <tr><td class="text-muted">Discount:</td><td>- ₹{{ number_format($purchaseOrder->discount_amount, 2) }}</td></tr>
                <tr><td class="text-muted">SGST:</td><td>₹{{ number_format($purchaseOrder->sgst, 2) }}</td></tr>
                <tr><td class="text-muted">CGST:</td><td>₹{{ number_format($purchaseOrder->cgst, 2) }}</td></tr>
                <tr><td class="text-muted">IGST:</td><td>₹{{ number_format($purchaseOrder->igst, 2) }}</td></tr>
                <tr><td class="text-muted">TCS:</td><td>₹{{ number_format($purchaseOrder->tcs, 2) }}</td></tr>
                <tr class="table-gold"><td class="fw-bold">Net Amount:</td><td class="fw-bold text-gold">₹{{ number_format($purchaseOrder->net_amount, 2) }}</td></tr>
            </table>
        </div>
    </div>
</div>

<div class="card-erp p-3 mb-3">
    <h6 class="text-gold mb-2">Order Items</h6>
    <div class="table-responsive">
        <table class="table table-sm table-erp mb-0">
            <thead><tr><th>Type</th><th>Code</th><th>Item Name</th><th>HSN</th><th>Unit</th><th class="text-end">Ordered Qty</th><th class="text-end">Pending Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th><th class="text-end">Net</th></tr></thead>
            <tbody>
            @foreach($purchaseOrder->items as $item)
                <tr>
                    <td><span class="badge bg-secondary">{{ $item->item_type }}</span></td>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->hsn_code }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="text-end">{{ number_format($item->qty, 3) }}</td>
                    <td class="text-end {{ $item->pending_qty > 0 ? 'text-warning' : 'text-success' }}">{{ number_format($item->pending_qty, 3) }}</td>
                    <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                    <td class="text-end">₹{{ number_format($item->amount, 2) }}</td>
                    <td class="text-end">₹{{ number_format($item->net_amount, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($purchaseOrder->grns && $purchaseOrder->grns->count() > 0)
<div class="card-erp p-3">
    <h6 class="text-gold mb-2">GRN History</h6>
    <div class="table-responsive">
        <table class="table table-sm table-erp mb-0">
            <thead><tr><th>GRN No</th><th>Date</th><th class="text-end">Net Amount</th><th>Status</th><th>QC</th></tr></thead>
            <tbody>
            @foreach($purchaseOrder->grns as $grn)
                <tr>
                    <td><a href="{{ route('grn.show', $grn) }}" class="text-gold-light">{{ $grn->slno }}</a></td>
                    <td>{{ \Carbon\Carbon::parse($grn->grn_date)->format('d/m/Y') }}</td>
                    <td class="text-end">₹{{ number_format($grn->net_amount, 2) }}</td>
                    <td><span class="badge bg-{{ $grn->status === 'approved' ? 'success' : 'secondary' }}">{{ ucfirst($grn->status) }}</span></td>
                    <td>{{ $grn->qc_result ? ucfirst($grn->qc_result) : '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
