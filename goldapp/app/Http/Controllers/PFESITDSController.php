<?php

namespace App\Http\Controllers;

use App\Models\PayrollMonth;
use App\Models\PayrollDetail;
use App\Models\PfEsiTdsRecord;
use Illuminate\Http\Request;

class PFESITDSController extends Controller
{
    public function index(Request $request)
    {
        $records = PfEsiTdsRecord::with('payrollMonth')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(20);

        return view('hrms.pf-esi-tds.index', compact('records'));
    }

    public function create()
    {
        return redirect()->route('pf-esi-tds.index');
    }

    public function store(Request $request)
    {
        return redirect()->route('pf-esi-tds.index');
    }

    public function show($id)
    {
        $record  = PfEsiTdsRecord::with('payrollMonth')->findOrFail($id);
        $details = PayrollDetail::where('payroll_month_id', $record->payroll_month_id)
            ->orderBy('employee_name')
            ->get();

        return view('hrms.pf-esi-tds.show', compact('record', 'details'));
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('pf-esi-tds.index');
    }

    public function destroy($id)
    {
        return redirect()->route('pf-esi-tds.index');
    }

    public function markPaid(Request $request, $id)
    {
        $record = PfEsiTdsRecord::findOrFail($id);

        $record->update([
            'status'         => 'paid',
            'challan_number' => $request->challan_number,
            'challan_date'   => $request->challan_date,
            'payment_date'   => $request->payment_date ?? now()->toDateString(),
        ]);

        return back()->with('success', 'PF/ESI/TDS marked as paid.');
    }
}
