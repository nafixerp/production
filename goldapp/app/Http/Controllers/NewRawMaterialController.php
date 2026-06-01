<?php
namespace App\Http\Controllers;

use App\Models\RawMaterialNew;
use App\Models\InventoryStock;
use App\Models\Unit;
use App\Models\TaxGst;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewRawMaterialController extends Controller
{
    public function index(Request $request)
    {
        $q        = $request->q;
        $category = $request->category;
        $status   = $request->status;

        $materials = RawMaterialNew::with('unit')
            ->when($q, fn($query) => $query->where('name', 'like', "%$q%")->orWhere('code', 'like', "%$q%"))
            ->when($category, fn($query) => $query->where('category', $category))
            ->when($status !== null && $status !== '', fn($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(25)->withQueryString();

        // Attach current stock balance
        $ids      = $materials->pluck('id');
        $stocks   = InventoryStock::whereIn('item_id', $ids)
            ->where('item_type', 'RM')
            ->selectRaw('item_id, SUM(qty_in - qty_out) as balance_qty')
            ->groupBy('item_id')
            ->pluck('balance_qty', 'item_id');

        $categories = RawMaterialNew::selectRaw('DISTINCT category')->whereNotNull('category')->pluck('category');

        return view('manufacturing.raw-materials.index', compact('materials', 'q', 'category', 'status', 'stocks', 'categories'));
    }

    public function create()
    {
        $units = Unit::orderBy('name')->get();
        $taxes = TaxGst::where('status', 1)->orderBy('name')->get();
        return view('manufacturing.raw-materials.form', [
            'material' => null,
            'units'    => $units,
            'taxes'    => $taxes,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:30|unique:raw_materials,code',
            'name' => 'required|string|max:180',
        ]);

        $data = $request->except(['_token']);
        $data['created_by'] = Auth::id();
        $data['allergen_ids'] = $request->allergen_ids ? json_encode($request->allergen_ids) : null;

        RawMaterialNew::create($data);
        return redirect()->route('raw-materials-new.index')->with('success', 'Raw Material created.');
    }

    public function show(RawMaterialNew $rawMaterialNew)
    {
        $stock = InventoryStock::where('item_type', 'RM')
            ->where('item_id', $rawMaterialNew->id)
            ->get();
        $totalStock = $stock->sum(fn($s) => $s->qty_in - $s->qty_out);
        return view('manufacturing.raw-materials.show', compact('rawMaterialNew', 'stock', 'totalStock'));
    }

    public function edit(RawMaterialNew $rawMaterialNew)
    {
        $units = Unit::orderBy('name')->get();
        $taxes = TaxGst::where('status', 1)->orderBy('name')->get();
        return view('manufacturing.raw-materials.form', [
            'material' => $rawMaterialNew,
            'units'    => $units,
            'taxes'    => $taxes,
        ]);
    }

    public function update(Request $request, RawMaterialNew $rawMaterialNew)
    {
        $request->validate([
            'code' => 'required|string|max:30|unique:raw_materials,code,' . $rawMaterialNew->id,
            'name' => 'required|string|max:180',
        ]);

        $data = $request->except(['_token', '_method']);
        $data['allergen_ids'] = $request->allergen_ids ? json_encode($request->allergen_ids) : null;
        $rawMaterialNew->update($data);
        return redirect()->route('raw-materials-new.index')->with('success', 'Raw Material updated.');
    }

    public function destroy(RawMaterialNew $rawMaterialNew)
    {
        $rawMaterialNew->delete();
        return redirect()->route('raw-materials-new.index')->with('success', 'Raw Material deleted.');
    }
}
