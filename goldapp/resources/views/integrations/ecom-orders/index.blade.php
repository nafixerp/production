@extends('layouts.app')
@section('title','E-Commerce Channel Orders')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-cart3 me-2"></i>E-Commerce Channel Orders</h5>
</div>

<div class="card-gold mb-3">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
            <input type="text" name="q" class="form-control" placeholder="Order ID / Customer..." value="{{ $q }}">
        </div>
        <div class="col-md-2">
            <select name="channel" class="form-select">
                <option value="">All Channels</option>
                @foreach(['website','amazon','flipkart','swiggy','zomato','shopify','woocommerce'] as $ch)
                    <option value="{{ $ch }}" {{ $channel === $ch ? 'selected' : '' }}>{{ ucfirst($ch) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">All Status</option>
                @foreach(['new','processing','shipped','delivered','cancelled','returned'] as $s)
                    <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn-gold">Filter</button>
        </div>
    </form>
</div>

<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th><th>Channel</th><th>Channel Order ID</th><th>Date</th>
                <th>Customer</th><th>Amount</th><th>Status</th>
                <th>Mapped SO</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>
                    @php
                        $chColor = match($order->channel) {
                            'amazon'=>'#ff9900','flipkart'=>'#2874f0','swiggy'=>'#fc8019',
                            'zomato'=>'#e23744','shopify'=>'#96bf48', default=>'#d4af37'
                        };
                    @endphp
                    <span style="color:{{ $chColor }};font-weight:600;font-size:.78rem">{{ strtoupper($order->channel) }}</span>
                </td>
                <td style="font-size:.75rem;font-family:monospace">{{ $order->channel_order_id }}</td>
                <td>{{ $order->order_date?->format('d/m/Y H:i') }}</td>
                <td>
                    <div>{{ $order->customer_name }}</div>
                    <small style="color:#888">{{ $order->customer_phone }}</small>
                </td>
                <td class="text-gold">₹{{ number_format($order->total_amount,2) }}</td>
                <td>
                    @php $sc = match($order->status) { 'delivered'=>'#4ade80','cancelled','returned'=>'#f87171','new'=>'#fb923c', default=>'#60a5fa' }; @endphp
                    <span style="color:{{ $sc }};font-weight:600">{{ ucfirst($order->status) }}</span>
                </td>
                <td>{{ $order->mapped_so_id ? '#'.$order->mapped_so_id : '<span style="color:#666">—</span>' }}</td>
                <td>
                    @if(!$order->mapped_so_id)
                    <button class="btn-outline-gold btn-sm-gold me-1" onclick="mapOrder({{ $order->id }})">
                        <i class="bi bi-link-45deg"></i> Map SO
                    </button>
                    @endif
                    @if($order->status === 'new')
                    <form method="POST" action="/ecom-orders/{{ $order->id }}/process" class="d-inline">
                        @csrf
                        <button class="btn-outline-gold btn-sm-gold" style="color:#60a5fa;border-color:#60a5fa"><i class="bi bi-play"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center" style="color:#666;padding:30px">No channel orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $orders->links() }}</div>
</div>

{{-- Map SO Modal --}}
<div class="modal fade" id="mapModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="background:#13132a;border:1px solid var(--border-gold)">
            <div class="modal-header" style="border-color:var(--border-gold)">
                <h5 class="modal-title text-gold">Map to Sales Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="mapForm" method="POST">
                @csrf
                <div class="modal-body">
                    <label class="form-label">Sales Order ID *</label>
                    <input type="number" name="so_id" class="form-control" required placeholder="Enter SO ID">
                </div>
                <div class="modal-footer" style="border-color:var(--border-gold)">
                    <button type="submit" class="btn-gold">Map</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function mapOrder(id) {
    document.getElementById('mapForm').action = '/ecom-orders/' + id + '/map-so';
    new bootstrap.Modal(document.getElementById('mapModal')).show();
}
</script>
@endpush
