<?php

namespace App\Http\Controllers;

use App\Models\GstFiling;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GSTFilingController extends Controller
{
    public function index()
    {
        $filings = GstFiling::orderByDesc('created_at')->paginate(20);
        return view('integrations.gst.filings', compact('filings'));
    }

    public function create()
    {
        return view('integrations.gst.prepare', ['filing' => new GstFiling()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'period_name' => 'required|string|max:20',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'filing_type' => 'required|in:GSTR1,GSTR3B,GSTR9',
        ]);

        // Simulate computing totals from sales/purchase invoices
        $filing = GstFiling::create([
            'period_name' => $data['period_name'],
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
            'filing_type' => $data['filing_type'],
            'total_taxable' => 0,
            'total_sgst' => 0,
            'total_cgst' => 0,
            'total_igst' => 0,
            'status' => 'draft',
        ]);

        return redirect()->route('gst-filings.show', $filing)->with('success', 'GST filing created in draft.');
    }

    public function show(GstFiling $gstFiling)
    {
        return view('integrations.gst.prepare', ['filing' => $gstFiling]);
    }

    public function edit(GstFiling $gstFiling)
    {
        return view('integrations.gst.prepare', ['filing' => $gstFiling]);
    }

    public function update(Request $request, GstFiling $gstFiling)
    {
        $data = $request->validate([
            'status' => 'required|in:draft,ready,filed,error',
        ]);
        $gstFiling->update($data);
        if ($data['status'] === 'filed') {
            $gstFiling->update(['filed_at' => now()]);
        }
        return redirect()->route('gst-filings.index')->with('success', 'Filing status updated.');
    }

    public function destroy(GstFiling $gstFiling)
    {
        $gstFiling->delete();
        return redirect()->route('gst-filings.index')->with('success', 'Filing deleted.');
    }

    public function prepare(string $type)
    {
        // Compute GST data from invoices for given type
        $fromDate = Carbon::now()->startOfMonth();
        $toDate = Carbon::now()->endOfMonth();
        $data = [
            'type' => $type,
            'period' => $fromDate->format('M Y'),
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'total_taxable' => 0,
            'total_cgst' => 0,
            'total_sgst' => 0,
            'total_igst' => 0,
            'invoice_count' => 0,
        ];
        return view('integrations.gst.prepare', compact('data'));
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'filing_id' => 'required|exists:gst_filings,id',
        ]);
        $filing = GstFiling::find($data['filing_id']);

        // Build JSON payload (simplified)
        $payload = [
            'version' => '1.1',
            'gstin' => config('app.gstin', ''),
            'fp' => $filing->period_name,
            'gt' => $filing->total_taxable,
            'cur_gt' => $filing->total_taxable,
            'b2b' => [],
        ];

        $filing->update([
            'json_payload' => $payload,
            'status' => 'ready',
        ]);

        return redirect()->route('gst-filings.show', $filing)->with('success', 'JSON payload generated.');
    }

    public function download(GstFiling $gstFiling)
    {
        $json = json_encode($gstFiling->json_payload ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = "GST_{$gstFiling->filing_type}_{$gstFiling->period_name}.json";
        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
