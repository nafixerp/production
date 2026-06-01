<?php
namespace App\Http\Controllers;

use App\Models\BatchTraceability;
use App\Models\DispatchOrderItem;
use App\Models\FgQualityCheck;
use App\Models\FgStock;
use App\Models\FinishedGoods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchTraceabilityController extends Controller
{
    public function index(Request $request)
    {
        $batches = BatchTraceability::with('finishedGood')
            ->when($request->batch_no, fn($q) => $q->where('batch_no', 'like', "%{$request->batch_no}%"))
            ->when($request->fg_id, fn($q) => $q->where('fg_id', $request->fg_id))
            ->when($request->qc_status, fn($q) => $q->where('qc_status', $request->qc_status))
            ->orderByDesc('id')
            ->paginate(20)->withQueryString();

        $fgList = FinishedGoods::where('status', 1)->orderBy('name')->get();

        return view('warehouse.batch-traceability.index', compact('batches', 'fgList'));
    }

    public function show(string $batchNo)
    {
        $trace      = BatchTraceability::where('batch_no', $batchNo)->first();
        $stocks     = FgStock::where('batch_no', $batchNo)->with('warehouse')->get();
        $qcChecks   = FgQualityCheck::where('batch_no', $batchNo)->with('finishedGood')->get();
        $dispatches = DispatchOrderItem::where('batch_no', $batchNo)->with('dispatchOrder')->get();

        return view('warehouse.batch-traceability.show', compact('trace','stocks','qcChecks','dispatches','batchNo'));
    }

    // Legacy stubs kept for Mobile API routes
    public function store(Request $request)       { return redirect()->back()->with('success','Saved.'); }
    public function createBackup()                { return redirect()->back()->with('success','Backup initiated.'); }
    public function update(Request $request, $id=null) { return redirect()->back()->with('success','Updated.'); }
    public function login(Request $request)       { return response()->json(['status'=>'ok']); }
    public function stockCheck($item_id)          { return response()->json(['item_id'=>$item_id,'stock'=>0]); }
    public function dispatchList()                { return response()->json(['dispatches'=>[]]); }
}
