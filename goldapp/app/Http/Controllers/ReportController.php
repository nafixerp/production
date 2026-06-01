<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function ledger(Request $request)
    {
        $accounts   = Account::orderBy('name')->get();
        $accountId  = $request->account_id;
        $from       = $request->from ?? date('Y-m-01');
        $to         = $request->to   ?? date('Y-m-d');
        $account    = null;
        $entries    = collect();
        $openingBal = 0;
        $runningBal = 0;

        if ($accountId) {
            $account = Account::find($accountId);
            if ($account) {
                // Opening balance from account
                $openingBal = $account->ob_type === 'dr'
                    ? -$account->opening_balance
                    :  $account->opening_balance;

                // Prior period daybook sum
                $priorSum = Daybook::where('account_id', $accountId)
                    ->where('tdate', '<', $from)
                    ->sum('amount');

                $openingBal = $openingBal + (float)$priorSum;

                $entries = Daybook::where('account_id', $accountId)
                    ->whereBetween('tdate', [$from, $to])
                    ->orderBy('tdate')->orderBy('id')
                    ->get();
            }
        }

        return view('reports.ledger', compact('accounts','accountId','from','to','account','entries','openingBal'));
    }

    public function trialBalance(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');

        $accounts = Account::all();
        $results  = [];

        foreach ($accounts as $acc) {
            $obAmount = $acc->ob_type === 'dr' ? -$acc->opening_balance : $acc->opening_balance;
            $txSum    = (float) Daybook::where('account_id', $acc->id)
                ->where('tdate', '<=', $date)
                ->sum('amount');
            $balance  = $obAmount + $txSum;
            if ($balance != 0) {
                $results[] = [
                    'code'    => $acc->code,
                    'name'    => $acc->name,
                    'debit'   => $balance < 0 ? abs($balance) : 0,
                    'credit'  => $balance > 0 ? $balance : 0,
                ];
            }
        }

        $totalDr = array_sum(array_column($results, 'debit'));
        $totalCr = array_sum(array_column($results, 'credit'));

        return view('reports.trial_balance', compact('results','totalDr','totalCr','date'));
    }
}
