<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfitAndLossController extends Controller
{
    public function index(Request $request)
    {
        return view('generic.report', ['title'=>'Profit & Loss','slug'=>'profit-loss']);
    }

    public function store(Request $request)
    {
        return redirect()->back()->with('success','Saved successfully.');
    }

    public function createBackup() {
        return redirect()->back()->with('success','Backup initiated.');
    }

    public function update(Request $request, $id=null) {
        return redirect()->back()->with('success','Settings saved.');
    }

    public function login(Request $request) {
        return response()->json(['status'=>'ok']);
    }

    public function stockCheck($item_id) {
        $stock = DB::table('stock_ledger')->where('item_id',$item_id)->sum('in_qty') - DB::table('stock_ledger')->where('item_id',$item_id)->sum('out_qty');
        return response()->json(['item_id'=>$item_id,'stock'=>$stock]);
    }

    public function dispatchList() {
        return response()->json(['dispatches'=>[]]);
    }
}
