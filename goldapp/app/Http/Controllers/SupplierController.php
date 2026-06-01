<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ApLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $suppliers = Supplier::when($q, function ($query) use ($q) {
            $query->where('name', 'like', "%$q%")
                  ->orWhere('code', 'like', "%$q%")
                  ->orWhere('gst_no', 'like', "%$q%");
        })
        ->withCount(['purchaseOrders as po_count'])
        ->orderBy('name')
        ->paginate(20)->withQueryString();

        // Attach current AP balance
        $supplierIds = $suppliers->pluck('id');
        $balances = ApLedger::whereIn('supplier_id', $supplierIds)
            ->selectRaw('supplier_id, SUM(credit) - SUM(debit) as balance')
            ->groupBy('supplier_id')
            ->pluck('balance', 'supplier_id');

        return view('procurement.suppliers.index', compact('suppliers', 'q', 'balances'));
    }

    public function create()
    {
        return view('procurement.suppliers.form', ['supplier' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'   => 'required|string|max:30|unique:suppliers,code',
            'name'   => 'required|string|max:180',
            'status' => 'required|in:active,inactive,blacklisted',
        ]);

        $supplier = Supplier::create(array_merge($request->except('_token'), ['created_by' => Auth::id()]));

        return redirect()->route('suppliers.index')->with('success', "Supplier '{$supplier->name}' created.");
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('purchaseOrders', 'apLedger');
        $apBalance = ApLedger::where('supplier_id', $supplier->id)
            ->selectRaw('SUM(credit) - SUM(debit) as balance')->value('balance');
        return view('procurement.suppliers.show', compact('supplier', 'apBalance'));
    }

    public function edit(Supplier $supplier)
    {
        return view('procurement.suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'code'   => 'required|string|max:30|unique:suppliers,code,' . $supplier->id,
            'name'   => 'required|string|max:180',
            'status' => 'required|in:active,inactive,blacklisted',
        ]);

        $supplier->update($request->except(['_token', '_method']));
        return redirect()->route('suppliers.index')->with('success', "Supplier updated.");
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', "Supplier deleted.");
    }

    /**
     * Check credit limit for a supplier given a PO amount
     */
    public function checkCreditLimit(Supplier $supplier, float $poAmount): array
    {
        $outstanding = ApLedger::where('supplier_id', $supplier->id)
            ->selectRaw('SUM(credit) - SUM(debit) as balance')->value('balance') ?? 0;
        $available = $supplier->credit_limit - $outstanding;
        return [
            'credit_limit'  => $supplier->credit_limit,
            'outstanding'   => $outstanding,
            'available'     => $available,
            'within_limit'  => $poAmount <= $available,
        ];
    }
}
