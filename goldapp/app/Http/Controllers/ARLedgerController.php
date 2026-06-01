<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\ArLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ARLedgerController extends Controller
{
    public function index(Request $request)
    {
        $customerId = $request->customer_id;
        $asOf       = $request->as_of ?? now()->toDateString();

        $customers = Account::where('atype', 'CUSTOMER')->orderBy('name')->get();

        // Build ageing report
        $query = ArLedger::selectRaw('
            customer_id,
            SUM(debit) as total_debit,
            SUM(credit) as total_credit,
            SUM(debit) - SUM(credit) as outstanding,
            SUM(CASE WHEN DATEDIFF(?, tdate) BETWEEN 0  AND 30  THEN debit-credit ELSE 0 END) as bucket_0_30,
            SUM(CASE WHEN DATEDIFF(?, tdate) BETWEEN 31 AND 60  THEN debit-credit ELSE 0 END) as bucket_31_60,
            SUM(CASE WHEN DATEDIFF(?, tdate) BETWEEN 61 AND 90  THEN debit-credit ELSE 0 END) as bucket_61_90,
            SUM(CASE WHEN DATEDIFF(?, tdate) > 90              THEN debit-credit ELSE 0 END) as bucket_90_plus
        ', [$asOf, $asOf, $asOf, $asOf])
        ->where('tdate', '<=', $asOf)
        ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
        ->groupBy('customer_id');

        $ageing = $query->get();

        // Map customer names
        $customerMap = $customers->keyBy('id');

        // Statement for selected customer
        $statement = [];
        if ($customerId) {
            $statement = ArLedger::where('customer_id', $customerId)
                ->where('tdate', '<=', $asOf)
                ->orderBy('tdate')
                ->get();
        }

        return view('sales.ar-ledger.index', compact('ageing', 'customers', 'customerMap', 'statement', 'customerId', 'asOf'));
    }
}
