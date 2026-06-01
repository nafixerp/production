<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Salesm;
use App\Models\Salesd;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesBillController extends Controller
{
    protected DaybookService $daybookService;

    public function __construct(DaybookService $daybookService)
    {
        $this->daybookService = $daybookService;
    }

    public function index(Request $request)
    {
        $q      = $request->q;
        $from   = $request->from;
        $to     = $request->to;
        $bills  = Salesm::when($q, fn($qry) => $qry->where('customer_name','like',"%$q%")->orWhere('billno','like',"%$q%"))
            ->when($from, fn($qry) => $qry->where('billdate','>=',$from))
            ->when($to,   fn($qry) => $qry->where('billdate','<=',$to))
            ->orderBy('billdate','desc')->orderBy('id','desc')
            ->paginate(20)->withQueryString();
        return view('sales.index', compact('bills','q','from','to'));
    }

    public function create()
    {
        $customers = Account::where('atype','customer')->orderBy('name')->get();
        $bankAccounts = Account::where('atype','bank')->orderBy('name')->get();
        $nextSlno = $this->daybookService->nextSlno('SB', 'salesm');
        $nextBillno = $nextSlno;
        return view('sales.create', compact('customers','bankAccounts','nextSlno','nextBillno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:account,id',
            'billdate'    => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            $slno = $this->daybookService->nextSlno('SB', 'salesm');

            $customer = Account::find($request->customer_id);

            // Calculate totals
            $items       = $request->input('items', []);
            $grossAmount = 0;
            $totalHmc    = 0;
            foreach ($items as $item) {
                $grossAmount += (float)($item['amount'] ?? 0);
                $totalHmc    += (float)($item['hmc'] ?? 0);
            }

            $discount   = (float)$request->discount ?? 0;
            $sgst       = (float)$request->sgst ?? 0;
            $cgst       = (float)$request->cgst ?? 0;
            $igst       = (float)$request->igst ?? 0;
            $hmc        = (float)$request->hmc ?? $totalHmc;
            $tcs        = (float)$request->tcs ?? 0;
            $exchange   = (float)$request->exchange_amount ?? 0;
            $sreturn    = (float)$request->sales_return ?? 0;
            $received   = (float)$request->received_amount ?? 0;

            $netAmount  = $grossAmount - $discount + $sgst + $cgst + $igst + $hmc + $tcs - $exchange - $sreturn;
            $roundOff   = round($netAmount) - $netAmount;
            $netAmount  = round($netAmount);

            $salesm = Salesm::create([
                'slno'            => $slno,
                'billno'          => $slno,
                'billdate'        => $request->billdate,
                'customer_id'     => $request->customer_id,
                'customer_name'   => $customer->name,
                'gross_amount'    => $grossAmount,
                'discount'        => $discount,
                'sgst'            => $sgst,
                'cgst'            => $cgst,
                'igst'            => $igst,
                'hmc'             => $hmc,
                'tcs'             => $tcs,
                'round_off'       => $roundOff,
                'net_amount'      => $netAmount,
                'received_amount' => $received,
                'payment_mode'    => $request->payment_mode ?? 'credit',
                'bank_account_id' => $request->bank_account_id ?? null,
                'exchange_amount' => $exchange,
                'sales_return'    => $sreturn,
                'narration'       => $request->narration,
                'status'          => 1,
                'created_by'      => Auth::id(),
            ]);

            // Save line items
            foreach ($items as $item) {
                if (empty($item['item_name'])) continue;
                Salesd::create([
                    'salesm_id' => $salesm->id,
                    'slno'      => $slno,
                    'item_code' => $item['item_code'] ?? '',
                    'item_name' => $item['item_name'],
                    'purity'    => $item['purity'] ?? '',
                    'gross_wt'  => $item['gross_wt'] ?? 0,
                    'net_wt'    => $item['net_wt'] ?? 0,
                    'rate'      => $item['rate'] ?? 0,
                    'amount'    => $item['amount'] ?? 0,
                    'hmc'       => $item['hmc'] ?? 0,
                ]);
            }

            // Post to daybook
            $this->daybookService->insertSalesDaybookEntries($salesm);
        });

        return redirect()->route('sales.index')->with('success', 'Sales bill saved successfully.');
    }

    public function show(Salesm $salesm)
    {
        $salesm->load('details');
        return view('sales.show', compact('salesm'));
    }

    public function destroy(Salesm $salesm)
    {
        DB::transaction(function () use ($salesm) {
            \App\Models\Daybook::where('slno', $salesm->slno)->delete();
            \App\Models\Daybookpart::where('slno', $salesm->slno)->delete();
            $salesm->details()->delete();
            $salesm->delete();
        });
        return redirect()->route('sales.index')->with('success','Sales bill deleted.');
    }
}
