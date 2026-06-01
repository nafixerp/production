@extends('layouts.app')
@section('title','E-Way Bills')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-truck me-2"></i>E-Way Bills</h5>
    <a href="{{ route('eway-bills.create') }}" class="btn-gold btn-sm-gold"><i class="bi bi-plus-circle me-1"></i>Generate EWB</a>
</div>
<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th><th>EWB No</th><th>Invoice ID</th><th>EWB Date</th>
                <th>Valid Till</th><th>From GSTIN</th><th>To GSTIN</th>
                <th>Vehicle</th><th>Dist (km)</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bills as $bill)
            <tr>
                <td>{{ $bill->id }}</td>
                <td><code style="color:var(--gold);font-size:.75rem">{{ $bill->ewb_no }}</code></td>
                <td>{{ $bill->sales_invoice_id }}</td>
                <td>{{ $bill->ewb_date?->format('d/m/Y') }}</td>
                <td>{{ $bill->valid_till?->format('d/m/Y H:i') }}</td>
                <td style="font-size:.75rem">{{ $bill->from_gstin }}</td>
                <td style="font-size:.75rem">{{ $bill->to_gstin }}</td>
                <td>{{ $bill->vehicle_no ?? '—' }}</td>
                <td>{{ $bill->distance_km ?? '—' }}</td>
                <td>
                    @php $sc = match($bill->status) { 'active'=>'#4ade80','cancelled'=>'#f87171', default=>'#fb923c' }; @endphp
                    <span style="color:{{ $sc }};font-weight:600">{{ ucfirst($bill->status) }}</span>
                </td>
                <td>
                    <a href="{{ route('eway-bills.edit', $bill) }}" class="btn-outline-gold btn-sm-gold me-1"><i class="bi bi-pencil"></i></a>
                    @if($bill->status === 'active')
                    <form method="POST" action="{{ route('eway-bills.destroy', $bill) }}" class="d-inline" onsubmit="return confirm('Cancel EWB?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171"><i class="bi bi-x-circle"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="11" class="text-center" style="color:#666;padding:30px">No e-way bills yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $bills->links() }}</div>
</div>
@endsection
