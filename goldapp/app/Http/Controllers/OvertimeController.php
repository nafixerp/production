<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\OvertimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $month      = $request->get('month');
        $year       = $request->get('year', now()->year);

        $records = OvertimeRecord::with('employee')
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->when($month, fn($q) => $q->whereMonth('date', $month)->whereYear('date', $year))
            ->orderByDesc('date')
            ->paginate(20)
            ->withQueryString();

        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        return view('hrms.overtime.index', compact('records', 'employees', 'employeeId', 'month', 'year'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $record    = null;
        return view('hrms.overtime.form', compact('employees', 'record'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'date'          => 'required|date',
            'hours'         => 'required|numeric|min:0.5',
            'rate_per_hour' => 'nullable|numeric|min:0',
        ]);

        $data['amount'] = ($data['hours'] ?? 0) * ($data['rate_per_hour'] ?? 0);
        $data['status'] = 'pending';

        OvertimeRecord::create($data);

        return redirect()->route('overtime.index')->with('success', 'Overtime record added.');
    }

    public function show($id)
    {
        $record    = OvertimeRecord::with('employee')->findOrFail($id);
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        return view('hrms.overtime.form', compact('record', 'employees'));
    }

    public function edit($id)
    {
        $record    = OvertimeRecord::with('employee')->findOrFail($id);
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        return view('hrms.overtime.form', compact('record', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $record = OvertimeRecord::findOrFail($id);

        $data = $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'date'          => 'required|date',
            'hours'         => 'required|numeric|min:0.5',
            'rate_per_hour' => 'nullable|numeric|min:0',
        ]);

        $data['amount'] = ($data['hours'] ?? 0) * ($data['rate_per_hour'] ?? 0);

        $record->update($data);

        return redirect()->route('overtime.index')->with('success', 'Overtime record updated.');
    }

    public function destroy($id)
    {
        OvertimeRecord::findOrFail($id)->delete();
        return redirect()->route('overtime.index')->with('success', 'Overtime record deleted.');
    }

    public function approve($id)
    {
        $record = OvertimeRecord::findOrFail($id);
        $record->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'amount'      => $record->hours * $record->rate_per_hour,
        ]);
        return back()->with('success', 'Overtime approved. Amount: ₹' . number_format($record->amount, 2));
    }
}
