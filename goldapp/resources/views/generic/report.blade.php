@extends('layouts.app')
@section('title', $title)
@section('page-title', $title)
@section('content')
<div class="card-gold">
    <div class="card-header-gold">
        <h5>◇ {{ $title }}</h5>
    </div>
    <form class="row g-3 mb-4" method="GET">
        <div class="col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" name="from" value="{{ request('from', date('Y-m-01')) }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" name="to" value="{{ request('to', date('Y-m-d')) }}" class="form-control">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn-gold w-100" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
        </div>
    </form>
    <div class="text-center py-5" style="color:rgba(212,175,55,0.4)">
        <i class="bi bi-bar-chart" style="font-size:2.5rem"></i>
        <p class="mt-2">{{ $title }} — Apply filters above to generate the report.</p>
    </div>
</div>
@endsection
