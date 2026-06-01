<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveManagementController extends Controller
{
    public function index(Request $request)
    {
        $status     = $request->get('status');
        $employeeId = $request->get('employee_id');

        $leaves = LeaveApplication::with('employee')
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($employeeId, fn($q) => $q->where('employee_id', $employeeId))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        return view('hrms.leaves.index', compact('leaves', 'status', 'employeeId', 'employees'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $leave     = null;
        return view('hrms.leaves.form', compact('employees', 'leave'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|in:casual,sick,earned,maternity,unpaid',
            'from_date'   => 'required|date',
            'to_date'     => 'required|date|after_or_equal:from_date',
            'reason'      => 'nullable|string',
        ]);

        // Calculate days
        $from = \Carbon\Carbon::parse($data['from_date']);
        $to   = \Carbon\Carbon::parse($data['to_date']);
        $data['days']   = $from->diffInDays($to) + 1;
        $data['status'] = 'pending';

        // Check leave balance (count approved leaves this year by type)
        $usedDays = LeaveApplication::where('employee_id', $data['employee_id'])
            ->where('leave_type', $data['leave_type'])
            ->where('status', 'approved')
            ->whereYear('from_date', now()->year)
            ->sum('days');

        $balanceLimits = [
            'casual'    => 12,
            'sick'      => 12,
            'earned'    => 15,
            'maternity' => 180,
            'unpaid'    => 365,
        ];
        $limit = $balanceLimits[$data['leave_type']] ?? 30;

        if (($usedDays + $data['days']) > $limit && $data['leave_type'] !== 'unpaid') {
            return back()->withInput()->with('error', "Insufficient leave balance. Used: {$usedDays}, Applying: {$data['days']}, Limit: {$limit}");
        }

        LeaveApplication::create($data);

        return redirect()->route('leaves.index')->with('success', 'Leave application submitted.');
    }

    public function show($id)
    {
        $leave = LeaveApplication::with('employee')->findOrFail($id);
        return view('hrms.leaves.show', compact('leave'));
    }

    public function edit($id)
    {
        $leave     = LeaveApplication::findOrFail($id);
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        return view('hrms.leaves.form', compact('leave', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $leave = LeaveApplication::findOrFail($id);

        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|in:casual,sick,earned,maternity,unpaid',
            'from_date'   => 'required|date',
            'to_date'     => 'required|date|after_or_equal:from_date',
            'reason'      => 'nullable|string',
        ]);

        $from           = \Carbon\Carbon::parse($data['from_date']);
        $to             = \Carbon\Carbon::parse($data['to_date']);
        $data['days']   = $from->diffInDays($to) + 1;

        $leave->update($data);

        return redirect()->route('leaves.index')->with('success', 'Leave application updated.');
    }

    public function destroy($id)
    {
        LeaveApplication::findOrFail($id)->delete();
        return redirect()->route('leaves.index')->with('success', 'Leave application deleted.');
    }

    public function approve($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $leave->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Leave approved.');
    }

    public function reject($id)
    {
        $leave = LeaveApplication::findOrFail($id);
        $leave->update([
            'status'   => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'remarks'  => request('remarks'),
        ]);
        return back()->with('success', 'Leave rejected.');
    }
}
