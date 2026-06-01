@extends('layouts.app')
@section('title','Sales Invoice - '.$sale->invoice_no)
@section('page-title','Sales Invoice')

@section('content')
<div class="card-gold mb-3">
    <div class="card-header-gold">
        <h5>◆ Invoice: {{ $sale->invoice_no }}</h5>
        <div>
            <a href="{{ route('sales.edit', $sale) }}" class="btn-outline-gold btn-sm-gold">Edit</a>
            <a href="{{ route('sales.index') }}" class="btn-outline-gold btn-sm-gold ms-2">← Back</a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><label class="form-label">Invoice No</label><div class="text-gold">{{ $sale->invoice_no }}</div></div>
        <div class="col-md-3"><label class="form-label">Date</label><div>{{ $sale->invoice_date }}</div></div>
        <div class="col-md-3"><label class="form-label">Customer</label><div>{{ $sale->customer_name }}</div></div>
        <div class="col-md-3"><label class="form-label">Payment Mode</label><div>{{ strtoupper($sale->payment_mode) }}</div></div>
    </div>

    <div class="table-responsive mb-3">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th><th>Item</th><th>HSN</th><th>Unit</th>
                <th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th>
                <th class="text-end">SGST</th><th class="text-end">CGST</th><th class="text-end">IGST</th>
            </tr>
        </thead>
        <tbody>
        @foreach($sale->details as $d)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $d->item_name }}</td>
            <td>{{ $d->hsn_code }}</td>
            <td>{{ $d->unit }}</td>
            <td class="text-end">{{ number_format($d->qty, 4) }}</td>
            <td class="text-end">{{ number_format($d->rate, 4) }}</td>
            <td class="text-end">{{ number_format($d->amount, 2) }}</td>
            <td class="text-end">{{ number_format($d->sgst, 2) }}</td>
            <td class="text-end">{{ number_format($d->cgst, 2) }}</td>
            <td class="text-end">{{ number_format($d->igst, 2) }}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top:1px solid var(--border-gold)">
                <td colspan="6" class="text-end text-gold"><strong>Totals</strong></td>
                <td class="text-end text-gold"><strong>{{ number_format($sale->taxable_amount, 2) }}</strong></td>
                <td class="text-end text-gold"><strong>{{ number_format($sale->sgst, 2) }}</strong></td>
                <td class="text-end text-gold"><strong>{{ number_format($sale->cgst, 2) }}</strong></td>
                <td class="text-end text-gold"><strong>{{ number_format($sale->igst, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
    </div>

    <div class="row justify-content-end mb-3">
        <div class="col-md-4">
            <table class="w-100" style="font-size:0.85rem">
                <tr><td class="form-label">Taxable Amount</td><td class="text-end">{{ number_format($sale->taxable_amount, 2) }}</td></tr>
                <tr><td class="form-label">Discount</td><td class="text-end text-debit">-{{ number_format($sale->discount, 2) }}</td></tr>
                <tr><td class="form-label">SGST</td><td class="text-end">{{ number_format($sale->sgst, 2) }}</td></tr>
                <tr><td class="form-label">CGST</td><td class="text-end">{{ number_format($sale->cgst, 2) }}</td></tr>
                <tr><td class="form-label">IGST</td><td class="text-end">{{ number_format($sale->igst, 2) }}</td></tr>
                <tr><td class="form-label">Other Charges</td><td class="text-end">{{ number_format($sale->other_charges, 2) }}</td></tr>
                <tr style="border-top:1px solid var(--border-gold)"><td class="form-label" style="font-size:0.9rem">NET AMOUNT</td><td class="text-end text-gold" style="font-size:1rem"><strong>{{ number_format($sale->net_amount, 2) }}</strong></td></tr>
                <tr><td class="form-label text-credit">Received</td><td class="text-end text-credit">{{ number_format($sale->received_amount, 2) }}</td></tr>
            </table>
        </div>
    </div>
</div>

<div class="card-gold">
    <div class="card-header-gold"><h5>◆ Daybook Entries</h5></div>
    <div class="table-responsive">
    <table class="table-gold w-100">
        <thead>
            <tr><th>Account</th><th>Particular</th><th class="text-end text-debit">Debit</th><th class="text-end text-credit">Credit</th></tr>
        </thead>
        <tbody>
        @foreach($daybookEntries as $e)
        <tr>
            <td>{{ $e->account_code }} - {{ $e->account_name }}</td>
            <td>{{ $e->particular }}</td>
            <td class="text-end text-debit">{{ $e->amount < 0 ? number_format(abs($e->amount), 2) : '' }}</td>
            <td class="text-end text-credit">{{ $e->amount > 0 ? number_format($e->amount, 2) : '' }}</td>
        </tr>
        @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top:1px solid var(--border-gold)">
                <td colspan="2" class="text-gold form-label">Totals</td>
                <td class="text-end text-debit"><strong>{{ number_format($daybookEntries->where('amount', '<', 0)->sum(fn($e) => abs($e->amount)), 2) }}</strong></td>
                <td class="text-end text-credit"><strong>{{ number_format($daybookEntries->where('amount', '>', 0)->sum('amount'), 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>
@endsection
