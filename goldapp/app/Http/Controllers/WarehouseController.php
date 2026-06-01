<?php
namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Models\Branch;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = Warehouse::when($request->q, fn($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->orderBy('name')
            ->paginate(20)->withQueryString();
        return view('warehouse.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        $branches = Branch::where('status', 1)->orderBy('name')->get();
        return view('warehouse.warehouses.form', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:30|unique:warehouses,code',
            'name' => 'required|string|max:180',
            'type' => 'required|in:raw,finished,packing,cold',
        ]);
        Warehouse::create($request->except(['_token']));
        return redirect()->route('warehouses.index')->with('success', 'Warehouse created.');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load('locations');
        $stockSummary = $warehouse->fgStocks()
            ->selectRaw('fg_name, fg_code, SUM(qty_in)-SUM(qty_out)-SUM(qty_reserved) as net_qty, SUM((qty_in-qty_out-qty_reserved)*cost_rate) as total_value')
            ->groupBy('fg_id','fg_name','fg_code')
            ->get();
        return view('warehouse.warehouses.show', compact('warehouse','stockSummary'));
    }

    public function edit(Warehouse $warehouse)
    {
        $branches = Branch::where('status', 1)->orderBy('name')->get();
        return view('warehouse.warehouses.form', compact('warehouse', 'branches'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'code' => 'required|string|max:30|unique:warehouses,code,' . $warehouse->id,
            'name' => 'required|string|max:180',
        ]);
        $warehouse->update($request->except(['_token','_method']));
        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->update(['status' => 0]);
        return redirect()->route('warehouses.index')->with('success', 'Warehouse deactivated.');
    }
}
