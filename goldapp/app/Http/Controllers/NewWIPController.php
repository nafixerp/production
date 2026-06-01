<?php
namespace App\Http\Controllers;

use App\Models\ProductionOrderNew;
use App\Models\WipTracking;
use Illuminate\Http\Request;

class NewWIPController extends Controller
{
    public function index(Request $request)
    {
        $status   = $request->status ?? 'in_progress';
        $priority = $request->priority;

        $orders = ProductionOrderNew::with(['bom', 'wipTracking'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($priority, fn($q) => $q->where('priority', $priority))
            ->whereIn('status', ['released', 'in_progress'])
            ->orderBy('priority', 'desc')
            ->orderBy('planned_end')
            ->get();

        // Enrich with progress calculation
        $orders->each(function ($order) {
            $totalBom   = $order->bom->count();
            $issuedBom  = $order->bom->filter(fn($b) => $b->issued_qty >= $b->required_qty)->count();
            $order->bom_progress_pct = $totalBom > 0 ? round(($issuedBom / $totalBom) * 100) : 0;

            $lastWip    = $order->wipTracking->sortByDesc('id')->first();
            $order->current_stage = $lastWip ? $lastWip->stage : 'pending';
            $order->last_updated  = $lastWip ? $lastWip->updated_at : null;
        });

        // Group by status for Kanban
        $byStatus = $orders->groupBy('status');

        return view('manufacturing.wip.index', compact('orders', 'byStatus', 'status', 'priority'));
    }
}
