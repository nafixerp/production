<?php
namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalanceSheetController extends Controller
{
    public function index(Request $request)
    {
        $asOf = $request->as_of ?? today()->toDateString();

        // Compute balance for each account up to asOf date
        // Balance = opening_balance (signed) + sum of daybook movements up to asOf
        $accounts = ChartOfAccount::where('status', 1)->get();

        $movements = Daybook::where('tdate', '<=', $asOf)
            ->selectRaw('account_id, SUM(amount) as movement')
            ->groupBy('account_id')
            ->pluck('movement', 'account_id');

        $assets = [];
        $liabilities = [];
        $equity = [];
        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        foreach ($accounts as $acc) {
            $ob = $acc->ob_type === 'dr' ? -abs($acc->opening_balance) : abs($acc->opening_balance);
            $balance = $ob + ($movements[$acc->id] ?? 0);

            if ($acc->account_group === 'asset') {
                // Assets have debit (negative) balances in our sign convention
                $debitBalance = -$balance;
                if ($debitBalance != 0) {
                    $assets[] = ['account' => $acc, 'balance' => $debitBalance];
                    $totalAssets += $debitBalance;
                }
            } elseif ($acc->account_group === 'liability') {
                // Liabilities have credit (positive) balances
                $creditBalance = $balance;
                if ($creditBalance != 0) {
                    $liabilities[] = ['account' => $acc, 'balance' => $creditBalance];
                    $totalLiabilities += $creditBalance;
                }
            } elseif ($acc->account_group === 'equity') {
                $creditBalance = $balance;
                if ($creditBalance != 0) {
                    $equity[] = ['account' => $acc, 'balance' => $creditBalance];
                    $totalEquity += $creditBalance;
                }
            }
        }

        // Net P&L for the period adds to equity
        $incomeMovements = DB::table('daybook')
            ->join('chart_of_accounts','daybook.account_id','=','chart_of_accounts.id')
            ->where('chart_of_accounts.account_group','income')
            ->where('daybook.tdate','<=',$asOf)
            ->sum('daybook.amount');
        $expenseMovements = DB::table('daybook')
            ->join('chart_of_accounts','daybook.account_id','=','chart_of_accounts.id')
            ->where('chart_of_accounts.account_group','expense')
            ->where('daybook.tdate','<=',$asOf)
            ->sum('daybook.amount');
        $netPL = $incomeMovements + $expenseMovements; // income positive, expense negative

        $totalEquity += $netPL;
        $totalLiabilitiesEquity = $totalLiabilities + $totalEquity;
        $balanced = abs($totalAssets - $totalLiabilitiesEquity) < 0.01;

        return view('finance.balance-sheet.index', compact(
            'assets','liabilities','equity','totalAssets','totalLiabilities','totalEquity',
            'totalLiabilitiesEquity','netPL','balanced','asOf'
        ));
    }
}
