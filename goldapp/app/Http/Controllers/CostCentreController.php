<?php
namespace App\Http\Controllers;

use App\Models\CostCentre;
use Illuminate\Http\Request;

class CostCentreController extends Controller
{
    public function index()
    {
        $costCentres = CostCentre::with('parent')->orderBy('name')->paginate(30);
        return view('finance.cost-centres.index', compact('costCentres'));
    }

    public function create()
    {
        $parents = CostCentre::where('status',1)->orderBy('name')->get();
        return view('finance.cost-centres.form', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'      => 'required|string|max:20|unique:cost_centres,code',
            'name'      => 'required|string|max:100',
            'parent_id' => 'nullable|integer',
            'budget'    => 'nullable|numeric|min:0',
            'status'    => 'nullable|integer',
        ]);
        $data['status'] = $request->status ?? 1;
        CostCentre::create($data);
        return redirect()->route('cost-centres.index')->with('success', 'Cost centre created.');
    }

    public function show($id)
    {
        $cc = CostCentre::with(['parent','children'])->findOrFail($id);
        return view('finance.cost-centres.show', compact('cc'));
    }

    public function edit($id)
    {
        $cc = CostCentre::findOrFail($id);
        $parents = CostCentre::where('id','!=',$id)->where('status',1)->orderBy('name')->get();
        return view('finance.cost-centres.form', compact('cc','parents'));
    }

    public function update(Request $request, $id)
    {
        $cc = CostCentre::findOrFail($id);
        $data = $request->validate([
            'code'      => "required|string|max:20|unique:cost_centres,code,{$id}",
            'name'      => 'required|string|max:100',
            'parent_id' => 'nullable|integer',
            'budget'    => 'nullable|numeric|min:0',
            'status'    => 'nullable|integer',
        ]);
        $data['status'] = $request->status ?? 1;
        $cc->update($data);
        return redirect()->route('cost-centres.index')->with('success', 'Cost centre updated.');
    }

    public function destroy($id)
    {
        $cc = CostCentre::findOrFail($id);
        if ($cc->children()->exists()) {
            return back()->with('error', 'Cannot delete cost centre with sub-centres.');
        }
        $cc->delete();
        return redirect()->route('cost-centres.index')->with('success', 'Cost centre deleted.');
    }
}
