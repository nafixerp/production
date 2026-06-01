@extends('layouts.app')
@section('title', isset($filing) && $filing->exists ? 'GST Filing #'.$filing->id : 'Prepare GST Filing')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-receipt-cutoff me-2"></i>
        {{ isset($filing) && $filing->exists ? 'GST Filing #'.$filing->id : 'Prepare New GST Filing' }}
    </h5>
    <a href="{{ route('gst-filings.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card-gold">
            <div class="card-header-gold"><h5>Filing Details</h5></div>
            <form method="POST" action="{{ isset($filing) && $filing->exists ? route('gst-filings.update', $filing) : route('gst-filings.store') }}">
                @csrf
                @if(isset($filing) && $filing->exists) @method('PUT') @endif

                <div class="mb-3">
                    <label class="form-label">Period Name *</label>
                    <input type="text" name="period_name" class="form-control" value="{{ old('period_name', $filing->period_name ?? date('m-Y')) }}" placeholder="e.g. 04-2025">
                </div>
                <div class="mb-3">
                    <label class="form-label">Filing Type *</label>
                    <select name="filing_type" class="form-select" {{ isset($filing) && $filing->exists ? 'disabled' : '' }}>
                        @foreach(['GSTR1','GSTR3B','GSTR9'] as $t)
                            <option value="{{ $t }}" {{ old('filing_type', $filing->filing_type ?? 'GSTR1') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">From Date *</label>
                        <input type="date" name="from_date" class="form-control" value="{{ old('from_date', $filing->from_date?->format('Y-m-d') ?? '') }}">
                    </div>
                    <div class="col-6">
                        <label class="form-label">To Date *</label>
                        <input type="date" name="to_date" class="form-control" value="{{ old('to_date', $filing->to_date?->format('Y-m-d') ?? '') }}">
                    </div>
                </div>

                @if(isset($filing) && $filing->exists)
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['draft','ready','filed','error'] as $s)
                            <option value="{{ $s }}" {{ $filing->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <button type="submit" class="btn-gold w-100">
                    <i class="bi bi-floppy me-1"></i>{{ isset($filing) && $filing->exists ? 'Update Filing' : 'Create Filing' }}
                </button>
            </form>

            @if(isset($filing) && $filing->exists && $filing->json_payload)
            <hr style="border-color:var(--border-gold)">
            <form method="POST" action="{{ route('gst-filings.generate') }}">
                @csrf
                <input type="hidden" name="filing_id" value="{{ $filing->id }}">
                <button type="submit" class="btn-outline-gold w-100">
                    <i class="bi bi-braces me-1"></i>Re-generate JSON Payload
                </button>
            </form>
            <a href="{{ route('gst-filings.download', $filing) }}" class="btn-gold w-100 mt-2 text-center" style="display:block">
                <i class="bi bi-download me-1"></i>Download JSON
            </a>
            @endif
        </div>
    </div>

    @if(isset($filing) && $filing->exists)
    <div class="col-md-7">
        <div class="card-gold">
            <div class="card-header-gold"><h5>Tax Summary</h5></div>
            <div class="row g-3">
                @foreach([['Taxable Amount','total_taxable','#d4af37'],['CGST','total_cgst','#60a5fa'],['SGST','total_sgst','#a78bfa'],['IGST','total_igst','#fb923c']] as [$label,$field,$color])
                <div class="col-6">
                    <div class="stat-card">
                        <div class="stat-label">{{ $label }}</div>
                        <div class="stat-value" style="color:{{ $color }}">₹{{ number_format($filing->$field,2) }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($filing->json_payload)
            <div class="mt-3">
                <label class="form-label">JSON Payload Preview</label>
                <pre style="background:rgba(0,0,0,.3);border:1px solid var(--border-gold);border-radius:6px;padding:12px;color:#ccc;font-size:.72rem;max-height:200px;overflow-y:auto">{{ json_encode($filing->json_payload, JSON_PRETTY_PRINT) }}</pre>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
