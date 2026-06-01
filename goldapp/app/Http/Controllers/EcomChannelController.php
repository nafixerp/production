<?php

namespace App\Http\Controllers;

use App\Models\EcomChannelOrder;
use Illuminate\Http\Request;

class EcomChannelController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $channel = $request->get('channel');
        $status = $request->get('status');

        $orders = EcomChannelOrder::query()
            ->when($q, fn($query) => $query->where('channel_order_id', 'like', "%{$q}%")
                ->orWhere('customer_name', 'like', "%{$q}%"))
            ->when($channel, fn($query) => $query->where('channel', $channel))
            ->when($status, fn($query) => $query->where('status', $status))
            ->orderByDesc('order_date')
            ->paginate(25)
            ->withQueryString();

        return view('integrations.ecom-orders.index', compact('orders', 'q', 'channel', 'status'));
    }

    public function show(EcomChannelOrder $ecomChannelOrder)
    {
        return view('integrations.ecom-orders.index', [
            'orders' => EcomChannelOrder::paginate(25),
            'selected' => $ecomChannelOrder,
        ]);
    }

    public function mapToSO(Request $request, EcomChannelOrder $ecomChannelOrder)
    {
        $data = $request->validate(['so_id' => 'required|integer']);
        $ecomChannelOrder->update([
            'mapped_so_id' => $data['so_id'],
            'status' => 'processing',
        ]);
        return redirect()->route('ecom-orders.index')->with('success', 'Order mapped to Sales Order #' . $data['so_id']);
    }

    public function process(EcomChannelOrder $ecomChannelOrder)
    {
        if ($ecomChannelOrder->status === 'new') {
            $ecomChannelOrder->update(['status' => 'processing']);
        }
        return redirect()->route('ecom-orders.index')->with('success', 'Order moved to processing.');
    }

    public function cancel(EcomChannelOrder $ecomChannelOrder)
    {
        $ecomChannelOrder->update(['status' => 'cancelled']);
        return redirect()->route('ecom-orders.index')->with('success', 'Order cancelled.');
    }
}
