<?php
namespace Database\Seeders;

use App\Models\Account;
use App\Models\AccountGroup;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        // Create account groups first
        $groups = [
            ['code' => 'AG001', 'name' => 'Current Assets',    'nature' => 'asset'],
            ['code' => 'AG002', 'name' => 'Fixed Assets',      'nature' => 'asset'],
            ['code' => 'AG003', 'name' => 'Current Liabilities','nature' => 'liability'],
            ['code' => 'AG004', 'name' => 'Long-term Liabilities','nature' => 'liability'],
            ['code' => 'AG005', 'name' => 'Sales Income',      'nature' => 'income'],
            ['code' => 'AG006', 'name' => 'Other Income',      'nature' => 'income'],
            ['code' => 'AG007', 'name' => 'Direct Expenses',   'nature' => 'expense'],
            ['code' => 'AG008', 'name' => 'Indirect Expenses', 'nature' => 'expense'],
        ];

        $groupMap = [];
        foreach ($groups as $g) {
            $grp = AccountGroup::firstOrCreate(['code' => $g['code']], $g);
            $groupMap[$g['nature']] = $grp->id; // last one wins per nature, that's fine
        }

        // Map nature to group
        $natureGroup = [];
        foreach ($groups as $g) {
            $grp = AccountGroup::where('code', $g['code'])->first();
            $natureGroup[$g['nature']] = $grp->id;
        }

        $accounts = [
            ['code' => 'CASH',         'name' => 'Cash Account',         'atype' => 'CASH',      'nature' => 'asset'],
            ['code' => 'BANK',         'name' => 'Bank Account',         'atype' => 'BANK',      'nature' => 'asset'],
            ['code' => 'RS',           'name' => 'Sales Account',        'atype' => 'INCOME',    'nature' => 'income'],
            ['code' => 'EP',           'name' => 'Purchase Account',     'atype' => 'EXPENSE',   'nature' => 'expense'],
            ['code' => 'DISC',         'name' => 'Discount Account',     'atype' => 'EXPENSE',   'nature' => 'expense'],
            ['code' => 'SGST',         'name' => 'SGST Payable',         'atype' => 'TAX',       'nature' => 'liability'],
            ['code' => 'CGST',         'name' => 'CGST Payable',         'atype' => 'TAX',       'nature' => 'liability'],
            ['code' => 'IGST',         'name' => 'IGST Payable',         'atype' => 'TAX',       'nature' => 'liability'],
            ['code' => 'TCS',          'name' => 'TCS Account',          'atype' => 'TAX',       'nature' => 'liability'],
            ['code' => 'ROUND',        'name' => 'Rounding Account',     'atype' => 'EXPENSE',   'nature' => 'expense'],
            ['code' => 'COGS',         'name' => 'Cost of Goods Sold',   'atype' => 'EXPENSE',   'nature' => 'expense'],
            ['code' => 'RM-STOCK',     'name' => 'Raw Material Stock',   'atype' => 'ASSET',     'nature' => 'asset'],
            ['code' => 'FG-STOCK',     'name' => 'Finished Goods Stock', 'atype' => 'ASSET',     'nature' => 'asset'],
            ['code' => 'WIP',          'name' => 'Work In Progress',     'atype' => 'ASSET',     'nature' => 'asset'],
            ['code' => 'CUST-DEFAULT', 'name' => 'Default Customer',     'atype' => 'CUSTOMER',  'nature' => 'asset'],
            ['code' => 'SUPP-DEFAULT', 'name' => 'Default Supplier',     'atype' => 'SUPPLIER',  'nature' => 'liability'],
            ['code' => 'SALARY',       'name' => 'Salary Payable',       'atype' => 'LIABILITY', 'nature' => 'liability'],
            ['code' => 'LABOUR',       'name' => 'Labour Recovery',      'atype' => 'EXPENSE',   'nature' => 'expense'],
        ];

        foreach ($accounts as $acc) {
            Account::firstOrCreate(['code' => $acc['code']], [
                'name'            => $acc['name'],
                'atype'           => $acc['atype'],
                'group_id'        => $natureGroup[$acc['nature']] ?? null,
                'opening_balance' => 0,
                'ob_type'         => 'dr',
                'status'          => 1,
            ]);
        }
    }
}
