<?php

namespace App\Http\Controllers;

use App\Models\EwayBill;
use Illuminate\Http\Request;

class EwayBillController extends Controller
{
    public function index()
    {
        $bills = EwayBill::orderByDesc('created_at')->paginate(25);
        return view('integrations.eway-bills.index', compact('bills'));
    }

    public function create()
    {
        return view('integrations.eway-bills.form', ['bill' => new EwayBill()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sales_invoice_id' => 'required|integer',
            'from_gstin' => 'required|string|max:30',
            'to_gstin' => 'required|string|max:30',
            'transporter_id' => 'nullable|string|max:30',
            'vehicle_no' => 'nullable|string|max:20',
            'distance_km' => 'nullable|integer',
        ]);

        $ewbNo = 'EWB' . date('Y') . str_pad(rand(1, 99999), 8, '0', STR_PAD_LEFT);
        EwayBill::create(array_merge($data, [
            'ewb_no' => $ewbNo,
            'ewb_date' => now(),
            'valid_till' => now()->addDays(15),
            'status' => 'active',
        ]));

        return redirect()->route('eway-bills.index')->with('success', "E-Way Bill {$ewbNo} generated.");
    }

    public function show(EwayBill $ewayBill)
    {
        return view('integrations.eway-bills.form', ['bill' => $ewayBill]);
    }

    public function edit(EwayBill $ewayBill)
    {
        return view('integrations.eway-bills.form', ['bill' => $ewayBill]);
    }

    public function update(Request $request, EwayBill $ewayBill)
    {
        $data = $request->validate([
            'vehicle_no' => 'nullable|string|max:20',
            'transporter_id' => 'nullable|string|max:30',
            'status' => 'nullable|in:active,cancelled,extended',
        ]);
        $ewayBill->update($data);
        return redirect()->route('eway-bills.index')->with('success', 'E-Way Bill updated.');
    }

    public function destroy(EwayBill $ewayBill)
    {
        $ewayBill->update(['status' => 'cancelled']);
        return redirect()->route('eway-bills.index')->with('success', 'E-Way Bill cancelled.');
    }
}
