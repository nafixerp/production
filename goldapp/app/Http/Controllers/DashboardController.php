<?php
namespace App\Http\Controllers;

use App\Models\Salesm;
use App\Models\Purchasem;
use App\Models\Receipt;
use App\Models\Payment;
use App\Models\Account;
use App\Models\Daybook;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');

        $todaySales     = Salesm::whereDate('billdate', $today)->sum('net_amount');
        $todayPurchases = Purchasem::whereDate('billdate', $today)->sum('net_amount');
        $todayReceipts  = Receipt::whereDate('vchdate', $today)->sum('amount');
        $todayPayments  = Payment::whereDate('vchdate', $today)->sum('amount');

        $totalSales     = Salesm::sum('net_amount');
        $totalPurchases = Purchasem::sum('net_amount');

        // Cash balance: sum of all daybook entries for CASH account
        $cashAcc = Account::where('code', 'CASH')->first();
        $cashBalance = $cashAcc
            ? -Daybook::where('account_id', $cashAcc->id)->sum('amount')
            : 0;

        $recentSales    = Salesm::orderBy('created_at','desc')->limit(5)->get();
        $recentPurchases = Purchasem::orderBy('created_at','desc')->limit(5)->get();

        return view('dashboard.index', compact(
            'todaySales','todayPurchases','todayReceipts','todayPayments',
            'totalSales','totalPurchases','cashBalance',
            'recentSales','recentPurchases'
        ));
    }
}
