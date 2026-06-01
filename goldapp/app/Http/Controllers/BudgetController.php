<?php
namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\ChartOfAccount;
use App\Models\CostCentre;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $fy = $request->fy ?? $this->currentFY();
        $costCentreId = $request->cost_centre_id;
        $month = $request->month;

        $q = Budget::with(['account','costCentre'])
            ->where('financial_year', $fy);
        if ($costCentreId) $q->where('cost_centre_id', $costCentreId);
        if ($month) $q->where('month', $month);

        $budgets = $q->orderBy('month')->get();

        // Update actual amounts from daybook
        foreach ($budgets as $budget) {
            $monthStart = date('Y-m-01', strtotime($this->fyStartYear($fy) . '-' . str_pad($budget->month, 2, '0', STR_PAD_LEFT) . '-01'));
            $monthEnd   = date('Y-m-t', strtotime($monthStart));
            $actual = abs(Daybook::where('account_id', $budget->account_id)
                ->whereBetween('tdate', [$monthStart, $monthEnd])
                ->sum('amount'));
            $variance = $budget->budgeted_amount - $actual;
            $budget->update(['actual_amount' => $actual, 'variance' => $variance]);
        }
        $budgets = $budgets->fresh();

        $costCentres = CostCentre::where('status',1)->get();
        $months = range(1,12);

        return view('finance.budgets.index', compact('budgets','fy','costCentreId','month','costCentres','months'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::where('status',1)
            ->whereIn('account_group',['income','expense'])->orderBy('name')->get();
        $costCentres = CostCentre::where('status',1)->get();
        return view('finance.budgets.form', compact('accounts','costCentres'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cost_centre_id'  => 'nullable|integer',
            'account_id'      => 'required|integer',
            'financial_year'  => 'required|string|max:10',
            'month'           => 'required|integer|min:1|max:12',
            'budgeted_amount' => 'required|numeric|min:0',
        ]);
        $account = ChartOfAccount::findOrFail($data['account_id']);
        $data['account_name'] = $account->name;
        $data['actual_amount'] = 0;
        $data['variance'] = $data['budgeted_amount'];
        Budget::create($data);
        return redirect()->route('budgets.index')->with('success', 'Budget entry created.');
    }

    public function show($id)
    {
        $budget = Budget::with(['account','costCentre'])->findOrFail($id);
        return view('finance.budgets.show', compact('budget'));
    }

    public function edit($id)
    {
        $budget = Budget::findOrFail($id);
        $accounts = ChartOfAccount::where('status',1)
            ->whereIn('account_group',['income','expense'])->orderBy('name')->get();
        $costCentres = CostCentre::where('status',1)->get();
        return view('finance.budgets.form', compact('budget','accounts','costCentres'));
    }

    public function update(Request $request, $id)
    {
        $budget = Budget::findOrFail($id);
        $data = $request->validate([
            'cost_centre_id'  => 'nullable|integer',
            'account_id'      => 'required|integer',
            'financial_year'  => 'required|string|max:10',
            'month'           => 'required|integer|min:1|max:12',
            'budgeted_amount' => 'required|numeric|min:0',
        ]);
        $account = ChartOfAccount::findOrFail($data['account_id']);
        $data['account_name'] = $account->name;
        $data['variance'] = $data['budgeted_amount'] - $budget->actual_amount;
        $budget->update($data);
        return redirect()->route('budgets.index')->with('success', 'Budget updated.');
    }

    public function destroy($id)
    {
        Budget::findOrFail($id)->delete();
        return redirect()->route('budgets.index')->with('success', 'Budget entry deleted.');
    }

    private function currentFY(): string
    {
        $year = date('n') >= 4 ? date('Y') : date('Y') - 1;
        return $year . '-' . ($year + 1);
    }

    private function fyStartYear(string $fy): int
    {
        return (int)explode('-', $fy)[0];
    }
}
