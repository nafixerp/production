<?php
namespace App\Http\Controllers;

use App\Models\FgQualityCheck;
use App\Models\FgStock;
use App\Models\FinishedGoods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FGQualityCheckController extends Controller
{
    public function index(Request $request)
    {
        $checks = FgQualityCheck::with('finishedGood')
            ->when($request->batch_no, fn($q) => $q->where('batch_no', 'like', "%{$request->batch_no}%"))
            ->when($request->result, fn($q) => $q->where('result', $request->result))
            ->orderByDesc('check_date')
            ->paginate(20)->withQueryString();

        return view('warehouse.fg-quality.index', compact('checks'));
    }

    public function create()
    {
        $fgList = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $batches = FgStock::where('status', 'available')
            ->select('batch_no', 'fg_id', 'fg_name')
            ->distinct()
            ->orderBy('batch_no')
            ->get();
        return view('warehouse.fg-quality.form', compact('fgList', 'batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fg_id'      => 'required|exists:finished_goods,id',
            'batch_no'   => 'required|string',
            'check_date' => 'required|date',
            'result'     => 'required|in:pass,fail,partial',
        ]);

        DB::transaction(function () use ($request) {
            $check = FgQualityCheck::create([
                'fg_id'        => $request->fg_id,
                'batch_no'     => $request->batch_no,
                'check_date'   => $request->check_date,
                'checked_by'   => Auth::id(),
                'result'       => $request->result,
                'temperature'  => $request->temperature,
                'moisture_pct' => $request->moisture_pct,
                'visual_check' => $request->visual_check,
                'taste_check'  => $request->taste_check,
                'micro_check'  => $request->micro_check,
                'release_date' => $request->release_date ?: null,
                'hold_reason'  => $request->hold_reason,
            ]);

            // On fail: hold batch in fg_stock
            if ($request->result === 'fail') {
                FgStock::where('batch_no', $request->batch_no)
                    ->where('fg_id', $request->fg_id)
                    ->update(['status' => 'on_hold']);
            }
            // On pass: release batch
            if ($request->result === 'pass') {
                FgStock::where('batch_no', $request->batch_no)
                    ->where('fg_id', $request->fg_id)
                    ->where('status', 'on_hold')
                    ->update(['status' => 'available']);
            }
        });

        return redirect()->route('fg-quality.index')->with('success', 'Quality check saved.');
    }

    public function show(FgQualityCheck $fgQuality)
    {
        $fgQuality->load('finishedGood','checker');
        return view('warehouse.fg-quality.show', compact('fgQuality'));
    }

    public function edit(FgQualityCheck $fgQuality)
    {
        $fgList  = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $batches = FgStock::select('batch_no','fg_id','fg_name')->distinct()->orderBy('batch_no')->get();
        return view('warehouse.fg-quality.form', compact('fgQuality', 'fgList', 'batches'));
    }

    public function update(Request $request, FgQualityCheck $fgQuality)
    {
        $request->validate([
            'result' => 'required|in:pass,fail,partial',
        ]);

        DB::transaction(function () use ($request, $fgQuality) {
            $fgQuality->update($request->except(['_token','_method']));

            if ($request->result === 'fail') {
                FgStock::where('batch_no', $fgQuality->batch_no)
                    ->where('fg_id', $fgQuality->fg_id)
                    ->update(['status' => 'on_hold']);
            }
            if ($request->result === 'pass') {
                FgStock::where('batch_no', $fgQuality->batch_no)
                    ->where('fg_id', $fgQuality->fg_id)
                    ->where('status', 'on_hold')
                    ->update(['status' => 'available']);
            }
        });

        return redirect()->route('fg-quality.index')->with('success', 'Quality check updated.');
    }

    public function destroy(FgQualityCheck $fgQuality)
    {
        $fgQuality->delete();
        return redirect()->route('fg-quality.index')->with('success', 'Record deleted.');
    }
}
