<?php
namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->from ?? date('Y-04-01'); // Default: start of financial year
        $toDate = $request->to ?? today()->toDateString();

        // Opening balances before fromDate
        $obMovements = Daybook::where('tdate', '<', $fromDate)
            ->selectRaw('account_id, SUM(amount) as ob_movement')
            ->groupBy('account_id')
            ->pluck('ob_movement', 'account_id');

        // Period movements
        $periodMovements = Daybook::whereBetween('tdate', [$fromDate, $toDate])
            ->selectRaw('account_id, SUM(amount) as period_movement')
            ->groupBy('account_id')
            ->pluck('period_movement', 'account_id');

        $accounts = ChartOfAccount::where('status', 1)
            ->orderBy('account_group')->orderBy('code')->get();

        $rows = [];
        $totalDr = 0;
        $totalCr = 0;

        foreach ($accounts as $acc) {
            $obBase = $acc->ob_type === 'dr' ? -abs($acc->opening_balance) : abs($acc->opening_balance);
            $openingBal = $obBase + ($obMovements[$acc->id] ?? 0);
            $periodMove = $periodMovements[$acc->id] ?? 0;
            $closingBal = $openingBal + $periodMove;

            if ($closingBal == 0 && $openingBal == 0 && $periodMove == 0) continue;

            // closingBal < 0 means Debit (we debit = negative in our convention), > 0 means Credit
            $drBalance = $closingBal < 0 ? abs($closingBal) : 0;
            $crBalance = $closingBal > 0 ? $closingBal : 0;

            $rows[] = [
                'account'      => $acc,
                'opening'      => $openingBal,
                'period_dr'    => $periodMove < 0 ? abs($periodMove) : 0,
                'period_cr'    => $periodMove > 0 ? $periodMove : 0,
                'closing_dr'   => $drBalance,
                'closing_cr'   => $crBalance,
            ];

            $totalDr += $drBalance;
            $totalCr += $crBalance;
        }

        $balanced = abs($totalDr - $totalCr) < 0.01;

        return view('finance.trial-balance.index', compact('rows','totalDr','totalCr','balanced','fromDate','toDate'));
    }
}
