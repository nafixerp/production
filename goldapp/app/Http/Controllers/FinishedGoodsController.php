<?php
namespace App\Http\Controllers;

use App\Models\FinishedGoods;
use App\Models\FgStock;
use App\Models\TaxGst;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinishedGoodsController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $items = FinishedGoods::with('unit')
            ->when($q, fn($qry) => $qry->where('name', 'like', "%$q%")->orWhere('code', 'like', "%$q%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        // Attach current stock and nearest expiry per FG
        $fgIds = $items->pluck('id');
        $stocks = FgStock::whereIn('fg_id', $fgIds)
            ->selectRaw('fg_id, SUM(qty_in)-SUM(qty_out)-SUM(qty_reserved) as net_qty, MIN(expiry_date) as nearest_expiry')
            ->groupBy('fg_id')
            ->get()
            ->keyBy('fg_id');

        return view('warehouse.finished-goods.index', compact('items', 'q', 'stocks'));
    }

    public function create()
    {
        $units = Unit::orderBy('name')->get();
        $taxes = TaxGst::orderBy('name')->get();
        return view('warehouse.finished-goods.form', compact('units', 'taxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:30|unique:finished_goods,code',
            'name' => 'required|string|max:180',
        ]);

        FinishedGoods::create(array_merge($request->except('_token'), [
            'created_by' => Auth::id(),
            'allergen_ids' => $request->allergen_ids ? json_encode($request->allergen_ids) : null,
        ]));

        return redirect()->route('finished-goods.index')->with('success', 'Finished Good created.');
    }

    public function show(FinishedGoods $finishedGood)
    {
        $stocks = FgStock::where('fg_id', $finishedGood->id)
            ->with('warehouse')
            ->orderBy('expiry_date')
            ->get();

        $totalStock = $stocks->sum(fn($s) => $s->qty_in - $s->qty_out - $s->qty_reserved);

        return view('warehouse.finished-goods.show', compact('finishedGood', 'stocks', 'totalStock'));
    }

    public function edit(FinishedGoods $finishedGood)
    {
        $units = Unit::orderBy('name')->get();
        $taxes = TaxGst::orderBy('name')->get();
        return view('warehouse.finished-goods.form', compact('finishedGood', 'units', 'taxes'));
    }

    public function update(Request $request, FinishedGoods $finishedGood)
    {
        $request->validate([
            'code' => 'required|string|max:30|unique:finished_goods,code,' . $finishedGood->id,
            'name' => 'required|string|max:180',
        ]);

        $finishedGood->update(array_merge($request->except(['_token','_method']), [
            'allergen_ids' => $request->allergen_ids ? json_encode($request->allergen_ids) : null,
        ]));

        return redirect()->route('finished-goods.index')->with('success', 'Finished Good updated.');
    }

    public function destroy(FinishedGoods $finishedGood)
    {
        $finishedGood->update(['status' => 0]);
        return redirect()->route('finished-goods.index')->with('success', 'Finished Good deactivated.');
    }
}
