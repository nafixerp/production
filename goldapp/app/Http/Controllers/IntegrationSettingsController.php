<?php

namespace App\Http\Controllers;

use App\Models\IntegrationSetting;
use Illuminate\Http\Request;

class IntegrationSettingsController extends Controller
{
    protected array $integrationTypes = [
        'gst' => ['label' => 'GST / E-Invoice', 'icon' => 'bi-receipt-cutoff', 'providers' => ['NIC','Tally','ClearTax','Zoho']],
        'payment_gateway' => ['label' => 'Payment Gateway', 'icon' => 'bi-credit-card', 'providers' => ['Razorpay','Paytm','Stripe','PayPal','UPI']],
        'courier' => ['label' => 'Courier / Logistics', 'icon' => 'bi-truck', 'providers' => ['Delhivery','DTDC','BlueDart','FedEx','Ecom Express']],
        'ecommerce' => ['label' => 'E-Commerce Channel', 'icon' => 'bi-cart3', 'providers' => ['Amazon','Flipkart','Shopify','WooCommerce','Swiggy','Zomato']],
        'accounting' => ['label' => 'Accounting Software', 'icon' => 'bi-journal-bookmark', 'providers' => ['Tally','QuickBooks','Zoho Books','SAP']],
        'bi' => ['label' => 'BI / Analytics', 'icon' => 'bi-bar-chart-line', 'providers' => ['Power BI','Tableau','Metabase','Google Data Studio']],
    ];

    public function index()
    {
        $settings = IntegrationSetting::all()->keyBy('integration_type');
        $types = $this->integrationTypes;
        return view('integrations.settings.index', compact('settings', 'types'));
    }

    public function configure(string $type)
    {
        abort_unless(array_key_exists($type, $this->integrationTypes), 404);
        $setting = IntegrationSetting::firstOrNew(['integration_type' => $type]);
        $meta = $this->integrationTypes[$type];
        return view('integrations.settings.configure', compact('type', 'setting', 'meta'));
    }

    public function save(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, $this->integrationTypes), 404);
        $data = $request->validate([
            'provider' => 'required|string|max:80',
            'is_active' => 'nullable|boolean',
            'config' => 'nullable|array',
        ]);

        IntegrationSetting::updateOrCreate(
            ['integration_type' => $type],
            [
                'provider' => $data['provider'],
                'is_active' => $data['is_active'] ?? 0,
                'config' => $data['config'] ?? [],
            ]
        );

        return redirect()->route('integrations.index')->with('success', 'Integration settings saved.');
    }

    public function test(string $type)
    {
        // Simulate connection test
        $results = [
            'success' => true,
            'message' => "Connection to {$type} tested successfully (simulated).",
            'latency_ms' => rand(80, 350),
        ];
        return response()->json($results);
    }

    public function sync(string $type)
    {
        $setting = IntegrationSetting::where('integration_type', $type)->first();
        if ($setting) {
            $setting->update(['last_sync_at' => now()]);
        }
        return response()->json(['success' => true, 'synced_at' => now()->toIso8601String()]);
    }
}
