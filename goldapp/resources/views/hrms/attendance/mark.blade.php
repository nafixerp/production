@extends('layouts.app')
@section('title', 'Mark Attendance')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-calendar-plus me-2"></i>Mark Attendance</h5>
    <a href="{{ route('attendance.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form method="POST" action="{{ route('attendance.bulk') }}">
    @csrf
    <div class="card-dark p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label-gold">Date *</label>
                <input type="date" name="date" class="form-control form-control-dark" value="{{ $date }}" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-gold" onclick="markAll('present')">All Present</button>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">All Absent</button>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-dark-gold table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Employee</th>
                    <th>Status</th>
                    <th>In Time</th>
                    <th>Out Time</th>
                    <th>Hours</th>
                    <th>OT Hours</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $i => $emp)
                @php $rec = $existing->get($emp->id); @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>
                        <strong class="text-gold-light">{{ $emp->name }}</strong>
                        <br><small class="text-muted">{{ $emp->employee_code }}</small>
                    </td>
                    <td>
                        <select name="attendance[{{ $emp->id }}][status]" class="form-select form-control-dark form-select-sm status-select" style="min-width:110px">
                            <option value="present"  @selected(($rec?->status ?? 'present') == 'present')>Present</option>
                            <option value="absent"   @selected(($rec?->status ?? '') == 'absent')>Absent</option>
                            <option value="half_day" @selected(($rec?->status ?? '') == 'half_day')>Half Day</option>
                            <option value="leave"    @selected(($rec?->status ?? '') == 'leave')>Leave</option>
                            <option value="holiday"  @selected(($rec?->status ?? '') == 'holiday')>Holiday</option>
                            <option value="weekend"  @selected(($rec?->status ?? '') == 'weekend')>Weekend</option>
                        </select>
                    </td>
                    <td><input type="time" name="attendance[{{ $emp->id }}][in_time]" class="form-control form-control-dark form-control-sm" value="{{ $rec?->in_time ?? '' }}" style="width:100px"></td>
                    <td><input type="time" name="attendance[{{ $emp->id }}][out_time]" class="form-control form-control-dark form-control-sm" value="{{ $rec?->out_time ?? '' }}" style="width:100px"></td>
                    <td><input type="number" step="0.1" name="attendance[{{ $emp->id }}][hours_worked]" class="form-control form-control-dark form-control-sm" value="{{ $rec?->hours_worked ?? 0 }}" style="width:70px"></td>
                    <td><input type="number" step="0.1" name="attendance[{{ $emp->id }}][overtime_hours]" class="form-control form-control-dark form-control-sm" value="{{ $rec?->overtime_hours ?? 0 }}" style="width:70px"></td>
                    <td><input type="text" name="attendance[{{ $emp->id }}][remarks]" class="form-control form-control-dark form-control-sm" value="{{ $rec?->remarks ?? '' }}" placeholder="Optional"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-gold"><i class="bi bi-save me-1"></i>Save Attendance</button>
        <a href="{{ route('attendance.index') }}" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</form>

@push('scripts')
<script>
function markAll(status){
    document.querySelectorAll('.status-select').forEach(s=>s.value=status);
}
</script>
@endpush
@endsection
