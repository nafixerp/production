<?php
namespace App\Http\Controllers;

use App\Models\CrmLead;
use App\Models\CrmActivity;
use App\Models\CrmCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CRMLeadController extends Controller
{
    private const STAGES = ['new','contacted','qualified','proposal_sent','negotiating','won','lost'];

    public function index(Request $request)
    {
        $q = CrmLead::query();
        if ($request->search) {
            $s = $request->search;
            $q->where(function($query) use ($s) {
                $query->where('contact_name','like',"%$s%")
                      ->orWhere('company_name','like',"%$s%")
                      ->orWhere('slno','like',"%$s%");
            });
        }
        $allLeads = $q->orderBy('lead_date','desc')->get();
        // Group by status for Kanban
        $pipeline = [];
        foreach (self::STAGES as $stage) {
            $pipeline[$stage] = $allLeads->where('status', $stage)->values();
        }
        $totalValue = $allLeads->whereNotIn('status',['lost'])->sum('estimated_value');
        return view('crm.leads.index', compact('pipeline','totalValue'));
    }

    public function create()
    {
        $slno = 'LEAD-' . date('Ymd') . '-' . str_pad(CrmLead::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        $staff = \App\Models\Staff::where('status', 1)->get();
        return view('crm.leads.form', compact('slno','staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slno'                => 'required|string|max:30|unique:crm_leads,slno',
            'lead_date'           => 'required|date',
            'company_name'        => 'nullable|string|max:180',
            'contact_name'        => 'required|string|max:100',
            'phone'               => 'required|string|max:20',
            'email'               => 'nullable|email|max:150',
            'city'                => 'required|string|max:80',
            'source'              => 'required|in:cold_call,referral,website,exhibition,social_media,walk_in,other',
            'product_interest'    => 'nullable|string',
            'estimated_value'     => 'nullable|numeric|min:0',
            'assigned_to'         => 'nullable|integer',
            'status'              => 'required|in:new,contacted,qualified,proposal_sent,negotiating,won,lost',
            'priority'            => 'required|in:low,medium,high',
            'lost_reason'         => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'narration'           => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        CrmLead::create($data);
        return redirect()->route('crm-leads.index')->with('success', 'Lead created successfully.');
    }

    public function show($id)
    {
        $lead = CrmLead::findOrFail($id);
        $activities = CrmActivity::where('lead_id', $id)->orderBy('activity_date','desc')->get();
        return view('crm.leads.show', compact('lead','activities'));
    }

    public function edit($id)
    {
        $lead = CrmLead::findOrFail($id);
        $staff = \App\Models\Staff::where('status', 1)->get();
        return view('crm.leads.form', compact('lead','staff'));
    }

    public function update(Request $request, $id)
    {
        $lead = CrmLead::findOrFail($id);
        $data = $request->validate([
            'slno'                => "required|string|max:30|unique:crm_leads,slno,{$id}",
            'lead_date'           => 'required|date',
            'company_name'        => 'nullable|string|max:180',
            'contact_name'        => 'required|string|max:100',
            'phone'               => 'required|string|max:20',
            'email'               => 'nullable|email|max:150',
            'city'                => 'required|string|max:80',
            'source'              => 'required|in:cold_call,referral,website,exhibition,social_media,walk_in,other',
            'product_interest'    => 'nullable|string',
            'estimated_value'     => 'nullable|numeric|min:0',
            'assigned_to'         => 'nullable|integer',
            'status'              => 'required|in:new,contacted,qualified,proposal_sent,negotiating,won,lost',
            'priority'            => 'required|in:low,medium,high',
            'lost_reason'         => 'nullable|string',
            'expected_close_date' => 'nullable|date',
            'narration'           => 'nullable|string',
        ]);
        $lead->update($data);
        return redirect()->route('crm-leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy($id)
    {
        CrmLead::findOrFail($id)->delete();
        return redirect()->route('crm-leads.index')->with('success', 'Lead deleted.');
    }

    public function convert(Request $request, $id)
    {
        $lead = CrmLead::findOrFail($id);
        if ($lead->converted_customer_id) {
            return back()->with('error', 'Lead already converted.');
        }
        $request->validate([
            'code' => 'required|string|max:30|unique:customers,code',
        ]);
        DB::transaction(function() use ($lead, $request) {
            $customer = CrmCustomer::create([
                'code'           => $request->code,
                'name'           => $lead->company_name ?? $lead->contact_name,
                'contact_person' => $lead->contact_name,
                'phone'          => $lead->phone,
                'email'          => $lead->email,
                'city'           => $lead->city,
                'customer_type'  => 'retail',
                'price_tier'     => 'standard',
                'status'         => 1,
                'created_by'     => auth()->id(),
            ]);
            $lead->update([
                'status'               => 'won',
                'converted_customer_id'=> $customer->id,
                'converted_at'         => now(),
            ]);
        });
        return redirect()->route('crm-leads.index')->with('success', 'Lead converted to customer successfully.');
    }
}
