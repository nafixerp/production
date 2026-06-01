<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use App\Models\PaymentGatewayTxn;
use App\Models\CourierShipment;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function receive(Request $request, string $source)
    {
        $payload = $request->all();
        $eventType = $request->header('X-Event-Type') ?? $payload['event'] ?? 'unknown';

        $log = WebhookLog::create([
            'source' => $source,
            'event_type' => $eventType,
            'payload' => $payload,
            'status' => 'received',
            'created_at' => now(),
        ]);

        try {
            $this->process($source, $eventType, $payload, $log);
            $log->update(['status' => 'processed', 'processed_at' => now()]);
        } catch (\Throwable $e) {
            $log->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }

        return response()->json(['received' => true, 'log_id' => $log->id], 200);
    }

    protected function process(string $source, string $eventType, array $payload, WebhookLog $log): void
    {
        match ($source) {
            'razorpay', 'paytm', 'stripe' => $this->processPayment($source, $eventType, $payload),
            'delhivery', 'bluedart', 'dtdc' => $this->processCourier($source, $eventType, $payload),
            default => null,
        };
    }

    protected function processPayment(string $source, string $eventType, array $payload): void
    {
        if (in_array($eventType, ['payment.captured', 'payment.success'])) {
            $txnId = $payload['payload']['payment']['entity']['id'] ?? $payload['txn_id'] ?? null;
            if ($txnId) {
                PaymentGatewayTxn::where('gateway_txn_id', $txnId)
                    ->update(['status' => 'success', 'settled_at' => now()]);
            }
        }
    }

    protected function processCourier(string $source, string $eventType, array $payload): void
    {
        $awb = $payload['awb'] ?? $payload['awb_no'] ?? null;
        if ($awb) {
            $statusMap = [
                'delivered' => 'delivered',
                'in_transit' => 'in_transit',
                'picked' => 'picked',
            ];
            $status = $statusMap[$eventType] ?? null;
            if ($status) {
                $shipment = CourierShipment::where('awb_no', $awb)->first();
                if ($shipment) {
                    $events = $shipment->tracking_events ?? [];
                    $events[] = [
                        'status' => $status,
                        'timestamp' => now()->toIso8601String(),
                        'source' => 'webhook',
                    ];
                    $shipment->update(['status' => $status, 'tracking_events' => $events]);
                }
            }
        }
    }

    public function index()
    {
        $logs = WebhookLog::orderByDesc('created_at')->paginate(50);
        return view('integrations.webhooks.index', compact('logs'));
    }
}
