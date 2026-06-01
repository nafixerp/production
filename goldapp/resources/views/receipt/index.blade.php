@extends('layouts.app')
@section('title','Receipts')
@section('page-title','Receipt Vouchers')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ Receipt Vouchers</h5>
        <a href="{{ route('receipts.create') }}" class="btn-gold">+ New Receipt</a>
    </div>

    <form method="GET" class="mb-3 d-flex flex-wrap gap-2 align-items-end">
        <div><label class="form-label">Search</label><input type="text" name="q" value="{{ $q }}" class="form-control" style="width:200px" placeholder="Voucher / party..."></div>
        <div><label class="form-label">From</label><input type="date" name="from" value="{{ $from }}" class="form-control" style="width:155px"></div>
        <div><label class="form-label">To</label><input type="date" name="to" value="{{ $to }}" class="form-control" style="width:155px"></div>
        <button class="btn-outline-gold">Filter</button>
        <a href="{{ route('receipts.index') }}" class="btn-outline-gold">Clear</a>
    </form>

    <div class="table-responsive">
    <table class="table-gold w-100">
        <thead><tr><th>#</th><th>Voucher No</th><th>Date</th><th>Party</th><th>Mode</th><th class="text-end">Amount</th><th class="text-end">Discount</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($receipts as $r)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $r->vch_no }}</td>
            <td>{{ $r->vch_date }}</td>
            <td>{{ $r->party_name }}</td>
            <td>{{ strtoupper($r->payment_mode) }}</td>
            <td class="text-end text-gold">{{ number_format($r->amount, 2) }}</td>
            <td class="text-end">{{ number_format($r->discount, 2) }}</td>
            <td>
                <a href="{{ route('receipts.show', $r) }}" class="btn-outline-gold btn-sm-gold">View</a>
                <a href="{{ route('receipts.edit', $r) }}" class="btn-outline-gold btn-sm-gold">Edit</a>
                <form method="POST" action="{{ route('receipts.destroy', $r) }}" style="display:inline" onsubmit="return confirm('Delete receipt?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center" style="color:#666;padding:20px">No receipts found.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-3">{{ $receipts->links() }}</div>
</div>
@endsection
