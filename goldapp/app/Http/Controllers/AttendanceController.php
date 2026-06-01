<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $month      = $request->get('month', now()->month);
        $year       = $request->get('year', now()->year);
        $department = $request->get('department_id');

        $employees = Employee::where('status', 'active')
            ->when($department, fn($q) => $q->where('department_id', $department))
            ->orderBy('name')
            ->get();

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $records = AttendanceRecord::whereIn('employee_id', $employees->pluck('id'))
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->groupBy('employee_id');

        $departments = DB::table('departments')->orderBy('name')->get();

        return view('hrms.attendance.index', compact(
            'employees', 'records', 'month', 'year', 'daysInMonth', 'departments', 'department'
        ));
    }

    public function create()
    {
        return redirect()->route('attendance.mark');
    }

    public function markForm(Request $request)
    {
        $date      = $request->get('date', today()->toDateString());
        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        $existing = AttendanceRecord::whereIn('employee_id', $employees->pluck('id'))
            ->where('date', $date)
            ->get()
            ->keyBy('employee_id');

        return view('hrms.attendance.mark', compact('employees', 'date', 'existing'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'date'       => 'required|date',
            'attendance' => 'required|array',
        ]);

        $date = $request->date;
        $now  = now();

        foreach ($request->attendance as $employeeId => $data) {
            AttendanceRecord::updateOrInsert(
                ['employee_id' => $employeeId, 'date' => $date],
                [
                    'status'         => $data['status'] ?? 'absent',
                    'in_time'        => (!empty($data['in_time'])) ? $data['in_time'] : null,
                    'out_time'       => (!empty($data['out_time'])) ? $data['out_time'] : null,
                    'hours_worked'   => $data['hours_worked'] ?? 0,
                    'overtime_hours' => $data['overtime_hours'] ?? 0,
                    'remarks'        => $data['remarks'] ?? null,
                    'updated_at'     => $now,
                    'created_at'     => $now,
                ]
            );
        }

        return redirect()->route('attendance.index')->with('success', 'Attendance saved for ' . $date);
    }

    public function store(Request $request)
    {
        return $this->bulkStore($request);
    }

    public function show($id)
    {
        $record = AttendanceRecord::with('employee')->findOrFail($id);
        return view('hrms.attendance.show', compact('record'));
    }

    public function edit($id)
    {
        $record    = AttendanceRecord::findOrFail($id);
        $date      = $record->date->toDateString();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $existing  = AttendanceRecord::whereIn('employee_id', $employees->pluck('id'))
            ->where('date', $date)
            ->get()
            ->keyBy('employee_id');
        return view('hrms.attendance.mark', compact('employees', 'date', 'existing'));
    }

    public function update(Request $request, $id)
    {
        return $this->bulkStore($request);
    }

    public function destroy($id)
    {
        AttendanceRecord::findOrFail($id)->delete();
        return back()->with('success', 'Record deleted.');
    }
}
