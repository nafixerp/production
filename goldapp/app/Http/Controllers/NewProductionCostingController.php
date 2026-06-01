<?php
namespace App\Http\Controllers;

use App\Models\ProductionCostingNew;
use App\Models\ProductionOrderNew;
use Illuminate\Http\Request;

class NewProductionCostingController extends Controller
{
    public function index(Request $request)
    {
        $from     = $request->from ?? now()->startOfMonth()->format('Y-m-d');
        $to       = $request->to   ?? now()->format('Y-m-d');
        $fgId     = $request->fg_id;
        $search   = $request->q;

        $costings = ProductionCostingNew::with('productionOrder')
            ->when($fgId, fn($q) => $q->where('fg_id', $fgId))
            ->when($search, fn($q) => $q->where('fg_name', 'like', "%$search%"))
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->paginate(25)->withQueryString();

        // Variance summary
        $summary = [
            'total_orders'   => $costings->total(),
            'total_rm_cost'  => $costings->sum('rm_cost'),
            'total_pm_cost'  => $costings->sum('pm_cost'),
            'total_labour'   => $costings->sum('labour_cost'),
            'total_overhead' => $costings->sum('overhead_cost'),
            'total_cost'     => $costings->sum('total_cost'),
            'total_variance' => $costings->sum('variance'),
        ];

        return view('manufacturing.production-costing.index', compact('costings', 'from', 'to', 'fgId', 'search', 'summary'));
    }
}
