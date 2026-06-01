@extends('layouts.app')
@section('title', 'Process Payroll')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-play-circle me-2"></i>Process Payroll</h5>
    <a href="{{ route('payroll.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="card-dark p-4" style="max-width:400px">
    <form method="POST" action="{{ route('payroll.process') }}">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label-gold">Month *</label>
                <select name="month" class="form-select form-control-dark" required>
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" @selected($m == now()->month)>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label-gold">Year *</label>
                <input type="number" name="year" class="form-control form-control-dark" value="{{ now()->year }}" required min="2020">
            </div>
        </div>
        <div class="alert alert-warning small">
            This will calculate salaries for all active employees using attendance and approved overtime records.
        </div>
        <button type="submit" class="btn btn-gold"><i class="bi bi-play-circle me-1"></i>Process Payroll</button>
    </form>
</div>
@endsection
