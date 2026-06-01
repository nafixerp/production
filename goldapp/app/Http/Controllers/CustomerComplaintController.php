<?php
namespace App\Http\Controllers;

use App\Models\CustomerComplaint;
use App\Models\CrmCustomer;
use Illuminate\Http\Request;

class CustomerComplaintController extends Controller
{
    public function index(Request $request)
    {
        $q = CustomerComplaint::with('customer');
        if ($request->status) $q->where('status', $request->status);
        if ($request->priority) $q->where('priority', $request->priority);
        if ($request->search) {
            $s = $request->search;
            $q->where(function($query) use ($s) {
                $query->where('slno','like',"%$s%")->orWhere('subject','like',"%$s%")->orWhere('customer_name','like',"%$s%");
            });
        }
        $complaints = $q->orderBy('complaint_date','desc')->paginate(25)->withQueryString();
        return view('crm.complaints.index', compact('complaints'));
    }

    public function create()
    {
        $customers = CrmCustomer::where('status',1)->orderBy('name')->get();
        $slno = 'COMP-' . date('Ymd') . '-' . str_pad(CustomerComplaint::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        return view('crm.complaints.form', compact('customers','slno'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slno'             => 'required|string|max:30|unique:customer_complaints,slno',
            'complaint_date'   => 'required|date',
            'customer_id'      => 'required|integer',
            'complaint_type'   => 'required|in:quality,delivery,billing,service,product,other',
            'subject'          => 'required|string|max:255',
            'description'      => 'required|string',
            'priority'         => 'required|in:low,medium,high,critical',
            'invoice_id'       => 'nullable|integer',
        ]);
        $customer = CrmCustomer::findOrFail($data['customer_id']);
        $data['customer_name'] = $customer->name;
        $data['status'] = 'open';
        $data['created_by'] = auth()->id();
        CustomerComplaint::create($data);
        return redirect()->route('customer-complaints.index')->with('success', 'Complaint registered.');
    }

    public function show($id)
    {
        $complaint = CustomerComplaint::with('customer')->findOrFail($id);
        return view('crm.complaints.show', compact('complaint'));
    }

    public function edit($id)
    {
        $complaint = CustomerComplaint::findOrFail($id);
        $customers = CrmCustomer::where('status',1)->orderBy('name')->get();
        return view('crm.complaints.form', compact('complaint','customers'));
    }

    public function update(Request $request, $id)
    {
        $complaint = CustomerComplaint::findOrFail($id);
        $data = $request->validate([
            'slno'             => "required|string|max:30|unique:customer_complaints,slno,{$id}",
            'complaint_date'   => 'required|date',
            'customer_id'      => 'required|integer',
            'complaint_type'   => 'required|in:quality,delivery,billing,service,product,other',
            'subject'          => 'required|string|max:255',
            'description'      => 'required|string',
            'priority'         => 'required|in:low,medium,high,critical',
            'status'           => 'required|in:open,acknowledged,investigating,resolved,closed',
            'resolution'       => 'nullable|string',
            'invoice_id'       => 'nullable|integer',
        ]);
        $customer = CrmCustomer::findOrFail($data['customer_id']);
        $data['customer_name'] = $customer->name;
        // If resolving now
        if (in_array($data['status'], ['resolved','closed']) && !$complaint->resolved_by) {
            $data['resolved_by'] = auth()->id();
            $data['resolved_at'] = now();
        }
        $complaint->update($data);
        return redirect()->route('customer-complaints.index')->with('success', 'Complaint updated.');
    }

    public function destroy($id)
    {
        CustomerComplaint::findOrFail($id)->delete();
        return redirect()->route('customer-complaints.index')->with('success', 'Complaint deleted.');
    }

    public function escalate($id)
    {
        $complaint = CustomerComplaint::findOrFail($id);
        $complaint->update(['escalated' => 1, 'priority' => 'critical', 'status' => 'investigating']);
        return back()->with('success', 'Complaint escalated to critical priority.');
    }

    public function resolve(Request $request, $id)
    {
        $complaint = CustomerComplaint::findOrFail($id);
        $request->validate(['resolution' => 'required|string']);
        $complaint->update([
            'resolution'  => $request->resolution,
            'status'      => 'resolved',
            'resolved_by' => auth()->id(),
            'resolved_at' => now(),
        ]);
        return back()->with('success', 'Complaint resolved.');
    }
}
