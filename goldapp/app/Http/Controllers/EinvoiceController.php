<?php

namespace App\Http\Controllers;

use App\Models\Einvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EinvoiceController extends Controller
{
    public function index()
    {
        $einvoices = Einvoice::orderByDesc('created_at')->paginate(25);
        return view('integrations.einvoice.index', compact('einvoices'));
    }

    public function create()
    {
        return redirect()->route('einvoices.index');
    }

    public function store(Request $request)
    {
        $request->validate(['invoice_id' => 'required|integer']);
        return $this->generate($request->input('invoice_id'));
    }

    /**
     * Simulate IRN generation for a sales invoice.
     */
    public function generate(int $invoiceId)
    {
        $existing = Einvoice::where('sales_invoice_id', $invoiceId)->where('status', 'active')->first();
        if ($existing) {
            return redirect()->back()->with('error', 'An active IRN already exists for this invoice.');
        }

        $irn = hash('sha256', 'INV-' . $invoiceId . '-' . now()->timestamp . '-' . Str::random(8));
        $ackNo = 'ACK' . date('Y') . str_pad($invoiceId, 8, '0', STR_PAD_LEFT);
        $qrData = base64_encode(json_encode([
            'irn' => $irn,
            'ackNo' => $ackNo,
            'invoice_id' => $invoiceId,
            'ts' => now()->toIso8601String(),
        ]));

        Einvoice::create([
            'sales_invoice_id' => $invoiceId,
            'irn' => $irn,
            'ack_no' => $ackNo,
            'ack_date' => now(),
            'qr_code' => $qrData,
            'signed_invoice' => json_encode(['irn' => $irn, 'invoice_id' => $invoiceId]),
            'status' => 'active',
        ]);

        return redirect()->route('einvoices.index')->with('success', "IRN generated: {$irn}");
    }

    public function show(Einvoice $einvoice)
    {
        return view('integrations.einvoice.index', [
            'einvoices' => Einvoice::paginate(25),
            'selected' => $einvoice,
        ]);
    }

    public function edit(Einvoice $einvoice)
    {
        return $this->show($einvoice);
    }

    public function update(Request $request, Einvoice $einvoice)
    {
        return redirect()->route('einvoices.index');
    }

    public function destroy(Einvoice $einvoice)
    {
        return $this->cancelIrn($einvoice->id);
    }

    public function cancelIrn(int $id)
    {
        $einvoice = Einvoice::findOrFail($id);
        if ($einvoice->status === 'cancelled') {
            return redirect()->back()->with('error', 'IRN is already cancelled.');
        }
        $cancelIrn = hash('sha256', 'CANCEL-' . $einvoice->irn . '-' . now()->timestamp);
        $einvoice->update([
            'status' => 'cancelled',
            'cancel_irn' => $cancelIrn,
            'cancel_date' => now(),
        ]);
        return redirect()->route('einvoices.index')->with('success', 'IRN cancelled successfully.');
    }
}
