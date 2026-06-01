<?php
namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->from ?? date('Y-04-01');
        $toDate   = $request->to ?? today()->toDateString();
        $accountId = $request->account_id;

        $accounts = ChartOfAccount::where('status',1)->orderBy('name')->get();

        $entries = collect();
        $account = null;
        $openingBalance = 0;
        $runningBalance = 0;

        if ($accountId) {
            $account = ChartOfAccount::find($accountId);
            // Opening balance = OB + movements before fromDate
            $obBase = $account->ob_type === 'dr' ? -abs($account->opening_balance) : abs($account->opening_balance);
            $priorMovements = Daybook::where('account_id', $accountId)
                ->where('tdate', '<', $fromDate)->sum('amount');
            $openingBalance = $obBase + $priorMovements;

            // Period entries
            $rawEntries = Daybook::where('account_id', $accountId)
                ->whereBetween('tdate', [$fromDate, $toDate])
                ->orderBy('tdate','asc')->orderBy('id','asc')->get();

            $runningBalance = $openingBalance;
            foreach ($rawEntries as $entry) {
                $runningBalance += $entry->amount;
                $entries->push([
                    'entry'   => $entry,
                    'dr'      => $entry->amount < 0 ? abs($entry->amount) : 0,
                    'cr'      => $entry->amount > 0 ? $entry->amount : 0,
                    'balance' => $runningBalance,
                ]);
            }
        }

        return view('finance.general-ledger.index', compact(
            'accounts','account','entries','openingBalance','runningBalance','fromDate','toDate','accountId'
        ));
    }
}
