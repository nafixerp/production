<?php
namespace App\Http\Controllers;

use App\Models\CrmOpportunity;
use App\Models\CrmLead;
use App\Models\CrmCustomer;
use App\Models\FinishedGoods;
use Illuminate\Http\Request;

class CRMOpportunityController extends Controller
{
    private const STAGES = ['prospect','qualified','proposal','negotiation','closed_won','closed_lost'];

    public function index(Request $request)
    {
        $q = CrmOpportunity::with(['lead','customer']);
        if ($request->stage) $q->where('stage', $request->stage);
        $all = $q->orderBy('expected_close','asc')->get();
        $pipeline = [];
        foreach (self::STAGES as $s) {
            $pipeline[$s] = $all->where('stage', $s)->values();
        }
        $totalOpen = $all->whereNotIn('stage',['closed_lost'])->sum('value');
        $weighted = $all->whereNotIn('stage',['closed_lost'])->sum(fn($o) => $o->value * $o->probability / 100);
        return view('crm.opportunities.index', compact('pipeline','totalOpen','weighted'));
    }

    public function create()
    {
        $leads = CrmLead::orderBy('contact_name')->get();
        $customers = CrmCustomer::where('status',1)->orderBy('name')->get();
        $products = FinishedGoods::where('status',1)->orderBy('name')->get();
        $staff = \App\Models\Staff::where('status',1)->get();
        return view('crm.opportunities.form', compact('leads','customers','products','staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lead_id'       => 'nullable|integer',
            'customer_id'   => 'nullable|integer',
            'title'         => 'required|string|max:255',
            'value'         => 'required|numeric|min:0',
            'probability'   => 'required|integer|min:0|max:100',
            'expected_close'=> 'required|date',
            'stage'         => 'required|in:prospect,qualified,proposal,negotiation,closed_won,closed_lost',
            'product_ids'   => 'nullable|array',
            'assigned_to'   => 'required|integer',
            'narration'     => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        CrmOpportunity::create($data);
        return redirect()->route('crm-opportunities.index')->with('success', 'Opportunity created.');
    }

    public function show($id)
    {
        $opportunity = CrmOpportunity::with(['lead','customer'])->findOrFail($id);
        return view('crm.opportunities.show', compact('opportunity'));
    }

    public function edit($id)
    {
        $opportunity = CrmOpportunity::findOrFail($id);
        $leads = CrmLead::orderBy('contact_name')->get();
        $customers = CrmCustomer::where('status',1)->orderBy('name')->get();
        $products = FinishedGoods::where('status',1)->orderBy('name')->get();
        $staff = \App\Models\Staff::where('status',1)->get();
        return view('crm.opportunities.form', compact('opportunity','leads','customers','products','staff'));
    }

    public function update(Request $request, $id)
    {
        $opportunity = CrmOpportunity::findOrFail($id);
        $data = $request->validate([
            'lead_id'       => 'nullable|integer',
            'customer_id'   => 'nullable|integer',
            'title'         => 'required|string|max:255',
            'value'         => 'required|numeric|min:0',
            'probability'   => 'required|integer|min:0|max:100',
            'expected_close'=> 'required|date',
            'stage'         => 'required|in:prospect,qualified,proposal,negotiation,closed_won,closed_lost',
            'product_ids'   => 'nullable|array',
            'assigned_to'   => 'required|integer',
            'narration'     => 'nullable|string',
        ]);
        $opportunity->update($data);
        return redirect()->route('crm-opportunities.index')->with('success', 'Opportunity updated.');
    }

    public function destroy($id)
    {
        CrmOpportunity::findOrFail($id)->delete();
        return redirect()->route('crm-opportunities.index')->with('success', 'Opportunity deleted.');
    }
}
