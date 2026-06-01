<?php
namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Supplier;
use App\Models\PurchaseInvoiceNew;
use App\Models\Daybook;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewPurchaseReturnController extends Controller
{
    protected DaybookService $daybookService;

    public function __construct(DaybookService $daybookService)
    {
        $this->daybookService = $daybookService;
    }

    public function index(Request $request)
    {
        $q    = $request->q;
        $from = $request->from;
        $to   = $request->to;

        $returns = PurchaseReturn::with('supplier')
            ->when($q, fn($query) => $query->where('supplier_name', 'like', "%$q%")->orWhere('slno', 'like', "%$q%"))
            ->when($from, fn($query) => $query->where('return_date', '>=', $from))
            ->when($to, fn($query) => $query->where('return_date', '<=', $to))
            ->orderByDesc('return_date')->orderByDesc('id')
            ->paginate(20)->withQueryString();

        return view('procurement.purchase-returns.index', compact('returns', 'q', 'from', 'to'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $invoices  = PurchaseInvoiceNew::whereIn('status', ['posted', 'partial'])->with('supplier')->orderByDesc('id')->get();
        $nextSlno  = $this->daybookService->nextSlno('PRET', 'purchase_returns');

        return view('procurement.purchase-returns.form', [
            'return'    => null,
            'suppliers' => $suppliers,
            'invoices'  => $invoices,
            'nextSlno'  => $nextSlno,
            'items'     => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'return_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        DB::transaction(function () use ($request) {
            $slno     = $this->daybookService->nextSlno('PRET', 'purchase_returns');
            $supplier = Supplier::findOrFail($request->supplier_id);
            $items    = $request->input('items', []);

            $taxable = 0;
            foreach ($items as $item) {
                $taxable += (float)($item['amount'] ?? 0);
            }
            $sgst = (float)($request->sgst ?? 0);
            $cgst = (float)($request->cgst ?? 0);
            $igst = (float)($request->igst ?? 0);
            $net  = $taxable + $sgst + $cgst + $igst;

            $ret = PurchaseReturn::create([
                'slno'           => $slno,
                'return_no'      => $slno,
                'return_date'    => $request->return_date,
                'invoice_id'     => $request->invoice_id ?: null,
                'grn_id'         => $request->grn_id ?: null,
                'supplier_id'    => $supplier->id,
                'supplier_name'  => $supplier->name,
                'reason'         => $request->reason,
                'taxable_amount' => $taxable,
                'sgst'           => $sgst,
                'cgst'           => $cgst,
                'igst'           => $igst,
                'net_amount'     => $net,
                'status'         => 'draft',
                'narration'      => $request->narration,
                'branch_id'      => $request->branch_id,
                'created_by'     => Auth::id(),
            ]);

            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                PurchaseReturnItem::create([
                    'return_id'  => $ret->id,
                    'item_type'  => $item['item_type'] ?? 'RM',
                    'item_id'    => $item['item_id'] ?: null,
                    'item_code'  => $item['item_code'] ?? '',
                    'item_name'  => $item['item_name'],
                    'unit'       => $item['unit'] ?? 'KG',
                    'qty'        => (float)($item['qty'] ?? 0),
                    'rate'       => (float)($item['rate'] ?? 0),
                    'amount'     => (float)($item['amount'] ?? 0),
                    'batch_no'   => $item['batch_no'] ?? null,
                ]);
            }
        });

        return redirect()->route('purchase-returns-new.index')->with('success', 'Purchase Return saved.');
    }

    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load('supplier', 'items');
        $daybookEntries = Daybook::where('slno', $purchaseReturn->slno)->get();
        return view('procurement.purchase-returns.show', compact('purchaseReturn', 'daybookEntries'));
    }

    public function edit(PurchaseReturn $purchaseReturn)
    {
        if ($purchaseReturn->status === 'completed') {
            return redirect()->route('purchase-returns-new.show', $purchaseReturn)->with('error', 'Cannot edit completed return.');
        }
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $invoices  = PurchaseInvoiceNew::whereIn('status', ['posted', 'partial'])->with('supplier')->orderByDesc('id')->get();
        $purchaseReturn->load('items');

        return view('procurement.purchase-returns.form', [
            'return'    => $purchaseReturn,
            'suppliers' => $suppliers,
            'invoices'  => $invoices,
            'nextSlno'  => $purchaseReturn->slno,
            'items'     => $purchaseReturn->items,
        ]);
    }

    public function update(Request $request, PurchaseReturn $purchaseReturn)
    {
        $request->validate([
            'return_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        DB::transaction(function () use ($request, $purchaseReturn) {
            $supplier = Supplier::findOrFail($request->supplier_id);
            $items    = $request->input('items', []);
            $taxable  = collect($items)->sum(fn($i) => (float)($i['amount'] ?? 0));
            $sgst = (float)($request->sgst ?? 0);
            $cgst = (float)($request->cgst ?? 0);
            $igst = (float)($request->igst ?? 0);

            $purchaseReturn->update([
                'return_date'    => $request->return_date,
                'supplier_id'    => $supplier->id,
                'supplier_name'  => $supplier->name,
                'reason'         => $request->reason,
                'taxable_amount' => $taxable,
                'sgst'           => $sgst,
                'cgst'           => $cgst,
                'igst'           => $igst,
                'net_amount'     => $taxable + $sgst + $cgst + $igst,
                'narration'      => $request->narration,
            ]);

            $purchaseReturn->items()->delete();
            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                PurchaseReturnItem::create([
                    'return_id' => $purchaseReturn->id,
                    'item_type' => $item['item_type'] ?? 'RM',
                    'item_id'   => $item['item_id'] ?: null,
                    'item_code' => $item['item_code'] ?? '',
                    'item_name' => $item['item_name'],
                    'unit'      => $item['unit'] ?? 'KG',
                    'qty'       => (float)($item['qty'] ?? 0),
                    'rate'      => (float)($item['rate'] ?? 0),
                    'amount'    => (float)($item['amount'] ?? 0),
                    'batch_no'  => $item['batch_no'] ?? null,
                ]);
            }
        });

        return redirect()->route('purchase-returns-new.index')->with('success', 'Return updated.');
    }

    public function destroy(PurchaseReturn $purchaseReturn)
    {
        if ($purchaseReturn->status === 'completed') {
            return back()->with('error', 'Cannot delete completed return.');
        }
        DB::transaction(function () use ($purchaseReturn) {
            $purchaseReturn->items()->delete();
            $purchaseReturn->delete();
        });
        return redirect()->route('purchase-returns-new.index')->with('success', 'Return deleted.');
    }

    /**
     * Approve return and post reverse daybook entries
     */
    public function approve(Request $request, $id)
    {
        $ret = PurchaseReturn::findOrFail($id);
        if ($ret->status !== 'draft') {
            return back()->with('error', 'Only draft returns can be approved.');
        }

        DB::transaction(function () use ($ret) {
            $ret->update(['status' => 'approved']);

            // Post reverse daybook entries (mirror of purchase invoice)
            // Supplier  -net_amount  (Debit: reduce AP)
            // Purchase  +taxable     (Credit: reverse purchase)
            // SGST      +sgst        (Credit: reverse input SGST)
            // etc.
            $slno  = $ret->slno . '-REV';
            $date  = $ret->return_date instanceof \Carbon\Carbon ? $ret->return_date->format('Y-m-d') : $ret->return_date;
            $this->daybookService->insertMaterialIssueDaybookEntries(
                $slno, $ret->net_amount, $date, $ret->branch_id,
                "Purchase Return {$ret->slno} - {$ret->supplier_name}"
            );
        });

        return redirect()->route('purchase-returns-new.show', $ret)->with('success', 'Return approved and daybook entries reversed.');
    }
}
