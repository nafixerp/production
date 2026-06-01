<?php
namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ApLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class APLedgerController extends Controller
{
    public function index(Request $request)
    {
        $supplierId = $request->supplier_id;
        $asOf       = $request->as_of ?? now()->format('Y-m-d');

        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();

        // Build AP ageing: group invoices by age buckets 0-30, 31-60, 61-90, 90+
        $query = DB::table('purchase_invoices as pi')
            ->select(
                'pi.supplier_id',
                'pi.supplier_name',
                DB::raw('SUM(pi.balance_amount) as total_outstanding'),
                DB::raw("SUM(CASE WHEN DATEDIFF('$asOf', pi.invoice_date) BETWEEN 0 AND 30 THEN pi.balance_amount ELSE 0 END) as bucket_0_30"),
                DB::raw("SUM(CASE WHEN DATEDIFF('$asOf', pi.invoice_date) BETWEEN 31 AND 60 THEN pi.balance_amount ELSE 0 END) as bucket_31_60"),
                DB::raw("SUM(CASE WHEN DATEDIFF('$asOf', pi.invoice_date) BETWEEN 61 AND 90 THEN pi.balance_amount ELSE 0 END) as bucket_61_90"),
                DB::raw("SUM(CASE WHEN DATEDIFF('$asOf', pi.invoice_date) > 90 THEN pi.balance_amount ELSE 0 END) as bucket_90plus")
            )
            ->whereIn('pi.status', ['posted', 'partial'])
            ->where('pi.balance_amount', '>', 0)
            ->groupBy('pi.supplier_id', 'pi.supplier_name');

        if ($supplierId) {
            $query->where('pi.supplier_id', $supplierId);
        }

        $ageing = $query->orderBy('pi.supplier_name')->get();

        // Supplier-wise transaction ledger
        $ledger = [];
        if ($supplierId) {
            $ledger = ApLedger::where('supplier_id', $supplierId)
                ->where('tdate', '<=', $asOf)
                ->orderBy('tdate')->orderBy('id')
                ->get();
        }

        // Totals
        $totals = [
            'total'       => $ageing->sum('total_outstanding'),
            'bucket_0_30' => $ageing->sum('bucket_0_30'),
            'bucket_31_60'=> $ageing->sum('bucket_31_60'),
            'bucket_61_90'=> $ageing->sum('bucket_61_90'),
            'bucket_90plus'=> $ageing->sum('bucket_90plus'),
        ];

        return view('procurement.ap-ledger.index', compact('ageing', 'suppliers', 'supplierId', 'asOf', 'totals', 'ledger'));
    }
}
