<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\AttendanceRecord;
use App\Models\OvertimeRecord;
use App\Models\PayrollMonth;
use App\Models\PayrollDetail;
use App\Models\PfEsiTdsRecord;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    protected DaybookService $daybookService;

    public function __construct(DaybookService $daybookService)
    {
        $this->daybookService = $daybookService;
    }

    public function index(Request $request)
    {
        $months = PayrollMonth::orderByDesc('year')->orderByDesc('month')->paginate(20);
        return view('hrms.payroll.index', compact('months'));
    }

    public function create()
    {
        return view('hrms.payroll.process');
    }

    public function process(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2020|max:2099',
        ]);

        $month = (int) $request->month;
        $year  = (int) $request->year;

        // Prevent duplicate processing
        if (PayrollMonth::where('month', $month)->where('year', $year)->exists()) {
            return back()->with('error', "Payroll for " . date('F', mktime(0,0,0,$month,1)) . " $year already exists.");
        }

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Create payroll month
        $payrollMonth = PayrollMonth::create([
            'month'        => $month,
            'year'         => $year,
            'status'       => 'draft',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        $employees = Employee::where('status', 'active')->get();

        $totalGross       = 0;
        $totalDeductions  = 0;
        $totalNet         = 0;
        $totalPfEmployee  = 0;
        $totalPfEmployer  = 0;
        $totalEsiEmployee = 0;
        $totalEsiEmployer = 0;
        $totalTds         = 0;

        foreach ($employees as $emp) {
            // Attendance for the month
            $attendance = AttendanceRecord::where('employee_id', $emp->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $presentDays = $attendance->whereIn('status', ['present', 'half_day'])->count();
            $halfDays    = $attendance->where('status', 'half_day')->count();
            $presentDays = $presentDays - ($halfDays * 0.5); // half days count as 0.5
            $leaveDays   = $attendance->where('status', 'leave')->count();
            $absentDays  = $daysInMonth - $attendance->count() + $attendance->where('status', 'absent')->count();

            // Salary components - prorate by working days
            $workingDays = $daysInMonth;
            $earnedDays  = max(0, $presentDays + $leaveDays);
            $ratio       = $workingDays > 0 ? $earnedDays / $workingDays : 1;

            $basic     = round($emp->basic_salary * $ratio, 2);
            $hra       = round($emp->hra * $ratio, 2);
            $transport = round($emp->transport_allowance * $ratio, 2);
            $other     = round($emp->other_allowance * $ratio, 2);
            $gross     = $basic + $hra + $transport + $other;

            // Overtime
            $overtimeAmount = OvertimeRecord::where('employee_id', $emp->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'approved')
                ->sum('amount');

            $totalEarnings = $gross + $overtimeAmount;

            // Deductions
            $pfEmployee  = 0;
            $pfEmployer  = 0;
            $esiEmployee = 0;
            $esiEmployer = 0;
            $tds         = 0;

            if ($emp->pf_applicable) {
                $pfEmployee = round($basic * 0.12, 2);
                $pfEmployer = round($basic * 0.12, 2);
            }

            if ($emp->esi_applicable && $gross <= 21000) {
                $esiEmployee = round($gross * 0.0075, 2);
                $esiEmployer = round($gross * 0.0325, 2);
            }

            if ($emp->tds_applicable) {
                // Flat 10% TDS on annual basic > 2.5 lakh, simple monthly estimate
                $annualBasic = $emp->basic_salary * 12;
                if ($annualBasic > 250000) {
                    $tds = round(($annualBasic - 250000) * 0.10 / 12, 2);
                }
            }

            $totalDed = $pfEmployee + $esiEmployee + $tds;
            $netSalary = $totalEarnings - $totalDed;

            PayrollDetail::create([
                'payroll_month_id'    => $payrollMonth->id,
                'employee_id'         => $emp->id,
                'employee_name'       => $emp->name,
                'designation'         => $emp->designation,
                'basic'               => $basic,
                'hra'                 => $hra,
                'transport_allowance' => $transport,
                'other_allowance'     => $other,
                'gross_salary'        => $gross,
                'overtime_amount'     => $overtimeAmount,
                'total_earnings'      => $totalEarnings,
                'pf_employee'         => $pfEmployee,
                'pf_employer'         => $pfEmployer,
                'esi_employee'        => $esiEmployee,
                'esi_employer'        => $esiEmployer,
                'tds'                 => $tds,
                'other_deductions'    => 0,
                'total_deductions'    => $totalDed,
                'net_salary'          => $netSalary,
                'working_days'        => $workingDays,
                'present_days'        => (int) $earnedDays,
                'leave_days'          => $leaveDays,
                'absent_days'         => max(0, $absentDays),
                'status'              => 'pending',
            ]);

            $totalGross       += $gross;
            $totalDeductions  += $totalDed;
            $totalNet         += $netSalary;
            $totalPfEmployee  += $pfEmployee;
            $totalPfEmployer  += $pfEmployer;
            $totalEsiEmployee += $esiEmployee;
            $totalEsiEmployer += $esiEmployer;
            $totalTds         += $tds;
        }

        // Update payroll month totals
        $payrollMonth->update([
            'total_employees' => $employees->count(),
            'total_gross'     => $totalGross,
            'total_deductions'=> $totalDeductions,
            'total_net'       => $totalNet,
            'status'          => 'processed',
        ]);

        // Create PF/ESI/TDS record
        PfEsiTdsRecord::create([
            'payroll_month_id'  => $payrollMonth->id,
            'month'             => $month,
            'year'              => $year,
            'pf_employee_total' => $totalPfEmployee,
            'pf_employer_total' => $totalPfEmployer,
            'esi_employee_total'=> $totalEsiEmployee,
            'esi_employer_total'=> $totalEsiEmployer,
            'tds_total'         => $totalTds,
            'status'            => 'pending',
        ]);

        return redirect()->route('payroll.show', $payrollMonth->id)
            ->with('success', "Payroll processed for " . date('F', mktime(0,0,0,$month,1)) . " $year — {$employees->count()} employees.");
    }

    public function show($id)
    {
        $payrollMonth = PayrollMonth::with('details')->findOrFail($id);
        return view('hrms.payroll.show', compact('payrollMonth'));
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('payroll.show', $id);
    }

    public function store(Request $request)
    {
        return $this->process($request);
    }

    public function destroy($id)
    {
        $pm = PayrollMonth::findOrFail($id);
        if ($pm->status === 'paid') {
            return back()->with('error', 'Cannot delete a paid payroll month.');
        }
        $pm->delete();
        return redirect()->route('payroll.index')->with('success', 'Payroll month deleted.');
    }

    public function postSalary(Request $request, $id)
    {
        $payrollMonth = PayrollMonth::with('details')->findOrFail($id);

        if ($payrollMonth->status === 'paid') {
            return back()->with('error', 'Salary already posted for this month.');
        }

        DB::transaction(function () use ($payrollMonth) {
            $this->daybookService->insertSalaryDaybookEntries([
                'payroll_month'      => $payrollMonth,
                'net_total'          => $payrollMonth->total_net,
                'pf_employer_total'  => $payrollMonth->details->sum('pf_employer'),
                'month'              => $payrollMonth->month,
                'year'               => $payrollMonth->year,
            ]);

            // Update payroll details to paid
            $payrollMonth->details()->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            $payrollMonth->update(['status' => 'paid']);
        });

        return back()->with('success', 'Salary posted to daybook and marked as paid.');
    }
}
