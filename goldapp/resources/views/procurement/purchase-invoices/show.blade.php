@extends('layouts.app')
@section('title', 'Invoice: '.$invoice->slno)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="text-gold mb-0"><i class="bi bi-receipt me-2"></i>{{ $invoice->slno }}</h4>
    <a href="{{ route('purchase-invoices-new.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-8">
        <div class="card-erp p-3">
            <div class="row g-2 small">
                <div class="col-4"><span class="text-muted">Invoice No:</span> <strong class="text-gold-light">{{ $invoice->slno }}</strong></div>
                <div class="col-4"><span class="text-muted">Date:</span> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</div>
                <div class="col-4"><span class="text-muted">Supplier:</span> <strong>{{ $invoice->supplier_name }}</strong></div>
                <div class="col-4"><span class="text-muted">Supplier Bill No:</span> {{ $invoice->supplier_bill_no ?? '—' }}</div>
                <div class="col-4"><span class="text-muted">Supplier Bill Date:</span> {{ $invoice->supplier_bill_date ? \Carbon\Carbon::parse($invoice->supplier_bill_date)->format('d/m/Y') : '—' }}</div>
                <div class="col-4"><span class="text-muted">Payment Mode:</span> {{ ucfirst($invoice->payment_mode) }}</div>
                @if($invoice->grn)<div class="col-4"><span class="text-muted">GRN:</span> <a href="{{ route('grn.show', $invoice->grn) }}" class="text-gold-light">{{ $invoice->grn->slno }}</a></div>@endif
                @if($invoice->narration)<div class="col-12"><span class="text-muted">Narration:</span> {{ $invoice->narration }}</div>@endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-erp p-3">
            <table class="table table-sm text-end mb-0">
                <tr><td class="text-muted">Taxable:</td><td>₹{{ number_format($invoice->taxable_amount, 2) }}</td></tr>
                <tr><td class="text-muted">Discount:</td><td>- ₹{{ number_format($invoice->discount, 2) }}</td></tr>
                <tr><td class="text-muted">SGST:</td><td>₹{{ number_format($invoice->sgst, 2) }}</td></tr>
                <tr><td class="text-muted">CGST:</td><td>₹{{ number_format($invoice->cgst, 2) }}</td></tr>
                <tr><td class="text-muted">IGST:</td><td>₹{{ number_format($invoice->igst, 2) }}</td></tr>
                <tr><td class="text-muted">TCS:</td><td>₹{{ number_format($invoice->tcs, 2) }}</td></tr>
                <tr><td class="text-muted">Other Charges:</td><td>₹{{ number_format($invoice->other_charges, 2) }}</td></tr>
                <tr class="table-gold"><td class="fw-bold">Net Amount:</td><td class="fw-bold text-gold">₹{{ number_format($invoice->net_amount, 2) }}</td></tr>
                <tr class="text-success"><td>Paid:</td><td>₹{{ number_format($invoice->paid_amount, 2) }}</td></tr>
                <tr class="{{ $invoice->balance_amount > 0 ? 'text-warning' : '' }}"><td>Balance:</td><td>₹{{ number_format($invoice->balance_amount, 2) }}</td></tr>
            </table>
        </div>
    </div>
</div>

<div class="card-erp p-3 mb-3">
    <h6 class="text-gold mb-2">Invoice Items</h6>
    <div class="table-responsive">
        <table class="table table-sm table-erp mb-0">
            <thead><tr><th>Type</th><th>Code</th><th>Item Name</th><th>HSN</th><th>Unit</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th><th class="text-end">SGST</th><th class="text-end">CGST</th><th class="text-end">IGST</th><th class="text-end">Net</th></tr></thead>
            <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td><span class="badge bg-secondary">{{ $item->item_type }}</span></td>
                    <td>{{ $item->item_code }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->hsn_code }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="text-end">{{ number_format($item->qty, 3) }}</td>
                    <td class="text-end">₹{{ number_format($item->rate, 2) }}</td>
                    <td class="text-end">₹{{ number_format($item->amount, 2) }}</td>
                    <td class="text-end">₹{{ number_format($item->sgst, 2) }}</td>
                    <td class="text-end">₹{{ number_format($item->cgst, 2) }}</td>
                    <td class="text-end">₹{{ number_format($item->igst, 2) }}</td>
                    <td class="text-end">₹{{ number_format($item->net_amount, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($daybookEntries->count() > 0)
<div class="card-erp p-3">
    <h6 class="text-gold mb-2"><i class="bi bi-journal-text me-1"></i>Daybook Entries (Double-Entry)</h6>
    <div class="table-responsive">
        <table class="table table-sm table-erp mb-0">
            <thead><tr><th>Account</th><th>Particular</th><th class="text-end">Debit (Dr)</th><th class="text-end">Credit (Cr)</th></tr></thead>
            <tbody>
            @foreach($daybookEntries as $entry)
                <tr>
                    <td><code class="text-gold-light">{{ $entry->account_code }}</code> {{ $entry->account_name }}</td>
                    <td class="small text-muted">{{ $entry->particular }}</td>
                    <td class="text-end text-danger">{{ $entry->amount < 0 ? '₹'.number_format(abs($entry->amount), 2) : '—' }}</td>
                    <td class="text-end text-success">{{ $entry->amount > 0 ? '₹'.number_format($entry->amount, 2) : '—' }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr class="table-gold">
                    <td colspan="2" class="fw-bold">Total</td>
                    <td class="text-end fw-bold text-danger">₹{{ number_format($daybookEntries->where('amount', '<', 0)->sum(fn($e) => abs($e->amount)), 2) }}</td>
                    <td class="text-end fw-bold text-success">₹{{ number_format($daybookEntries->where('amount', '>', 0)->sum('amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <p class="small text-muted mt-1">
        Net Sum: {{ number_format($daybookEntries->sum('amount'), 4) }}
        @if(abs($daybookEntries->sum('amount')) < 0.01) <span class="text-success"><i class="bi bi-check-circle me-1"></i>Balanced</span> @else <span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Imbalanced</span> @endif
    </p>
</div>
@endif
@endsection
