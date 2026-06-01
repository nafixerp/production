@extends('layouts.app')
@section('title', $shipment->exists ? 'Update Shipment' : 'Book Shipment')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-box-seam me-2"></i>{{ $shipment->exists ? 'Update Shipment — '.$shipment->slno : 'Book New Shipment' }}</h5>
    <a href="{{ route('couriers.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card-gold" style="max-width:680px">
    <form method="POST" action="{{ $shipment->exists ? route('couriers.update', $shipment) : route('couriers.store') }}">
        @csrf
        @if($shipment->exists) @method('PUT') @endif

        @if(!$shipment->exists)
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">Courier Partner *</label>
                <select name="courier_partner" class="form-select" required>
                    @foreach(['delhivery','dtdc','bluedart','fedex','ecom_express','custom'] as $cp)
                        <option value="{{ $cp }}">{{ ucwords(str_replace('_',' ',$cp)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label">Dispatch ID</label>
                <input type="number" name="dispatch_id" class="form-control" value="{{ old('dispatch_id') }}" placeholder="Optional">
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-4">
                <label class="form-label">Weight (kg) *</label>
                <input type="number" step="0.001" name="weight" class="form-control" value="{{ old('weight') }}" required>
            </div>
            <div class="col-4">
                <label class="form-label">Dimensions</label>
                <input type="text" name="dimensions" class="form-control" value="{{ old('dimensions') }}" placeholder="LxWxH cm">
            </div>
            <div class="col-4">
                <label class="form-label">Charges (₹)</label>
                <input type="number" step="0.01" name="charges" class="form-control" value="{{ old('charges',0) }}">
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">Pickup Date</label>
                <input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date') }}">
            </div>
            <div class="col-6">
                <label class="form-label">Estimated Delivery</label>
                <input type="date" name="estimated_delivery" class="form-control" value="{{ old('estimated_delivery') }}">
            </div>
        </div>
        @else
        <div class="mb-3">
            <label class="form-label">Status *</label>
            <select name="status" class="form-select" required>
                @foreach(['booked','picked','in_transit','out_for_delivery','delivered','returned','failed'] as $s)
                    <option value="{{ $s }}" {{ $shipment->status === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Actual Delivery Date</label>
            <input type="date" name="actual_delivery" class="form-control" value="{{ old('actual_delivery', $shipment->actual_delivery?->format('Y-m-d')) }}">
        </div>

        @if($shipment->tracking_events)
        <div class="mb-3">
            <label class="form-label">Tracking History</label>
            <div style="max-height:200px;overflow-y:auto">
                @foreach(array_reverse($shipment->tracking_events) as $event)
                <div style="border-left:2px solid var(--border-gold);padding:6px 12px;margin-bottom:6px;font-size:.78rem">
                    <span style="color:var(--gold)">{{ ucwords(str_replace('_',' ',$event['status'])) }}</span>
                    <span style="color:#888;margin-left:8px">{{ $event['timestamp'] ?? '' }}</span>
                    @if(!empty($event['location']))<div style="color:#ccc">{{ $event['location'] }}</div>@endif
                    @if(!empty($event['remarks']))<div style="color:#aaa">{{ $event['remarks'] }}</div>@endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        <button type="submit" class="btn-gold"><i class="bi bi-check2 me-1"></i>{{ $shipment->exists ? 'Update Shipment' : 'Book Shipment' }}</button>
    </form>
</div>
@endsection
