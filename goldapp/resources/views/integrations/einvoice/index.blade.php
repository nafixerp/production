@extends('layouts.app')
@section('title','E-Invoices / IRN')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-file-earmark-code me-2"></i>E-Invoices / IRN</h5>
    <div class="d-flex gap-2">
        <button class="btn-outline-gold btn-sm-gold" data-bs-toggle="modal" data-bs-target="#genModal">
            <i class="bi bi-plus-circle me-1"></i>Generate IRN
        </button>
    </div>
</div>

<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Invoice ID</th>
                <th>IRN</th>
                <th>Ack No</th>
                <th>Ack Date</th>
                <th>Status</th>
                <th>Cancel Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($einvoices as $inv)
            <tr>
                <td>{{ $inv->id }}</td>
                <td>{{ $inv->sales_invoice_id }}</td>
                <td style="font-size:.68rem;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-family:monospace" title="{{ $inv->irn }}">
                    {{ substr($inv->irn, 0, 24) }}…
                </td>
                <td style="font-size:.75rem">{{ $inv->ack_no }}</td>
                <td>{{ $inv->ack_date?->format('d/m/Y H:i') }}</td>
                <td>
                    @if($inv->status === 'active')
                        <span style="color:#4ade80;font-weight:600">Active</span>
                    @else
                        <span style="color:#f87171;font-weight:600">Cancelled</span>
                    @endif
                </td>
                <td>{{ $inv->cancel_date?->format('d/m/Y') ?? '—' }}</td>
                <td>
                    @if($inv->status === 'active')
                    <form method="POST" action="{{ route('einvoices.destroy', $inv) }}" class="d-inline"
                          onsubmit="return confirm('Cancel this IRN?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171" title="Cancel IRN">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                    </form>
                    @else
                        <span style="color:#666;font-size:.75rem">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center" style="color:#666;padding:30px">No e-invoices generated yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $einvoices->links() }}</div>
</div>

{{-- Generate IRN Modal --}}
<div class="modal fade" id="genModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="background:#13132a;border:1px solid var(--border-gold)">
            <div class="modal-header" style="border-color:var(--border-gold)">
                <h5 class="modal-title text-gold">Generate IRN</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('einvoices.store') }}">
                @csrf
                <div class="modal-body">
                    <label class="form-label">Sales Invoice ID *</label>
                    <input type="number" name="invoice_id" class="form-control" required placeholder="Enter invoice ID">
                </div>
                <div class="modal-footer" style="border-color:var(--border-gold)">
                    <button type="submit" class="btn-gold">Generate IRN</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
