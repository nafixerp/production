<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    protected array $modules = [
        'sales' => ['order_created', 'payment_received', 'order_cancelled'],
        'purchase' => ['po_created', 'grn_completed', 'payment_due'],
        'production' => ['plan_started', 'order_completed', 'quality_fail'],
        'inventory' => ['low_stock', 'expiry_alert', 'transfer_done'],
        'accounts' => ['invoice_due', 'payment_overdue', 'bank_reconciled'],
        'hr' => ['leave_request', 'payroll_processed', 'attendance_alert'],
        'dispatch' => ['shipment_booked', 'out_for_delivery', 'delivered'],
    ];

    public function index()
    {
        $userId = auth()->id();
        $prefs = NotificationPreference::where('user_id', $userId)->get()
            ->groupBy('module')
            ->map(fn($group) => $group->keyBy('event_type'));
        $modules = $this->modules;
        return view('settings.notifications.index', compact('prefs', 'modules'));
    }

    public function update(Request $request)
    {
        $userId = auth()->id();
        $prefData = $request->input('prefs', []);

        foreach ($this->modules as $module => $events) {
            foreach ($events as $event) {
                $key = "{$module}.{$event}";
                NotificationPreference::updateOrCreate(
                    ['user_id' => $userId, 'module' => $module, 'event_type' => $event],
                    [
                        'email' => isset($prefData[$key]['email']) ? 1 : 0,
                        'sms' => isset($prefData[$key]['sms']) ? 1 : 0,
                        'push' => isset($prefData[$key]['push']) ? 1 : 0,
                    ]
                );
            }
        }

        return redirect()->route('notification-prefs.index')->with('success', 'Notification preferences saved.');
    }
}
