<?php

namespace App\Http\Controllers;

use App\Models\PaymentGatewayTxn;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $gateway = $request->get('gateway');
        $status = $request->get('status');

        $txns = PaymentGatewayTxn::query()
            ->when($q, fn($query) => $query->where('order_ref', 'like', "%{$q}%")
                ->orWhere('gateway_txn_id', 'like', "%{$q}%"))
            ->when($gateway, fn($query) => $query->where('gateway', $gateway))
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $summary = [
            'total_success' => PaymentGatewayTxn::where('status', 'success')->sum('amount'),
            'total_pending' => PaymentGatewayTxn::whereIn('status', ['created', 'processing'])->count(),
            'total_failed' => PaymentGatewayTxn::where('status', 'failed')->count(),
        ];

        return view('integrations.payment-gateway.index', compact('txns', 'summary', 'q', 'gateway', 'status'));
    }

    public function reconcile(int $id)
    {
        $txn = PaymentGatewayTxn::findOrFail($id);
        // Mark as settled/reconciled
        $txn->update(['settled_at' => now()]);
        return redirect()->back()->with('success', "Transaction {$txn->gateway_txn_id} reconciled.");
    }

    public function statusCheck(int $id)
    {
        $txn = PaymentGatewayTxn::findOrFail($id);
        // Simulate gateway status check
        return response()->json([
            'txn_id' => $txn->gateway_txn_id,
            'status' => $txn->status,
            'amount' => $txn->amount,
            'currency' => $txn->currency,
            'checked_at' => now()->toIso8601String(),
        ]);
    }
}
