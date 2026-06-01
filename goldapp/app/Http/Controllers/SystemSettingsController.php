<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    protected array $categories = [
        'General' => ['app_name','app_timezone','app_locale','default_currency','pagination_limit'],
        'Accounting' => ['default_account_group','round_off_limit','gst_enabled','tds_enabled'],
        'Inventory' => ['stock_valuation_method','negative_stock_allowed','auto_reorder','reorder_alert_email'],
        'Sales' => ['default_price_list','discount_max_pct','credit_limit_check','invoice_prefix'],
        'Purchase' => ['default_lead_days','auto_grn_po_link','pr_approval_required'],
        'HR' => ['payroll_cycle','attendance_mode','leave_year_start'],
        'Notifications' => ['email_from','email_from_name','sms_gateway','push_enabled'],
        'Security' => ['session_timeout_minutes','password_min_length','two_factor_enabled','login_attempts_max'],
    ];

    public function index()
    {
        $allSettings = CompanySetting::all()->keyBy('setting_key');
        $categories = $this->categories;
        return view('settings.system-settings.index', compact('allSettings', 'categories'));
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            CompanySetting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value]
            );
        }

        return redirect()->route('system-settings.index')->with('success', 'Settings saved successfully.');
    }
}
