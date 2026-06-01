<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Purchasem;
use App\Models\Purchasedd;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseBillController extends Controller
{
    protected DaybookService $daybookService;

    public function __construct(DaybookService $daybookService)
    {
        $this->daybookService = $daybookService;
    }

    public function index(Request $request)
    {
        $q     = $request->q;
        $from  = $request->from;
        $to    = $request->to;
        $bills = Purchasem::when($q, fn($qry) => $qry->where('supplier_name','like',"%$q%")->orWhere('docno','like',"%$q%"))
            ->when($from, fn($qry) => $qry->where('billdate','>=',$from))
            ->when($to,   fn($qry) => $qry->where('billdate','<=',$to))
            ->orderBy('billdate','desc')->orderBy('id','desc')
            ->paginate(20)->withQueryString();
        return view('purchase.index', compact('bills','q','from','to'));
    }

    public function create()
    {
        $suppliers    = Account::where('atype','supplier')->orderBy('name')->get();
        $bankAccounts = Account::where('atype','bank')->orderBy('name')->get();
        $nextSlno     = $this->daybookService->nextSlno('PB', 'purchasem');
        return view('purchase.create', compact('suppliers','bankAccounts','nextSlno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:account,id',
            'billdate'    => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            $slno     = $this->daybookService->nextSlno('PB', 'purchasem');
            $supplier = Account::find($request->supplier_id);

            $items       = $request->input('items', []);
            $grossAmount = 0;
            $totalHmc    = 0;
            foreach ($items as $item) {
                $grossAmount += (float)($item['amount'] ?? 0);
                $totalHmc    += (float)($item['hmc'] ?? 0);
            }

            $discount  = (float)($request->discount ?? 0);
            $sgst      = (float)($request->sgst ?? 0);
            $cgst      = (float)($request->cgst ?? 0);
            $igst      = (float)($request->igst ?? 0);
            $hmc       = (float)($request->hmc ?? $totalHmc);
            $tcs       = (float)($request->tcs ?? 0);
            $exchange  = (float)($request->exchange_amount ?? 0);
            $paid      = (float)($request->paid_amount ?? 0);

            $netAmount = $grossAmount - $discount + $sgst + $cgst + $igst + $hmc + $tcs;
            $roundOff  = round($netAmount) - $netAmount;
            $netAmount = round($netAmount);

            $purchasem = Purchasem::create([
                'slno'             => $slno,
                'docno'            => $slno,
                'billdate'         => $request->billdate,
                'supplier_id'      => $request->supplier_id,
                'supplier_name'    => $supplier->name,
                'supplier_bill_no' => $request->supplier_bill_no ?? '',
                'gross_amount'     => $grossAmount,
                'discount'         => $discount,
                'sgst'             => $sgst,
                'cgst'             => $cgst,
                'igst'             => $igst,
                'hmc'              => $hmc,
                'tcs'              => $tcs,
                'round_off'        => $roundOff,
                'net_amount'       => $netAmount,
                'paid_amount'      => $paid,
                'payment_mode'     => $request->payment_mode ?? 'credit',
                'bank_account_id'  => $request->bank_account_id ?? null,
                'exchange_amount'  => $exchange,
                'narration'        => $request->narration,
                'status'           => 1,
                'created_by'       => Auth::id(),
            ]);

            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                Purchasedd::create([
                    'purchasem_id' => $purchasem->id,
                    'slno'         => $slno,
                    'item_code'    => $item['item_code'] ?? '',
                    'item_name'    => $item['item_name'],
                    'purity'       => $item['purity'] ?? '',
                    'gross_wt'     => $item['gross_wt'] ?? 0,
                    'net_wt'       => $item['net_wt'] ?? 0,
                    'rate'         => $item['rate'] ?? 0,
                    'amount'       => $item['amount'] ?? 0,
                    'hmc'          => $item['hmc'] ?? 0,
                ]);
            }

            $this->daybookService->insertPurchaseDaybookEntries($purchasem);
        });

        return redirect()->route('purchase.index')->with('success','Purchase bill saved successfully.');
    }

    public function show(Purchasem $purchasem)
    {
        $purchasem->load('details');
        return view('purchase.show', compact('purchasem'));
    }

    public function destroy(Purchasem $purchasem)
    {
        DB::transaction(function () use ($purchasem) {
            \App\Models\Daybook::where('slno', $purchasem->slno)->delete();
            \App\Models\Daybookpart::where('slno', $purchasem->slno)->delete();
            $purchasem->details()->delete();
            $purchasem->delete();
        });
        return redirect()->route('purchase.index')->with('success','Purchase bill deleted.');
    }
}
