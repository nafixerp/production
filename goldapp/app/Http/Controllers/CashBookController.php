<?php
namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashBookController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->from ?? today()->toDateString();
        $toDate   = $request->to ?? today()->toDateString();

        // Find cash accounts (account_type = 'cash' or name contains 'cash')
        $cashAccounts = ChartOfAccount::where('status',1)
            ->where('is_bank_account', 0)
            ->where(function($q) {
                $q->where('account_type', 'cash')
                  ->orWhere('name', 'like', '%Cash%');
            })->get();

        $cashAccountIds = $cashAccounts->pluck('id')->toArray();

        $openingBalance = 0;
        foreach ($cashAccounts as $acc) {
            $obBase = $acc->ob_type === 'dr' ? -abs($acc->opening_balance) : abs($acc->opening_balance);
            $prior = Daybook::where('account_id', $acc->id)->where('tdate','<',$fromDate)->sum('amount');
            $openingBalance += ($obBase + $prior);
        }

        $entries = Daybook::whereIn('account_id', $cashAccountIds)
            ->whereBetween('tdate', [$fromDate, $toDate])
            ->orderBy('tdate','asc')->orderBy('id','asc')->get();

        $totalReceipts = $entries->where('amount', '>', 0)->sum('amount'); // CR
        $totalPayments = $entries->where('amount', '<', 0)->sum(fn($e) => abs($e->amount)); // DR
        $closingBalance = $openingBalance + $entries->sum('amount');

        // Build rows with running balance
        $rows = [];
        $running = $openingBalance;
        foreach ($entries as $e) {
            $running += $e->amount;
            $rows[] = [
                'entry'   => $e,
                'dr'      => $e->amount < 0 ? abs($e->amount) : 0,
                'cr'      => $e->amount > 0 ? $e->amount : 0,
                'balance' => $running,
            ];
        }

        return view('finance.cash-book.index', compact(
            'rows','openingBalance','closingBalance','totalReceipts','totalPayments',
            'fromDate','toDate','cashAccounts'
        ));
    }
}
