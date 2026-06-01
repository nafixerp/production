<?php
namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Daybook;
use Illuminate\Http\Request;

class BankBookController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->from ?? today()->toDateString();
        $toDate   = $request->to ?? today()->toDateString();
        $bankAccountId = $request->bank_account_id;

        $bankAccounts = ChartOfAccount::where('status',1)->where('is_bank_account',1)->get();

        $selectedAccounts = $bankAccountId
            ? $bankAccounts->where('id', $bankAccountId)
            : $bankAccounts;
        $ids = $selectedAccounts->pluck('id')->toArray();

        $openingBalance = 0;
        foreach ($selectedAccounts as $acc) {
            $obBase = $acc->ob_type === 'dr' ? -abs($acc->opening_balance) : abs($acc->opening_balance);
            $prior = Daybook::where('account_id', $acc->id)->where('tdate','<',$fromDate)->sum('amount');
            $openingBalance += ($obBase + $prior);
        }

        $entries = Daybook::whereIn('account_id', $ids)
            ->whereBetween('tdate', [$fromDate, $toDate])
            ->orderBy('tdate','asc')->orderBy('id','asc')->get();

        $totalReceipts = $entries->where('amount', '>', 0)->sum('amount');
        $totalPayments = $entries->where('amount', '<', 0)->sum(fn($e) => abs($e->amount));
        $closingBalance = $openingBalance + $entries->sum('amount');

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

        return view('finance.bank-book.index', compact(
            'rows','openingBalance','closingBalance','totalReceipts','totalPayments',
            'fromDate','toDate','bankAccounts','bankAccountId'
        ));
    }
}
