@extends('layouts.app')
@section('title','GST Filings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-receipt-cutoff me-2"></i>GST Filings</h5>
    <a href="{{ route('gst-filings.create') }}" class="btn-gold btn-sm-gold"><i class="bi bi-plus-circle me-1"></i>New Filing</a>
</div>

<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Period</th>
                <th>Type</th>
                <th>From</th>
                <th>To</th>
                <th>Taxable</th>
                <th>CGST</th>
                <th>SGST</th>
                <th>IGST</th>
                <th>Status</th>
                <th>Filed At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($filings as $filing)
            <tr>
                <td>{{ $filing->id }}</td>
                <td>{{ $filing->period_name }}</td>
                <td><span class="badge-gold">{{ $filing->filing_type }}</span></td>
                <td>{{ $filing->from_date?->format('d/m/Y') }}</td>
                <td>{{ $filing->to_date?->format('d/m/Y') }}</td>
                <td class="text-gold">₹{{ number_format($filing->total_taxable,2) }}</td>
                <td>₹{{ number_format($filing->total_cgst,2) }}</td>
                <td>₹{{ number_format($filing->total_sgst,2) }}</td>
                <td>₹{{ number_format($filing->total_igst,2) }}</td>
                <td>
                    @php
                        $sc = match($filing->status) { 'filed'=>'#4ade80','ready'=>'#60a5fa','error'=>'#f87171', default=>'#fb923c' };
                    @endphp
                    <span style="color:{{ $sc }};font-weight:600">{{ ucfirst($filing->status) }}</span>
                </td>
                <td>{{ $filing->filed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                <td>
                    <a href="{{ route('gst-filings.show', $filing) }}" class="btn-outline-gold btn-sm-gold me-1"><i class="bi bi-eye"></i></a>
                    @if($filing->json_payload)
                    <a href="{{ route('gst-filings.download', $filing) }}" class="btn-outline-gold btn-sm-gold me-1" title="Download JSON">
                        <i class="bi bi-download"></i>
                    </a>
                    @endif
                    <form method="POST" action="{{ route('gst-filings.destroy', $filing) }}" class="d-inline"
                          onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="12" class="text-center" style="color:#666;padding:30px">No filings yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $filings->links() }}</div>
</div>
@endsection
