<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\PayrollDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $status = $request->get('status');

        $employees = Employee::when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%$q%")
                      ->orWhere('employee_code', 'like', "%$q%")
                      ->orWhere('designation', 'like', "%$q%");
            })
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('hrms.employees.index', compact('employees', 'q', 'status'));
    }

    public function create()
    {
        $departments = DB::table('departments')->orderBy('name')->get();
        $branches    = DB::table('branches')->orderBy('name')->get();
        $employee    = null;
        return view('hrms.employees.form', compact('employee', 'departments', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_code'       => 'required|string|max:20|unique:employees',
            'name'                => 'required|string|max:150',
            'designation'         => 'nullable|string|max:100',
            'department_id'       => 'nullable|integer',
            'branch_id'           => 'nullable|integer',
            'date_of_joining'     => 'nullable|date',
            'date_of_birth'       => 'nullable|date',
            'gender'              => 'nullable|in:male,female,other',
            'mobile'              => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:150',
            'address'             => 'nullable|string',
            'bank_account'        => 'nullable|string|max:30',
            'bank_ifsc'           => 'nullable|string|max:20',
            'bank_name'           => 'nullable|string|max:100',
            'pan_number'          => 'nullable|string|max:20',
            'aadhaar_number'      => 'nullable|string|max:20',
            'pf_number'           => 'nullable|string|max:30',
            'esi_number'          => 'nullable|string|max:30',
            'basic_salary'        => 'nullable|numeric|min:0',
            'hra'                 => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'other_allowance'     => 'nullable|numeric|min:0',
            'pf_applicable'       => 'nullable|boolean',
            'esi_applicable'      => 'nullable|boolean',
            'tds_applicable'      => 'nullable|boolean',
            'status'              => 'nullable|in:active,inactive,terminated',
        ]);

        $data['pf_applicable']  = $request->boolean('pf_applicable');
        $data['esi_applicable'] = $request->boolean('esi_applicable');
        $data['tds_applicable'] = $request->boolean('tds_applicable');
        $data['gross_salary']   = ($data['basic_salary'] ?? 0)
                                + ($data['hra'] ?? 0)
                                + ($data['transport_allowance'] ?? 0)
                                + ($data['other_allowance'] ?? 0);

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show($id)
    {
        $employee = Employee::findOrFail($id);

        $currentMonth = now()->month;
        $currentYear  = now()->year;

        $attendanceSummary = AttendanceRecord::where('employee_id', $id)
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $latestPayroll = PayrollDetail::where('employee_id', $id)
            ->latest()
            ->with('payrollMonth')
            ->first();

        return view('hrms.employees.show', compact('employee', 'attendanceSummary', 'latestPayroll'));
    }

    public function edit($id)
    {
        $employee    = Employee::findOrFail($id);
        $departments = DB::table('departments')->orderBy('name')->get();
        $branches    = DB::table('branches')->orderBy('name')->get();
        return view('hrms.employees.form', compact('employee', 'departments', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $data = $request->validate([
            'employee_code'       => 'required|string|max:20|unique:employees,employee_code,' . $id,
            'name'                => 'required|string|max:150',
            'designation'         => 'nullable|string|max:100',
            'department_id'       => 'nullable|integer',
            'branch_id'           => 'nullable|integer',
            'date_of_joining'     => 'nullable|date',
            'date_of_birth'       => 'nullable|date',
            'gender'              => 'nullable|in:male,female,other',
            'mobile'              => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:150',
            'address'             => 'nullable|string',
            'bank_account'        => 'nullable|string|max:30',
            'bank_ifsc'           => 'nullable|string|max:20',
            'bank_name'           => 'nullable|string|max:100',
            'pan_number'          => 'nullable|string|max:20',
            'aadhaar_number'      => 'nullable|string|max:20',
            'pf_number'           => 'nullable|string|max:30',
            'esi_number'          => 'nullable|string|max:30',
            'basic_salary'        => 'nullable|numeric|min:0',
            'hra'                 => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'other_allowance'     => 'nullable|numeric|min:0',
            'pf_applicable'       => 'nullable|boolean',
            'esi_applicable'      => 'nullable|boolean',
            'tds_applicable'      => 'nullable|boolean',
            'status'              => 'nullable|in:active,inactive,terminated',
        ]);

        $data['pf_applicable']  = $request->boolean('pf_applicable');
        $data['esi_applicable'] = $request->boolean('esi_applicable');
        $data['tds_applicable'] = $request->boolean('tds_applicable');
        $data['gross_salary']   = ($data['basic_salary'] ?? 0)
                                + ($data['hra'] ?? 0)
                                + ($data['transport_allowance'] ?? 0)
                                + ($data['other_allowance'] ?? 0);

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy($id)
    {
        Employee::findOrFail($id)->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted.');
    }
}
