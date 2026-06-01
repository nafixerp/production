@extends('layouts.app')
@section('title','Payments')
@section('page-title','Payment Vouchers')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ Payment Vouchers</h5>
        <a href="{{ route('payments.create') }}" class="btn-gold">+ New Payment</a>
    </div>

    <form method="GET" class="mb-3 d-flex flex-wrap gap-2 align-items-end">
        <div><label class="form-label">Search</label><input type="text" name="q" value="{{ $q }}" class="form-control" style="width:200px" placeholder="Voucher / party..."></div>
        <div><label class="form-label">From</label><input type="date" name="from" value="{{ $from }}" class="form-control" style="width:155px"></div>
        <div><label class="form-label">To</label><input type="date" name="to" value="{{ $to }}" class="form-control" style="width:155px"></div>
        <button class="btn-outline-gold">Filter</button>
        <a href="{{ route('payments.index') }}" class="btn-outline-gold">Clear</a>
    </form>

    <div class="table-responsive">
    <table class="table-gold w-100">
        <thead><tr><th>#</th><th>Voucher No</th><th>Date</th><th>Party</th><th>Mode</th><th class="text-end">Amount</th><th>Actions</th></tr></thead>
        <tbody>
        @forelse($payments as $p)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $p->vch_no }}</td>
            <td>{{ $p->vch_date }}</td>
            <td>{{ $p->party_name }}</td>
            <td>{{ strtoupper($p->payment_mode) }}</td>
            <td class="text-end text-gold">{{ number_format($p->amount, 2) }}</td>
            <td>
                <a href="{{ route('payments.show', $p) }}" class="btn-outline-gold btn-sm-gold">View</a>
                <a href="{{ route('payments.edit', $p) }}" class="btn-outline-gold btn-sm-gold">Edit</a>
                <form method="POST" action="{{ route('payments.destroy', $p) }}" style="display:inline" onsubmit="return confirm('Delete payment?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center" style="color:#666;padding:20px">No payments found.</td></tr>
        @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-3">{{ $payments->links() }}</div>
</div>
@endsection
