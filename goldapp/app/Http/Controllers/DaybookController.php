<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Daybook;
use App\Models\DaybookPart;
use App\Services\DaybookService;
use Illuminate\Http\Request;

class DaybookController extends Controller
{
    protected DaybookService $daybookService;

    public function __construct(DaybookService $daybookService)
    {
        $this->daybookService = $daybookService;
    }

    public function index(Request $request)
    {
        $from  = $request->from ?? date('Y-m-d');
        $to    = $request->to   ?? date('Y-m-d');
        $vtype = $request->vtype;

        $parts = DaybookPart::whereBetween('tdate', [$from, $to])
            ->when($vtype, fn($q) => $q->where('vtype', $vtype))
            ->orderBy('tdate')->orderBy('slno')
            ->get();

        $slnos = $parts->pluck('slno');
        $sums  = Daybook::whereIn('slno', $slnos)
            ->selectRaw('slno, SUM(CASE WHEN amount < 0 THEN ABS(amount) ELSE 0 END) as total_debit, SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) as total_credit')
            ->groupBy('slno')
            ->get()
            ->keyBy('slno');

        return view('accounts.daybook', compact('parts', 'sums', 'from', 'to', 'vtype'));
    }

    public function ledger(Request $request)
    {
        $accounts   = Account::orderBy('name')->get();
        $accountId  = $request->account_id;
        $from       = $request->from ?? date('Y-m-01');
        $to         = $request->to   ?? date('Y-m-d');
        $ledgerData = null;

        if ($accountId) {
            $ledgerData = $this->daybookService->getLedger((int)$accountId, $from, $to);
        }

        return view('accounts.ledger', compact('accounts', 'accountId', 'from', 'to', 'ledgerData'));
    }

    public function trialBalance(Request $request)
    {
        $asOfDate = $request->as_of ?? date('Y-m-d');

        $accounts = Account::where('status', 1)->orderBy('name')->get();

        $rows = [];
        foreach ($accounts as $acc) {
            $sum = Daybook::where('account_id', $acc->id)
                ->where('tdate', '<=', $asOfDate)
                ->sum('amount');

            $openingBal = (float)$acc->opening_balance;
            if ($acc->ob_type === 'cr') $openingBal = -$openingBal;

            $balance = $openingBal + (float)$sum;

            if (abs($balance) > 0.001) {
                $rows[] = [
                    'account' => $acc,
                    'balance' => $balance,
                    'debit'   => $balance < 0 ? abs($balance) : 0,
                    'credit'  => $balance > 0 ? $balance : 0,
                ];
            }
        }

        $totalDebit  = array_sum(array_column($rows, 'debit'));
        $totalCredit = array_sum(array_column($rows, 'credit'));

        return view('accounts.trial_balance', compact('rows', 'totalDebit', 'totalCredit', 'asOfDate'));
    }
}
