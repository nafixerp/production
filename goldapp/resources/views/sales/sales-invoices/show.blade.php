@extends('layouts.app')
@section('title','Invoice')
@section('content')
<div class="container-fluid px-4 py-3">
  <div class="d-flex gap-2 align-items-center mb-3">
    <a href="{{ route('sales-invoices.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h4 class="text-warning mb-0">Invoice: {{ $salesInvoice->invoice_no }}</h4>
    <span class="badge bg-info">{{ $salesInvoice->status }}</span>
    <a href="{{ route('sales-invoices.edit',$salesInvoice) }}" class="btn btn-sm btn-outline-warning ms-auto"><i class="bi bi-pencil me-1"></i>Edit</a>
    <form method="POST" action="{{ route('sales-invoices.destroy',$salesInvoice) }}" onsubmit="return confirm('Cancel?')">
      @csrf @method('DELETE')
      <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Cancel</button>
    </form>
  </div>
  @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card bg-dark border-secondary">
        <div class="card-body">
          <h6 class="text-warning">Invoice Details</h6>
          <table class="table table-sm table-dark mb-0">
            <tr><td class="text-muted">Date</td><td>{{ $salesInvoice->invoice_date->format('d/m/Y') }}</td></tr>
            <tr><td class="text-muted">Customer</td><td>{{ $salesInvoice->customer_name }}</td></tr>
            <tr><td class="text-muted">Channel</td><td>{{ $salesInvoice->channel }}</td></tr>
            <tr><td class="text-muted">Payment Mode</td><td>{{ $salesInvoice->payment_mode }}</td></tr>
            @if($salesInvoice->cheque_no)<tr><td class="text-muted">Cheque No</td><td>{{ $salesInvoice->cheque_no }}</td></tr>@endif
            @if($salesInvoice->utr_no)<tr><td class="text-muted">UTR No</td><td>{{ $salesInvoice->utr_no }}</td></tr>@endif
            @if($salesInvoice->irn_no)<tr><td class="text-muted">IRN No</td><td>{{ $salesInvoice->irn_no }}</td></tr>@endif
            @if($salesInvoice->eway_bill_no)<tr><td class="text-muted">E-Way Bill</td><td>{{ $salesInvoice->eway_bill_no }}</td></tr>@endif
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-dark border-secondary h-100">
        <div class="card-body">
          <h6 class="text-warning">Amounts</h6>
          <table class="table table-sm table-dark mb-0">
            <tr><td class="text-muted">Taxable</td><td class="text-end">₹{{ number_format($salesInvoice->taxable_amount,2) }}</td></tr>
            <tr><td class="text-muted">Discount</td><td class="text-end">-₹{{ number_format($salesInvoice->discount,2) }}</td></tr>
            <tr><td class="text-muted">SGST</td><td class="text-end">₹{{ number_format($salesInvoice->sgst,2) }}</td></tr>
            <tr><td class="text-muted">CGST</td><td class="text-end">₹{{ number_format($salesInvoice->cgst,2) }}</td></tr>
            <tr><td class="text-muted">IGST</td><td class="text-end">₹{{ number_format($salesInvoice->igst,2) }}</td></tr>
            <tr><td class="text-muted">TCS</td><td class="text-end">₹{{ number_format($salesInvoice->tcs,2) }}</td></tr>
            <tr><td class="text-muted">Other Charges</td><td class="text-end">₹{{ number_format($salesInvoice->other_charges,2) }}</td></tr>
            <tr><td class="text-muted">Round Off</td><td class="text-end">₹{{ number_format($salesInvoice->round_off,2) }}</td></tr>
            <tr class="text-warning fw-bold"><td>Net Amount</td><td class="text-end">₹{{ number_format($salesInvoice->net_amount,2) }}</td></tr>
            <tr class="text-success"><td>Received</td><td class="text-end">₹{{ number_format($salesInvoice->received_amount,2) }}</td></tr>
            <tr class="{{ $salesInvoice->balance_amount>0?'text-danger':'text-success' }}"><td><strong>Balance</strong></td><td class="text-end"><strong>₹{{ number_format($salesInvoice->balance_amount,2) }}</strong></td></tr>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card bg-dark border-secondary h-100">
        <div class="card-body">
          <h6 class="text-warning">Addresses</h6>
          <p class="small text-muted mb-1">Billing:</p>
          <p class="small">{{ $salesInvoice->billing_address }}</p>
          <p class="small text-muted mb-1">Shipping:</p>
          <p class="small">{{ $salesInvoice->shipping_address }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="card bg-dark border-secondary mt-3">
    <div class="card-body">
      <h6 class="text-warning">Invoice Items</h6>
      <div class="table-responsive">
        <table class="table table-dark table-sm mb-0">
          <thead class="text-warning"><tr><th>FG</th><th>Batch</th><th>HSN</th><th>Unit</th><th class="text-end">Qty</th><th class="text-end">Rate</th><th class="text-end">Amount</th><th class="text-end">Disc</th><th class="text-end">SGST</th><th class="text-end">CGST</th><th class="text-end">IGST</th><th class="text-end">Net Amt</th><th class="text-end">Cost Rate</th></tr></thead>
          <tbody>
            @foreach($salesInvoice->items as $item)
            <tr>
              <td>{{ $item->fg_name }}</td><td><code>{{ $item->batch_no }}</code></td>
              <td>{{ $item->hsn_code }}</td><td>{{ $item->unit }}</td>
              <td class="text-end">{{ number_format($item->qty,4) }}</td>
              <td class="text-end">{{ number_format($item->rate,2) }}</td>
              <td class="text-end">{{ number_format($item->amount,2) }}</td>
              <td class="text-end">{{ number_format($item->discount_amount,2) }}</td>
              <td class="text-end">{{ number_format($item->sgst,2) }}</td>
              <td class="text-end">{{ number_format($item->cgst,2) }}</td>
              <td class="text-end">{{ number_format($item->igst,2) }}</td>
              <td class="text-end fw-bold">₹{{ number_format($item->net_amount,2) }}</td>
              <td class="text-end">{{ number_format($item->cost_rate,4) }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if($daybookEntries->count())
  <div class="card bg-dark border-secondary mt-3">
    <div class="card-body">
      <h6 class="text-warning">Double-Entry Daybook ({{ $salesInvoice->slno }})</h6>
      @php $sum = $daybookEntries->sum('amount') @endphp
      <div class="alert alert-{{ abs($sum)<0.01?'success':'danger' }} py-1 small">
        SUM of all entries: {{ number_format($sum,4) }} {{ abs($sum)<0.01 ? '✓ Balanced':'✗ UNBALANCED' }}
      </div>
      <div class="table-responsive">
        <table class="table table-dark table-sm mb-0">
          <thead class="text-warning"><tr><th>Account</th><th>Particular</th><th>Type</th><th class="text-end">Amount</th><th>Dr/Cr</th></tr></thead>
          <tbody>
            @foreach($daybookEntries as $e)
            <tr>
              <td>{{ $e->account_name }} <span class="badge bg-secondary">{{ $e->vtype }}</span></td>
              <td>{{ $e->particular }}</td>
              <td><span class="badge bg-{{ $e->vtype=='COGS'?'warning':'info' }}">{{ $e->vtype }}</span></td>
              <td class="text-end {{ $e->amount<0?'text-danger':'text-success' }}">₹{{ number_format(abs($e->amount),2) }}</td>
              <td><span class="badge bg-{{ $e->amount<0?'danger':'success' }}">{{ $e->amount<0?'Debit':'Credit' }}</span></td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="text-warning"><td colspan="3" class="text-end">Net (should be 0):</td><td class="text-end {{ abs($sum)<0.01?'text-success':'text-danger' }}">{{ number_format($sum,4) }}</td><td></td></tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
  @endif

  @if($arEntries->count())
  <div class="card bg-dark border-secondary mt-3">
    <div class="card-body">
      <h6 class="text-warning">AR Ledger Entries</h6>
      <div class="table-responsive">
        <table class="table table-dark table-sm mb-0">
          <thead class="text-warning"><tr><th>Date</th><th>Type</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th><th>Narration</th></tr></thead>
          <tbody>
            @foreach($arEntries as $ar)
            <tr>
              <td>{{ $ar->tdate->format('d/m/Y') }}</td>
              <td>{{ $ar->vtype }}</td>
              <td class="text-end">₹{{ number_format($ar->debit,2) }}</td>
              <td class="text-end">₹{{ number_format($ar->credit,2) }}</td>
              <td class="text-end fw-bold {{ $ar->balance>0?'text-danger':'text-success' }}">₹{{ number_format($ar->balance,2) }}</td>
              <td>{{ $ar->narration }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection
