<?php
namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->from ?? date('Y-04-01');
        $toDate   = $request->to ?? today()->toDateString();
        $costCentre = $request->cost_centre;

        $baseQuery = function($group) use ($fromDate, $toDate, $costCentre) {
            return DB::table('daybook')
                ->join('chart_of_accounts as coa','daybook.account_id','=','coa.id')
                ->where('coa.account_group', $group)
                ->whereBetween('daybook.tdate', [$fromDate, $toDate])
                ->when($costCentre, function($q) use ($costCentre) {
                    // cost_centre not in daybook, but in JV lines — join if possible
                });
        };

        // Income: amount > 0 = credit = income
        $incomeAccounts = DB::table('daybook')
            ->join('chart_of_accounts as coa','daybook.account_id','=','coa.id')
            ->where('coa.account_group', 'income')
            ->whereBetween('daybook.tdate', [$fromDate, $toDate])
            ->selectRaw('coa.id, coa.code, coa.name, coa.account_type, SUM(daybook.amount) as total')
            ->groupBy('coa.id','coa.code','coa.name','coa.account_type')
            ->orderBy('coa.account_type')->orderBy('coa.name')
            ->get();

        // Expense: amount < 0 = debit = expense
        $expenseAccounts = DB::table('daybook')
            ->join('chart_of_accounts as coa','daybook.account_id','=','coa.id')
            ->where('coa.account_group', 'expense')
            ->whereBetween('daybook.tdate', [$fromDate, $toDate])
            ->selectRaw('coa.id, coa.code, coa.name, coa.account_type, SUM(daybook.amount) as total')
            ->groupBy('coa.id','coa.code','coa.name','coa.account_type')
            ->orderBy('coa.account_type')->orderBy('coa.name')
            ->get();

        // Categorize income: Sales revenue vs Other income
        $salesAccounts = $incomeAccounts->where('account_type', 'sales');
        $otherIncomeAccounts = $incomeAccounts->whereNotIn('account_type', ['sales']);
        $totalSales = $salesAccounts->sum('total'); // positive = credit

        // COGS: expense accounts with type 'cogs' or 'direct'
        $cogsAccounts = $expenseAccounts->filter(fn($a) => in_array($a->account_type, ['cogs','direct','raw_material']));
        $otherExpenseAccounts = $expenseAccounts->filter(fn($a) => !in_array($a->account_type, ['cogs','direct','raw_material']));

        $totalCOGS = abs($cogsAccounts->sum('total'));
        $totalOtherIncome = $otherIncomeAccounts->sum('total');
        $totalOtherExpense = abs($otherExpenseAccounts->sum('total'));

        $grossProfit = $totalSales - $totalCOGS;
        $netProfit = $grossProfit + $totalOtherIncome - $totalOtherExpense;

        return view('finance.profit-loss.index', compact(
            'salesAccounts','cogsAccounts','otherIncomeAccounts','otherExpenseAccounts',
            'totalSales','totalCOGS','totalOtherIncome','totalOtherExpense',
            'grossProfit','netProfit','fromDate','toDate','costCentre'
        ));
    }
}
