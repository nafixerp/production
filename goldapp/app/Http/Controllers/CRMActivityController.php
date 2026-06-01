<?php
namespace App\Http\Controllers;

use App\Models\CrmActivity;
use App\Models\CrmLead;
use App\Models\CrmCustomer;
use Illuminate\Http\Request;

class CRMActivityController extends Controller
{
    public function index(Request $request)
    {
        $q = CrmActivity::with(['lead','customer','doneBy']);
        if ($request->date) $q->whereDate('activity_date', $request->date);
        if ($request->type) $q->where('activity_type', $request->type);
        if ($request->lead_id) $q->where('lead_id', $request->lead_id);
        if ($request->customer_id) $q->where('customer_id', $request->customer_id);
        $activities = $q->orderBy('activity_date','desc')->paginate(30)->withQueryString();
        return view('crm.activities.index', compact('activities'));
    }

    public function create(Request $request)
    {
        $leads = CrmLead::whereNotIn('status',['won','lost'])->orderBy('contact_name')->get();
        $customers = CrmCustomer::where('status',1)->orderBy('name')->get();
        $preLeadId = $request->lead_id;
        $preCustomerId = $request->customer_id;
        return view('crm.activities.form', compact('leads','customers','preLeadId','preCustomerId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lead_id'          => 'nullable|integer',
            'customer_id'      => 'nullable|integer',
            'activity_date'    => 'required|date',
            'activity_type'    => 'required|in:call,email,visit,demo,follow_up,whatsapp,meeting',
            'subject'          => 'required|string|max:255',
            'notes'            => 'required|string',
            'outcome'          => 'nullable|string',
            'next_action'      => 'nullable|string|max:255',
            'next_action_date' => 'nullable|date',
            'done_by'          => 'required|integer',
        ]);
        CrmActivity::create($data);
        // Redirect back to lead or customer if linked
        if ($data['lead_id']) return redirect()->route('crm-leads.show', $data['lead_id'])->with('success', 'Activity logged.');
        if ($data['customer_id']) return redirect()->route('customers.show', $data['customer_id'])->with('success', 'Activity logged.');
        return redirect()->route('crm-activities.index')->with('success', 'Activity logged.');
    }

    public function show($id)
    {
        $activity = CrmActivity::with(['lead','customer','doneBy'])->findOrFail($id);
        return view('crm.activities.show', compact('activity'));
    }

    public function edit($id)
    {
        $activity = CrmActivity::findOrFail($id);
        $leads = CrmLead::orderBy('contact_name')->get();
        $customers = CrmCustomer::where('status',1)->orderBy('name')->get();
        return view('crm.activities.form', compact('activity','leads','customers'));
    }

    public function update(Request $request, $id)
    {
        $activity = CrmActivity::findOrFail($id);
        $data = $request->validate([
            'lead_id'          => 'nullable|integer',
            'customer_id'      => 'nullable|integer',
            'activity_date'    => 'required|date',
            'activity_type'    => 'required|in:call,email,visit,demo,follow_up,whatsapp,meeting',
            'subject'          => 'required|string|max:255',
            'notes'            => 'required|string',
            'outcome'          => 'nullable|string',
            'next_action'      => 'nullable|string|max:255',
            'next_action_date' => 'nullable|date',
            'done_by'          => 'required|integer',
        ]);
        $activity->update($data);
        return redirect()->route('crm-activities.index')->with('success', 'Activity updated.');
    }

    public function destroy($id)
    {
        CrmActivity::findOrFail($id)->delete();
        return redirect()->route('crm-activities.index')->with('success', 'Activity deleted.');
    }
}
