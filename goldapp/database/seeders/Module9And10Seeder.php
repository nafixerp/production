<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SequenceConfig;
use App\Models\CompanySetting;

class Module9And10Seeder extends Seeder
{
    public function run(): void
    {
        // Default Sequence Configurations
        $sequences = [
            ['module' => 'sales_order',        'prefix' => 'SO',   'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'sales_invoice',       'prefix' => 'INV',  'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'purchase_order',      'prefix' => 'PO',   'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'purchase_invoice',    'prefix' => 'PINV', 'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'grn',                 'prefix' => 'GRN',  'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'production_order',    'prefix' => 'PRD',  'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'dispatch_order',      'prefix' => 'DO',   'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'delivery_note',       'prefix' => 'DN',   'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'receipt_voucher',     'prefix' => 'RV',   'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'payment_voucher',     'prefix' => 'PV',   'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'journal_voucher',     'prefix' => 'JV',   'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'credit_note',         'prefix' => 'CN',   'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'debit_note',          'prefix' => 'DN',   'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
            ['module' => 'courier_shipment',    'prefix' => 'SHIP', 'format' => '{PREFIX}/{YYYY}{MM}/{SEQ4}', 'reset_cycle' => 'monthly'],
            ['module' => 'crm_lead',            'prefix' => 'LEAD', 'format' => '{PREFIX}/{YY}/{SEQ4}', 'reset_cycle' => 'yearly'],
        ];

        foreach ($sequences as $seq) {
            SequenceConfig::firstOrCreate(
                ['module' => $seq['module']],
                array_merge($seq, ['current_seq' => 0, 'branch_specific' => 0])
            );
        }

        // Default System Settings
        $settings = [
            // General
            ['setting_key' => 'app_name',              'setting_value' => 'Food Production ERP', 'setting_type' => 'string',  'description' => 'Application display name'],
            ['setting_key' => 'app_timezone',           'setting_value' => 'Asia/Kolkata',        'setting_type' => 'string',  'description' => 'Server timezone'],
            ['setting_key' => 'app_locale',             'setting_value' => 'en',                  'setting_type' => 'string',  'description' => 'Default locale'],
            ['setting_key' => 'default_currency',       'setting_value' => 'INR',                 'setting_type' => 'string',  'description' => 'Default currency code'],
            ['setting_key' => 'pagination_limit',       'setting_value' => '25',                  'setting_type' => 'number',  'description' => 'Records per page'],
            // Accounting
            ['setting_key' => 'default_account_group', 'setting_value' => '',                    'setting_type' => 'string',  'description' => 'Default account group'],
            ['setting_key' => 'round_off_limit',        'setting_value' => '0.50',                'setting_type' => 'number',  'description' => 'Auto round-off limit'],
            ['setting_key' => 'gst_enabled',            'setting_value' => '1',                   'setting_type' => 'boolean', 'description' => 'Enable GST calculations'],
            ['setting_key' => 'tds_enabled',            'setting_value' => '0',                   'setting_type' => 'boolean', 'description' => 'Enable TDS deductions'],
            // Inventory
            ['setting_key' => 'stock_valuation_method','setting_value' => 'FIFO',                'setting_type' => 'string',  'description' => 'Stock valuation: FIFO/FEFO/Avg'],
            ['setting_key' => 'negative_stock_allowed','setting_value' => '0',                   'setting_type' => 'boolean', 'description' => 'Allow negative stock'],
            ['setting_key' => 'auto_reorder',           'setting_value' => '1',                   'setting_type' => 'boolean', 'description' => 'Auto reorder suggestions'],
            ['setting_key' => 'reorder_alert_email',    'setting_value' => '',                    'setting_type' => 'string',  'description' => 'Email for reorder alerts'],
            // Sales
            ['setting_key' => 'default_price_list',    'setting_value' => 'Standard',            'setting_type' => 'string',  'description' => 'Default price list name'],
            ['setting_key' => 'discount_max_pct',       'setting_value' => '20',                  'setting_type' => 'number',  'description' => 'Maximum discount allowed (%)'],
            ['setting_key' => 'credit_limit_check',     'setting_value' => '1',                   'setting_type' => 'boolean', 'description' => 'Enforce customer credit limit'],
            ['setting_key' => 'invoice_prefix',         'setting_value' => 'INV',                 'setting_type' => 'string',  'description' => 'Sales invoice prefix'],
            // Purchase
            ['setting_key' => 'default_lead_days',     'setting_value' => '7',                   'setting_type' => 'number',  'description' => 'Default lead time (days)'],
            ['setting_key' => 'auto_grn_po_link',       'setting_value' => '1',                   'setting_type' => 'boolean', 'description' => 'Auto-link GRN to PO'],
            ['setting_key' => 'pr_approval_required',   'setting_value' => '1',                   'setting_type' => 'boolean', 'description' => 'PR requires approval'],
            // HR
            ['setting_key' => 'payroll_cycle',         'setting_value' => 'Monthly',             'setting_type' => 'string',  'description' => 'Payroll processing cycle'],
            ['setting_key' => 'attendance_mode',        'setting_value' => 'Manual',              'setting_type' => 'string',  'description' => 'Attendance mode: Manual/Biometric'],
            ['setting_key' => 'leave_year_start',       'setting_value' => 'April',               'setting_type' => 'string',  'description' => 'Leave year start month'],
            // Notifications
            ['setting_key' => 'email_from',            'setting_value' => 'noreply@example.com', 'setting_type' => 'string',  'description' => 'From email address'],
            ['setting_key' => 'email_from_name',        'setting_value' => 'Food ERP',            'setting_type' => 'string',  'description' => 'From name in emails'],
            ['setting_key' => 'sms_gateway',            'setting_value' => '',                    'setting_type' => 'string',  'description' => 'SMS gateway provider'],
            ['setting_key' => 'push_enabled',           'setting_value' => '0',                   'setting_type' => 'boolean', 'description' => 'Enable push notifications'],
            // Security
            ['setting_key' => 'session_timeout_minutes','setting_value' => '120',                 'setting_type' => 'number',  'description' => 'Session idle timeout (minutes)'],
            ['setting_key' => 'password_min_length',    'setting_value' => '8',                   'setting_type' => 'number',  'description' => 'Minimum password length'],
            ['setting_key' => 'two_factor_enabled',     'setting_value' => '0',                   'setting_type' => 'boolean', 'description' => 'Enable 2-factor authentication'],
            ['setting_key' => 'login_attempts_max',     'setting_value' => '5',                   'setting_type' => 'number',  'description' => 'Max failed login attempts before lockout'],
        ];

        foreach ($settings as $setting) {
            CompanySetting::firstOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}
