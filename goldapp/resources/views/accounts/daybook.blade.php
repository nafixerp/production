@extends('layouts.app')
@section('title','Daybook Register')
@section('page-title','Daybook Register')

@section('content')
<div class="card-gold">
    <div class="card-header-gold"><h5>◆ Daybook Register</h5></div>

    <form method="GET" class="mb-3 d-flex flex-wrap gap-2 align-items-end">
        <div><label class="form-label">From</label><input type="date" name="from" value="{{ $from }}" class="form-control" style="width:155px"></div>
        <div><label class="form-label">To</label><input type="date" name="to" value="{{ $to }}" class="form-control" style="width:155px"></div>
        <div>
            <label class="form-label">Type</label>
            <select name="vtype" class="form-select" style="width:120px">
                <option value="">All</option>
                @foreach(['SI','PI','RV','PV','JV','CO'] as $vt)
                <option value="{{ $vt }}" {{ $vtype == $vt ? 'selected' : '' }}>{{ $vt }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-outline-gold">Filter</button>
    </form>

    <div class="table-responsive">
    <table class="table-gold w-100">
        <thead>
            <tr><th>Date</th><th>Slno</th><th>Type</th><th>Particular</th><th class="text-end text-debit">Debit</th><th class="text-end text-credit">Credit</th></tr>
        </thead>
        <tbody>
        @forelse($parts as $part)
        <tr>
            <td>{{ $part->tdate }}</td>
            <td><a href="#" style="color:var(--gold)">{{ $part->slno }}</a></td>
            <td><span class="badge-gold">{{ $part->vtype }}</span></td>
            <td>{{ $part->particular }}</td>
            <td class="text-end text-debit">
                @if(isset($sums[$part->slno]))
                    {{ number_format($sums[$part->slno]->total_debit, 2) }}
                @endif
            </td>
            <td class="text-end text-credit">
                @if(isset($sums[$part->slno]))
                    {{ number_format($sums[$part->slno]->total_credit, 2) }}
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center" style="color:#666;padding:20px">No entries for selected period.</td></tr>
        @endforelse
        </tbody>
        @if($parts->count())
        <tfoot>
            <tr style="border-top:1px solid var(--border-gold)">
                <td colspan="4" class="text-gold form-label">TOTALS</td>
                <td class="text-end text-debit"><strong>{{ number_format($sums->sum('total_debit'), 2) }}</strong></td>
                <td class="text-end text-credit"><strong>{{ number_format($sums->sum('total_credit'), 2) }}</strong></td>
            </tr>
        </tfoot>
        @endif
    </table>
    </div>
</div>
@endsection
