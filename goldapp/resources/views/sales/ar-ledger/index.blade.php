@extends('layouts.app')
@section('title','AR Ledger / Ageing')
@section('content')
<div class="container-fluid px-4 py-3">
  <h4 class="text-warning mb-3"><i class="bi bi-person-lines-fill me-2"></i>Accounts Receivable – Ageing Report</h4>
  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-3">
      <select name="customer_id" class="form-select form-select-sm bg-dark text-light border-secondary">
        <option value="">All Customers</option>
        @foreach($customers as $c)
        <option value="{{ $c->id }}" {{ $customerId==$c->id?'selected':'' }}>{{ $c->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <input type="date" name="as_of" value="{{ $asOf }}" class="form-control form-control-sm bg-dark text-light border-secondary">
    </div>
    <div class="col-auto"><button class="btn btn-warning btn-sm">Generate</button></div>
  </form>

  <div class="card bg-dark border-secondary mb-3">
    <div class="card-header text-warning fw-bold">AR Ageing (As of {{ $asOf }})</div>
    <div class="table-responsive">
      <table class="table table-dark table-hover table-sm mb-0">
        <thead class="text-warning">
          <tr>
            <th>Customer</th>
            <th class="text-end">0-30 Days</th>
            <th class="text-end">31-60 Days</th>
            <th class="text-end">61-90 Days</th>
            <th class="text-end">90+ Days</th>
            <th class="text-end">Total Debit</th>
            <th class="text-end">Total Credit</th>
            <th class="text-end">Outstanding</th>
          </tr>
        </thead>
        <tbody>
          @php $totals = ['b0'=>0,'b1'=>0,'b2'=>0,'b3'=>0,'td'=>0,'tc'=>0,'out'=>0]; @endphp
          @forelse($ageing as $row)
          @php
            $cust = $customerMap[$row->customer_id] ?? null;
            $totals['b0'] += $row->bucket_0_30;
            $totals['b1'] += $row->bucket_31_60;
            $totals['b2'] += $row->bucket_61_90;
            $totals['b3'] += $row->bucket_90_plus;
            $totals['td'] += $row->total_debit;
            $totals['tc'] += $row->total_credit;
            $totals['out'] += $row->outstanding;
          @endphp
          <tr>
            <td>{{ $cust?->name ?? 'Unknown ('.$row->customer_id.')' }}</td>
            <td class="text-end">{{ number_format($row->bucket_0_30,2) }}</td>
            <td class="text-end {{ $row->bucket_31_60>0?'text-warning':'' }}">{{ number_format($row->bucket_31_60,2) }}</td>
            <td class="text-end {{ $row->bucket_61_90>0?'text-orange':'' }}">{{ number_format($row->bucket_61_90,2) }}</td>
            <td class="text-end {{ $row->bucket_90_plus>0?'text-danger fw-bold':'' }}">{{ number_format($row->bucket_90_plus,2) }}</td>
            <td class="text-end">{{ number_format($row->total_debit,2) }}</td>
            <td class="text-end">{{ number_format($row->total_credit,2) }}</td>
            <td class="text-end fw-bold {{ $row->outstanding>0?'text-danger':'text-success' }}">₹{{ number_format($row->outstanding,2) }}</td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-4">No AR data.</td></tr>
          @endforelse
        </tbody>
        <tfoot class="text-warning fw-bold">
          <tr>
            <td>TOTAL</td>
            <td class="text-end">{{ number_format($totals['b0'],2) }}</td>
            <td class="text-end">{{ number_format($totals['b1'],2) }}</td>
            <td class="text-end">{{ number_format($totals['b2'],2) }}</td>
            <td class="text-end">{{ number_format($totals['b3'],2) }}</td>
            <td class="text-end">{{ number_format($totals['td'],2) }}</td>
            <td class="text-end">{{ number_format($totals['tc'],2) }}</td>
            <td class="text-end text-danger">₹{{ number_format($totals['out'],2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  @if($customerId && count($statement))
  <div class="card bg-dark border-secondary">
    <div class="card-header text-warning">Customer Statement – {{ $customerMap[$customerId]?->name }}</div>
    <div class="table-responsive">
      <table class="table table-dark table-sm mb-0">
        <thead class="text-warning"><tr><th>Date</th><th>Type</th><th>Ref No</th><th>Narration</th><th class="text-end">Debit</th><th class="text-end">Credit</th><th class="text-end">Balance</th></tr></thead>
        <tbody>
          @foreach($statement as $s)
          <tr>
            <td>{{ $s->tdate->format('d/m/Y') }}</td>
            <td><span class="badge bg-secondary">{{ $s->vtype }}</span></td>
            <td>{{ $s->ref_no }}</td>
            <td>{{ $s->narration }}</td>
            <td class="text-end text-danger">{{ $s->debit > 0 ? number_format($s->debit,2) : '' }}</td>
            <td class="text-end text-success">{{ $s->credit > 0 ? number_format($s->credit,2) : '' }}</td>
            <td class="text-end fw-bold {{ $s->balance>0?'text-danger':'text-success' }}">₹{{ number_format($s->balance,2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
</div>
@endsection
