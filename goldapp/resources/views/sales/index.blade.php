@extends('layouts.app')
@section('title','Sales Bills')
@section('page-title','Sales Bills')

@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◆ Sales Bills</h5>
        <a href="{{ route('sales.create') }}" class="btn-gold">+ New Bill</a>
    </div>

    <form method="GET" class="mb-3 d-flex flex-wrap gap-2 align-items-end">
        <div>
            <label class="form-label">Search</label>
            <input type="text" name="q" value="{{ $q }}" class="form-control" style="width:200px" placeholder="Bill no / customer...">
        </div>
        <div>
            <label class="form-label">From</label>
            <input type="date" name="from" value="{{ $from }}" class="form-control" style="width:155px">
        </div>
        <div>
            <label class="form-label">To</label>
            <input type="date" name="to" value="{{ $to }}" class="form-control" style="width:155px">
        </div>
        <button class="btn-outline-gold" style="margin-bottom:1px">Filter</button>
        <a href="{{ route('sales.index') }}" class="btn-outline-gold" style="margin-bottom:1px">Clear</a>
    </form>

    <div class="table-responsive">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Bill No</th>
                <th>Date</th>
                <th>Customer</th>
                <th class="text-end">Gross</th>
                <th class="text-end">Discount</th>
                <th class="text-end">GST</th>
                <th class="text-end">Net Amount</th>
                <th class="text-end">Received</th>
                <th>Mode</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bills as $b)
            <tr>
                <td>{{ $bills->firstItem() + $loop->index }}</td>
                <td><a href="{{ route('sales.show',$b->id) }}" class="text-gold">{{ $b->billno }}</a></td>
                <td>{{ $b->billdate }}</td>
                <td>{{ Str::limit($b->customer_name,22) }}</td>
                <td class="text-end">{{ number_format($b->gross_amount,2) }}</td>
                <td class="text-end text-debit">{{ $b->discount > 0 ? number_format($b->discount,2) : '-' }}</td>
                <td class="text-end">{{ number_format($b->sgst+$b->cgst+$b->igst,2) }}</td>
                <td class="text-end text-credit fw-bold">₹{{ number_format($b->net_amount,2) }}</td>
                <td class="text-end">{{ $b->received_amount > 0 ? number_format($b->received_amount,2) : '-' }}</td>
                <td><span class="badge-gold">{{ strtoupper($b->payment_mode) }}</span></td>
                <td>
                    <a href="{{ route('sales.show',$b->id) }}" class="btn-outline-gold btn-sm-gold">View</a>
                    <form method="POST" action="{{ route('sales.destroy',$b->id) }}" style="display:inline" onsubmit="return confirm('Delete this bill?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:rgba(248,113,113,0.4)">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center" style="padding:24px;color:var(--text-muted-gold)">No sales bills found.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="mt-3">{{ $bills->links() }}</div>
</div>
@endsection
