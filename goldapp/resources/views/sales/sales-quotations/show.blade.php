@extends('layouts.app')
@section('title','Quotation')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('sales-quotations.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">Quotation: {{ $salesQuotation->quot_no }}</h4>
    <span class="badge bg-info">{{ $salesQuotation->channel }}</span>
    @if(!in_array($salesQuotation->status,['accepted','rejected']))
    <form method="POST" action="{{ route('sales-quotations.convert-so',$salesQuotation) }}" class="ms-auto">
      @csrf <button class="btn btn-sm btn-success"><i class="bi bi-arrow-right-circle me-1"></i>Convert to SO</button>
    </form>
    @endif
  </div>
  <div class="row g-3">
    <div class="col-md-4">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <table class="table table-sm table-dark mb-0">
            <tr><td class="text-muted">Date</td><td>{{ $salesQuotation->quot_date->format('d/m/Y') }}</td></tr>
            <tr><td class="text-muted">Valid Till</td><td>{{ $salesQuotation->valid_till?->format('d/m/Y') ?? '—' }}</td></tr>
            <tr><td class="text-muted">Customer</td><td>{{ $salesQuotation->customer_name }}</td></tr>
            <tr><td class="text-muted">Address</td><td>{{ $salesQuotation->customer_address }}</td></tr>
            <tr><td class="text-muted">Terms</td><td>{{ $salesQuotation->terms }}</td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-dark table-sm mb-0">
              <thead class="text-warning"><tr><th>FG</th><th>HSN</th><th>Unit</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th><th class="text-end">Disc</th><th class="text-end">SGST</th><th class="text-end">CGST</th><th class="text-end">Net</th></tr></thead>
              <tbody>
                @foreach($salesQuotation->items as $item)
                <tr>
                  <td>{{ $item->fg_name }}</td><td>{{ $item->hsn_code }}</td><td>{{ $item->unit }}</td>
                  <td class="text-end">{{ $item->qty }}</td>
                  <td class="text-end">{{ number_format($item->rate,2) }}</td>
                  <td class="text-end">{{ number_format($item->amount,2) }}</td>
                  <td class="text-end">{{ number_format($item->discount_amount,2) }}</td>
                  <td class="text-end">{{ number_format($item->sgst,2) }}</td>
                  <td class="text-end">{{ number_format($item->cgst,2) }}</td>
                  <td class="text-end fw-bold">{{ number_format($item->net_amount,2) }}</td>
                </tr>
                @endforeach
              </tbody>
              <tfoot class="text-warning">
                <tr><td colspan="5" class="text-end">Totals</td>
                  <td class="text-end">{{ number_format($salesQuotation->taxable_amount,2) }}</td>
                  <td class="text-end">{{ number_format($salesQuotation->discount,2) }}</td>
                  <td class="text-end">{{ number_format($salesQuotation->sgst,2) }}</td>
                  <td class="text-end">{{ number_format($salesQuotation->cgst,2) }}</td>
                  <td class="text-end">₹{{ number_format($salesQuotation->net_amount,2) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
