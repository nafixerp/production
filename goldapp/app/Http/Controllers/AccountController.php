<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountGroup;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->q;
        $accounts = Account::with('group')
            ->when($q, fn($query) => $query->where('name','like',"%$q%")->orWhere('code','like',"%$q%"))
            ->orderBy('code')
            ->paginate(20)->withQueryString();
        return view('accounts.index', compact('accounts','q'));
    }

    public function create()
    {
        $groups = AccountGroup::orderBy('name')->get();
        return view('accounts.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:account,code',
            'name' => 'required',
        ]);
        Account::create($request->only(['code','name','group_id','atype','opening_balance','ob_type','phone','address','gst','status']));
        return redirect()->route('accounts.index')->with('success','Account created successfully.');
    }

    public function edit(Account $account)
    {
        $groups = AccountGroup::orderBy('name')->get();
        return view('accounts.edit', compact('account','groups'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'code' => 'required|unique:account,code,'.$account->id,
            'name' => 'required',
        ]);
        $account->update($request->only(['code','name','group_id','atype','opening_balance','ob_type','phone','address','gst','status']));
        return redirect()->route('accounts.index')->with('success','Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('accounts.index')->with('success','Account deleted.');
    }

    // AJAX search for accounts
    public function search(Request $request)
    {
        $q = $request->q;
        $type = $request->type; // customer, supplier, bank, all
        $accounts = Account::when($q, fn($query) => $query->where('name','like',"%$q%")->orWhere('code','like',"%$q%"))
            ->when($type === 'customer', fn($query) => $query->where('atype','customer'))
            ->when($type === 'supplier', fn($query) => $query->where('atype','supplier'))
            ->when($type === 'bank', fn($query) => $query->where('atype','bank'))
            ->limit(20)
            ->get(['id','code','name','atype']);
        return response()->json($accounts);
    }
}
