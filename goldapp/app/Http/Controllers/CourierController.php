<?php

namespace App\Http\Controllers;

use App\Models\CourierShipment;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $status = $request->get('status');

        $shipments = CourierShipment::query()
            ->when($q, fn($query) => $query->where('slno', 'like', "%{$q}%")
                ->orWhere('awb_no', 'like', "%{$q}%"))
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('integrations.courier.index', compact('shipments', 'q', 'status'));
    }

    public function create()
    {
        return view('integrations.courier.form', ['shipment' => new CourierShipment()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dispatch_id' => 'nullable|integer',
            'courier_partner' => 'required|in:delhivery,dtdc,bluedart,fedex,ecom_express,custom',
            'weight' => 'required|numeric|min:0.001',
            'dimensions' => 'nullable|string|max:50',
            'charges' => 'nullable|numeric|min:0',
            'pickup_date' => 'nullable|date',
            'estimated_delivery' => 'nullable|date',
        ]);

        $slno = 'SHIP/' . date('Ymd') . '/' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $awbNo = strtoupper($data['courier_partner'][0]) . 'AWB' . date('Y') . str_pad(rand(1, 99999), 8, '0', STR_PAD_LEFT);

        CourierShipment::create(array_merge($data, [
            'slno' => $slno,
            'awb_no' => $awbNo,
            'status' => 'booked',
            'tracking_events' => [[
                'status' => 'booked',
                'location' => 'Origin',
                'timestamp' => now()->toIso8601String(),
                'remarks' => 'Shipment booked',
            ]],
        ]));

        return redirect()->route('couriers.index')->with('success', "Shipment {$slno} booked. AWB: {$awbNo}");
    }

    public function show(CourierShipment $courier)
    {
        return view('integrations.courier.form', ['shipment' => $courier]);
    }

    public function edit(CourierShipment $courier)
    {
        return view('integrations.courier.form', ['shipment' => $courier]);
    }

    public function update(Request $request, CourierShipment $courier)
    {
        $data = $request->validate([
            'status' => 'required|in:booked,picked,in_transit,out_for_delivery,delivered,returned,failed',
            'actual_delivery' => 'nullable|date',
            'vehicle_no' => 'nullable|string',
        ]);

        $events = $courier->tracking_events ?? [];
        $events[] = [
            'status' => $data['status'],
            'timestamp' => now()->toIso8601String(),
            'remarks' => 'Status updated',
        ];
        $courier->update(array_merge($data, ['tracking_events' => $events]));

        return redirect()->route('couriers.index')->with('success', 'Shipment status updated.');
    }

    public function destroy(CourierShipment $courier)
    {
        $courier->delete();
        return redirect()->route('couriers.index')->with('success', 'Shipment record deleted.');
    }

    public function track(string $awb)
    {
        $shipment = CourierShipment::where('awb_no', $awb)->first();
        if (!$shipment) {
            return response()->json(['error' => 'AWB not found'], 404);
        }
        return response()->json([
            'awb_no' => $shipment->awb_no,
            'status' => $shipment->status,
            'tracking_events' => $shipment->tracking_events,
        ]);
    }
}
