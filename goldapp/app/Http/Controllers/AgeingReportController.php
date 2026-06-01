<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgeingReportController extends Controller {
    public function index(Request $request) {
        $type       = $request->get('type', 'ar'); // ar | ap
        $as_of      = $request->get('as_of', date('Y-m-d'));
        $party_name = $request->get('party_name');

        if ($type === 'ar') {
            $ledgerTable  = 'ar_ledger';
            $partyCol     = 'customer_name';
            $partyIdCol   = 'customer_id';
            $amountCol    = 'debit'; // amount owed to us
        } else {
            $ledgerTable  = 'ap_ledger';
            $partyCol     = 'vendor_name';
            $partyIdCol   = 'vendor_id';
            $amountCol    = 'credit'; // amount we owe
        }

        $q = DB::table($ledgerTable)
            ->where($amountCol, '>', 0)
            ->where('tdate', '<=', $as_of);

        if ($party_name) $q->where($partyCol, 'like', "%{$party_name}%");

        $raw_rows = $q->selectRaw(
            "$partyCol, $amountCol as outstanding,
            DATEDIFF('$as_of', tdate) as age_days,
            tdate as invoice_date,
            ref_no"
        )->get();

        // Group into ageing buckets
        $buckets = [
            'current'   => ['label' => 'Current (0-30)', 'rows' => [], 'total' => 0],
            'b31_60'    => ['label' => '31-60 Days',     'rows' => [], 'total' => 0],
            'b61_90'    => ['label' => '61-90 Days',     'rows' => [], 'total' => 0],
            'b91_120'   => ['label' => '91-120 Days',    'rows' => [], 'total' => 0],
            'over120'   => ['label' => 'Over 120 Days',  'rows' => [], 'total' => 0],
        ];

        $party_summary = [];

        foreach ($raw_rows as $row) {
            $age = (int) $row->age_days;
            $key = match(true) {
                $age <= 30  => 'current',
                $age <= 60  => 'b31_60',
                $age <= 90  => 'b61_90',
                $age <= 120 => 'b91_120',
                default     => 'over120',
            };

            $buckets[$key]['rows'][]  = $row;
            $buckets[$key]['total']  += $row->outstanding;

            $pname = $row->$partyCol ?? 'Unknown';
            if (!isset($party_summary[$pname])) {
                $party_summary[$pname] = ['current'=>0,'b31_60'=>0,'b61_90'=>0,'b91_120'=>0,'over120'=>0,'total'=>0];
            }
            $party_summary[$pname][$key] += $row->outstanding;
            $party_summary[$pname]['total'] += $row->outstanding;
        }

        arsort($party_summary);

        $grand_total = array_sum(array_column($buckets, 'total'));

        return view('reports.ageing', compact(
            'type', 'as_of', 'party_name', 'buckets',
            'party_summary', 'grand_total'
        ));
    }
}
