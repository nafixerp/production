<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MobileAPIController extends Controller
{
    /**
     * POST /api/v1/auth/login — authenticate, return simple token
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Create/retrieve API token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * GET /api/v1/dashboard — today's KPIs
     */
    public function dashboard(Request $request)
    {
        $today = now()->toDateString();

        $data = [
            'date' => $today,
            'sales_today' => (float) DB::table('sales_invoices')
                ->whereDate('invoice_date', $today)
                ->sum('total_amount') ?? 0,
            'orders_open' => DB::table('sales_orders')
                ->where('status', 'open')
                ->count(),
            'production_orders_today' => DB::table('production_orders')
                ->whereDate('created_at', $today)
                ->count(),
            'pending_dispatches' => DB::table('dispatch_orders')
                ->where('status', 'pending')
                ->count(),
            'low_stock_items' => DB::table('stock_movements')
                ->select('item_id')
                ->groupBy('item_id')
                ->havingRaw('SUM(CASE WHEN movement_type IN ("in","production_in") THEN qty ELSE -qty END) < 10')
                ->count(),
        ];

        return response()->json($data);
    }

    /**
     * GET /api/v1/stock/{item_id} — current stock for item
     */
    public function stockCheck(Request $request, int $itemId)
    {
        $stock = DB::table('stock_movements')
            ->where('item_id', $itemId)
            ->selectRaw('SUM(CASE WHEN movement_type IN ("in","production_in","transfer_in") THEN qty ELSE -qty END) as current_qty')
            ->first();

        $item = DB::table('raw_materials')->find($itemId)
            ?? DB::table('finished_goods')->find($itemId);

        return response()->json([
            'item_id' => $itemId,
            'item_name' => $item->name ?? 'Unknown',
            'current_qty' => (float) ($stock->current_qty ?? 0),
            'unit' => $item->uom ?? '',
        ]);
    }

    /**
     * GET /api/v1/sales-orders — open sales orders
     */
    public function salesOrders(Request $request)
    {
        $orders = DB::table('sales_orders')
            ->where('status', 'open')
            ->orderByDesc('order_date')
            ->limit(50)
            ->get();

        return response()->json(['orders' => $orders]);
    }

    /**
     * POST /api/v1/dispatch/{id}/confirm — confirm delivery
     */
    public function confirmDispatch(Request $request, int $id)
    {
        $data = $request->validate([
            'delivered_at' => 'nullable|date',
            'remarks' => 'nullable|string|max:255',
        ]);

        $dispatch = DB::table('dispatch_orders')->find($id);
        if (!$dispatch) {
            return response()->json(['error' => 'Dispatch not found'], 404);
        }

        DB::table('dispatch_orders')->where('id', $id)->update([
            'status' => 'delivered',
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'dispatch_id' => $id,
            'delivered_at' => $data['delivered_at'] ?? now()->toIso8601String(),
        ]);
    }
}
