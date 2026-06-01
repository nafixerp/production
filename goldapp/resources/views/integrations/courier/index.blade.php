@extends('layouts.app')
@section('title','Courier Shipments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-box-seam me-2"></i>Courier Shipments</h5>
    <a href="{{ route('couriers.create') }}" class="btn-gold btn-sm-gold"><i class="bi bi-plus-circle me-1"></i>Book Shipment</a>
</div>

<div class="card-gold mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control" placeholder="Search SLNO / AWB..." value="{{ $q }}">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach(['booked','picked','in_transit','out_for_delivery','delivered','returned','failed'] as $s)
                    <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-gold w-100">Filter</button>
        </div>
    </form>
</div>

<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>SLNO</th><th>Partner</th><th>AWB</th><th>Weight</th><th>Charges</th>
                <th>Pickup</th><th>Est. Delivery</th><th>Actual Delivery</th><th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($shipments as $ship)
            <tr>
                <td style="font-size:.75rem">{{ $ship->slno }}</td>
                <td><span class="badge-gold">{{ strtoupper(str_replace('_',' ',$ship->courier_partner)) }}</span></td>
                <td style="font-size:.75rem;font-family:monospace">{{ $ship->awb_no ?? '—' }}</td>
                <td>{{ $ship->weight }} kg</td>
                <td>₹{{ number_format($ship->charges,2) }}</td>
                <td>{{ $ship->pickup_date?->format('d/m/Y') ?? '—' }}</td>
                <td>{{ $ship->estimated_delivery?->format('d/m/Y') ?? '—' }}</td>
                <td>{{ $ship->actual_delivery?->format('d/m/Y') ?? '—' }}</td>
                <td>
                    @php
                        $sc = match($ship->status) {
                            'delivered'=>'#4ade80','failed','returned'=>'#f87171',
                            'out_for_delivery'=>'#60a5fa', default=>'#fb923c'
                        };
                    @endphp
                    <span style="color:{{ $sc }};font-weight:600">{{ ucwords(str_replace('_',' ',$ship->status)) }}</span>
                </td>
                <td>
                    <a href="{{ route('couriers.edit', $ship) }}" class="btn-outline-gold btn-sm-gold me-1"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('couriers.destroy', $ship) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="10" class="text-center" style="color:#666;padding:30px">No shipments booked yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $shipments->links() }}</div>
</div>
@endsection
