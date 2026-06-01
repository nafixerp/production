<?php
namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->group;
        $q = ChartOfAccount::with('parent');
        if ($group) $q->where('account_group', $group);
        $accounts = $q->orderBy('account_group')->orderBy('code')->get();

        // Build hierarchy
        $tree = [];
        $parents = $accounts->whereNull('parent_id');
        foreach ($parents as $parent) {
            $tree[] = ['account' => $parent, 'children' => $accounts->where('parent_id', $parent->id)->values()];
        }
        // Add orphaned children under their group
        $allGroups = ['asset','liability','equity','income','expense','tax'];
        return view('finance.chart-of-accounts.index', compact('accounts','tree','allGroups','group'));
    }

    public function create()
    {
        $parents = ChartOfAccount::where('allow_direct_posting',0)->orWhereNull('parent_id')->orderBy('name')->get();
        $allAccounts = ChartOfAccount::orderBy('name')->get();
        return view('finance.chart-of-accounts.form', compact('parents','allAccounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'               => 'required|string|max:30|unique:chart_of_accounts,code',
            'name'               => 'required|string|max:180',
            'account_group'      => 'required|in:asset,liability,equity,income,expense,tax',
            'account_type'       => 'nullable|string|max:50',
            'parent_id'          => 'nullable|integer',
            'is_control_account' => 'nullable|integer',
            'currency'           => 'nullable|string|max:10',
            'opening_balance'    => 'nullable|numeric',
            'ob_type'            => 'nullable|in:dr,cr',
            'is_bank_account'    => 'nullable|integer',
            'bank_name'          => 'nullable|string|max:100',
            'bank_account_no'    => 'nullable|string|max:50',
            'ifsc'               => 'nullable|string|max:20',
            'allow_direct_posting'=> 'nullable|integer',
            'status'             => 'nullable|integer',
            'notes'              => 'nullable|string',
        ]);
        $data['created_by'] = auth()->id();
        $data['is_control_account'] = $request->is_control_account ? 1 : 0;
        $data['is_bank_account'] = $request->is_bank_account ? 1 : 0;
        $data['allow_direct_posting'] = $request->allow_direct_posting ? 1 : 0;
        $data['status'] = $request->status ?? 1;
        ChartOfAccount::create($data);
        return redirect()->route('chart-of-accounts.index')->with('success', 'Account created successfully.');
    }

    public function show($id)
    {
        $account = ChartOfAccount::with(['parent','children'])->findOrFail($id);
        // Get recent daybook entries
        $entries = Daybook::where('account_id', $id)->orderBy('tdate','desc')->limit(30)->get();
        $balance = $account->getBalance();
        return view('finance.chart-of-accounts.show', compact('account','entries','balance'));
    }

    public function edit($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        $allAccounts = ChartOfAccount::where('id','!=',$id)->orderBy('name')->get();
        return view('finance.chart-of-accounts.form', compact('account','allAccounts'));
    }

    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);
        $data = $request->validate([
            'code'               => "required|string|max:30|unique:chart_of_accounts,code,{$id}",
            'name'               => 'required|string|max:180',
            'account_group'      => 'required|in:asset,liability,equity,income,expense,tax',
            'account_type'       => 'nullable|string|max:50',
            'parent_id'          => 'nullable|integer',
            'is_control_account' => 'nullable|integer',
            'currency'           => 'nullable|string|max:10',
            'opening_balance'    => 'nullable|numeric',
            'ob_type'            => 'nullable|in:dr,cr',
            'is_bank_account'    => 'nullable|integer',
            'bank_name'          => 'nullable|string|max:100',
            'bank_account_no'    => 'nullable|string|max:50',
            'ifsc'               => 'nullable|string|max:20',
            'allow_direct_posting'=> 'nullable|integer',
            'status'             => 'nullable|integer',
            'notes'              => 'nullable|string',
        ]);
        $data['is_control_account'] = $request->is_control_account ? 1 : 0;
        $data['is_bank_account'] = $request->is_bank_account ? 1 : 0;
        $data['allow_direct_posting'] = $request->allow_direct_posting ? 1 : 0;
        $data['status'] = $request->status ?? 1;
        $account->update($data);
        return redirect()->route('chart-of-accounts.index')->with('success', 'Account updated.');
    }

    public function destroy($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        // Cannot delete if has daybook entries
        if (Daybook::where('account_id', $id)->exists()) {
            return back()->with('error', 'Cannot delete account with posted transactions.');
        }
        if ($account->children()->exists()) {
            return back()->with('error', 'Cannot delete account that has sub-accounts.');
        }
        $account->delete();
        return redirect()->route('chart-of-accounts.index')->with('success', 'Account deleted.');
    }
}
