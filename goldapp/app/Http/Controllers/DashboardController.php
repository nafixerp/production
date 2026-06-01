<?php
namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Receipt;
use App\Models\Payment;
use App\Models\Account;
use App\Models\Daybook;

class DashboardController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');

        $todaySalesCount    = SalesInvoice::whereDate('invoice_date', $today)->where('status', 1)->count();
        $todaySalesAmount   = SalesInvoice::whereDate('invoice_date', $today)->where('status', 1)->sum('net_amount');
        $todayPurchaseCount = PurchaseInvoice::whereDate('invoice_date', $today)->where('status', 1)->count();
        $todayPurchaseAmount= PurchaseInvoice::whereDate('invoice_date', $today)->where('status', 1)->sum('net_amount');
        $todayReceiptsAmount= Receipt::whereDate('vch_date', $today)->where('status', 1)->sum('amount');
        $todayPaymentsAmount= Payment::whereDate('vch_date', $today)->where('status', 1)->sum('amount');

        // Cash balance from daybook
        $cashAcc     = Account::where('code', 'CASH')->first();
        $cashBalance = 0;
        if ($cashAcc) {
            $ob = (float)$cashAcc->opening_balance;
            if ($cashAcc->ob_type === 'cr') $ob = -$ob;
            $cashBalance = $ob + (float)Daybook::where('account_id', $cashAcc->id)->sum('amount');
        }

        $recentSales    = SalesInvoice::where('status', 1)->orderBy('created_at', 'desc')->limit(5)->get();
        $recentPurchases= PurchaseInvoice::where('status', 1)->orderBy('created_at', 'desc')->limit(5)->get();

        return view('dashboard.index', compact(
            'todaySalesCount', 'todaySalesAmount',
            'todayPurchaseCount', 'todayPurchaseAmount',
            'todayReceiptsAmount', 'todayPaymentsAmount',
            'cashBalance', 'recentSales', 'recentPurchases'
        ));
    }
}
