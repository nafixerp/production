@extends('layouts.app')
@section('title','Purchase Invoices')
@section('page-title','Purchase Invoices')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ Purchase Invoices</h5>
        <a href="{{ route('purchase.create') }}" class="btn-gold">+ New Invoice</a>
    </div>

    <form method="GET" class="mb-3 d-flex flex-wrap gap-2 align-items-end">
        <div><label class="form-label">Search</label><input type="text" name="q" value="{{ $q }}" class="form-control" style="width:200px" placeholder="Doc no / supplier..."></div>
        <div><label class="form-label">From</label><input type="date" name="from" value="{{ $from }}" class="form-control" style="width:155px"></div>
        <div><label class="form-label">To</label><input type="date" name="to" value="{{ $to }}" class="form-control" style="width:155px"></div>
        <button class="btn-outline-gold">Filter</button>
        <a href="{{ route('purchase.index') }}" class="btn-outline-gold">Clear</a>
    </form>

    <div class="table-responsive">
    <table class="table-gold w-100">
        <thead>
            <tr><th>#</th><th>Doc No</th><th>Date</th><th>Supplier</th><th>Supplier Bill</th><th class="text-end">Taxable</th><th class="text-end">GST</th><th class="text-end">Net Amount</th><th class="text-end">Paid</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
        @forelse($invoices as $inv)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $inv->doc_no }}</td>
            <td>{{ $inv->invoice_date }}</td>
            <td>{{ $inv->supplier_name }}</td>
            <td>{{ $inv->supplier_bill_no }}</td>
            <td class="text-end">{{ number_format($inv->taxable_amount, 2) }}</td>
            <td class="text-end">{{ number_format($inv->sgst + $inv->cgst + $inv->igst, 2) }}</td>
            <td class="text-end text-gold">{{ number_format($inv->net_amount, 2) }}</td>
            <td class="text-end text-credit">{{ number_format($inv->paid_amount, 2) }}</td>
            <td>@if($inv->status)<span class="badge-gold">Active</span>@else<span style="color:#f87171;font-size:0.75rem">Cancelled</span>@endif</td>
            <td>
                <a href="{{ route('purchase.show', $inv) }}" class="btn-outline-gold btn-sm-gold">View</a>
                <a href="{{ route('purchase.edit', $inv) }}" class="btn-outline-gold btn-sm-gold">Edit</a>
                @if($inv->status)
                <form method="POST" action="{{ route('purchase.destroy', $inv) }}" style="display:inline" onsubmit="return confirm('Cancel this invoice?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171">Cancel</button>
                </form>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="11" class="text-center" style="color:#666;padding:20px">No purchase invoices found.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-3">{{ $invoices->links() }}</div>
</div>
@endsection
