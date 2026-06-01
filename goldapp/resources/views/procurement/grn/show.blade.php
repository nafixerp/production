@extends('layouts.app')
@section('title', 'GRN: '.$grn->slno)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-box-arrow-in-down me-2"></i>GRN: {{ $grn->slno }}</h4>
    <a href="{{ route('grn.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-8">
        <div class="card-erp p-3">
            <div class="row g-2 small">
                <div class="col-4"><span class="text-muted">GRN No:</span> <strong class="text-gold-light">{{ $grn->slno }}</strong></div>
                <div class="col-4"><span class="text-muted">Date:</span> {{ \Carbon\Carbon::parse($grn->grn_date)->format('d/m/Y') }}</div>
                <div class="col-4"><span class="text-muted">Status:</span>
                    @php $c = match($grn->status) { 'approved'=>'success','rejected'=>'danger','qc_pending'=>'warning',default=>'secondary' }; @endphp
                    <span class="badge bg-{{ $c }}">{{ ucwords(str_replace('_',' ',$grn->status)) }}</span>
                </div>
                <div class="col-4"><span class="text-muted">Supplier:</span> <strong>{{ $grn->supplier_name }}</strong></div>
                <div class="col-4"><span class="text-muted">Supplier Invoice:</span> {{ $grn->supplier_invoice_no ?? '—' }}</div>
                <div class="col-4"><span class="text-muted">Inv Date:</span> {{ $grn->supplier_invoice_date ? \Carbon\Carbon::parse($grn->supplier_invoice_date)->format('d/m/Y') : '—' }}</div>
                @if($grn->purchaseOrder)<div class="col-6"><span class="text-muted">Linked PO:</span> <a href="{{ route('purchase-orders.show', $grn->purchaseOrder) }}" class="text-gold-light">{{ $grn->purchaseOrder->slno }}</a></div>@endif
                @if($grn->qc_result)<div class="col-4"><span class="text-muted">QC Result:</span> <span class="badge bg-{{ $grn->qc_result==='pass'?'success':($grn->qc_result==='fail'?'danger':'warning') }}">{{ ucfirst($grn->qc_result) }}</span></div>@endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-erp p-3">
            <table class="table table-sm text-end mb-0">
                <tr><td class="text-muted">Taxable Amount:</td><td>₹{{ number_format($grn->taxable_amount, 2) }}</td></tr>
                <tr><td class="text-muted">SGST:</td><td>₹{{ number_format($grn->sgst, 2) }}</td></tr>
                <tr><td class="text-muted">CGST:</td><td>₹{{ number_format($grn->cgst, 2) }}</td></tr>
                <tr><td class="text-muted">IGST:</td><td>₹{{ number_format($grn->igst, 2) }}</td></tr>
                <tr class="table-gold"><td class="fw-bold">Net Amount:</td><td class="fw-bold text-gold">₹{{ number_format($grn->net_amount, 2) }}</td></tr>
            </table>
        </div>
    </div>
</div>

<div class="card-erp p-3">
    <h6 class="text-gold mb-2">Received Items</h6>
    <div class="table-responsive">
        <table class="table table-sm table-erp mb-0">
            <thead><tr><th>Type</th><th>Code</th><th>Item Name</th><th>Unit</th><th class="text-end">Ordered</th><th class="text-end">Received</th><th class="text-end">Accepted</th><th class="text-end">Rejected</th><th class="text-end">Rate</th><th class="text-end">Amount</th><th>Batch</th><th>Mfg Date</th><th>Expiry</th></tr></thead>
            <tbody>
            @foreach($grn->items as $item)
                <tr>
                    <td><span class="badge bg-secondary">{{ $item->item_type }}</span></td>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="text-end">{{ number_format($item->ordered_qty, 3) }}</td>
                    <td class="text-end">{{ number_format($item->received_qty, 3) }}</td>
                    <td class="text-end text-success">{{ number_format($item->accepted_qty, 3) }}</td>
                    <td class="text-end {{ $item->rejected_qty > 0 ? 'text-danger' : '' }}">{{ number_format($item->rejected_qty, 3) }}</td>
                    <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                    <td class="text-end">₹{{ number_format($item->amount, 2) }}</td>
                    <td>{{ $item->batch_no ?? '—' }}</td>
                    <td>{{ $item->mfg_date ? \Carbon\Carbon::parse($item->mfg_date)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('d/m/Y') : '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
