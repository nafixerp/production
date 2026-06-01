<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder {
    public function run(): void {
        // Roles
        $roles = [
            ['name' => 'super_admin',     'display_name' => 'Super Administrator', 'description' => 'Full unrestricted access',        'is_system' => 1],
            ['name' => 'admin',           'display_name' => 'Administrator',        'description' => 'System administrator',            'is_system' => 1],
            ['name' => 'manager',         'display_name' => 'Manager',              'description' => 'Branch / department manager',     'is_system' => 0],
            ['name' => 'accountant',      'display_name' => 'Accountant',           'description' => 'Accounts & finance operations',   'is_system' => 0],
            ['name' => 'storekeeper',     'display_name' => 'Storekeeper',          'description' => 'Inventory & warehouse management','is_system' => 0],
            ['name' => 'production_user', 'display_name' => 'Production User',      'description' => 'Production floor operations',     'is_system' => 0],
            ['name' => 'sales_user',      'display_name' => 'Sales User',           'description' => 'Sales & CRM operations',          'is_system' => 0],
            ['name' => 'viewer',          'display_name' => 'Viewer',               'description' => 'Read-only access',                'is_system' => 0],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['name' => $r['name']], $r);
        }

        // Permissions: module × action
        $modules = [
            'purchases', 'inventory', 'production', 'sales', 'crm',
            'accounts', 'reports', 'settings', 'users', 'hrm',
        ];
        $actions = ['view', 'create', 'edit', 'delete', 'approve', 'export'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::updateOrCreate(
                    ['name' => "{$module}.{$action}"],
                    [
                        'display_name' => ucfirst($module) . ' - ' . ucfirst($action),
                        'module'       => $module,
                        'action'       => $action,
                    ]
                );
            }
        }

        // Assign ALL permissions to super_admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        $allPermIds = Permission::pluck('id');
        $existing   = DB::table('role_permissions')->where('role_id', $superAdmin->id)->pluck('permission_id')->flip();

        $inserts = [];
        foreach ($allPermIds as $pid) {
            if (!isset($existing[$pid])) {
                $inserts[] = ['role_id' => $superAdmin->id, 'permission_id' => $pid, 'created_at' => now(), 'updated_at' => now()];
            }
        }
        if ($inserts) DB::table('role_permissions')->insert($inserts);

        // Assign sensible defaults to admin (all except users.delete)
        $admin = Role::where('name', 'admin')->first();
        $adminPerms = Permission::where('name', '!=', 'users.delete')->pluck('id');
        DB::table('role_permissions')->where('role_id', $admin->id)->delete();
        $adminInserts = $adminPerms->map(fn($pid) => ['role_id' => $admin->id, 'permission_id' => $pid, 'created_at' => now(), 'updated_at' => now()])->toArray();
        DB::table('role_permissions')->insert($adminInserts);

        // Manager: view + create + edit + approve on all modules
        $manager = Role::where('name', 'manager')->first();
        $managerPerms = Permission::whereIn('action', ['view', 'create', 'edit', 'approve', 'export'])->pluck('id');
        DB::table('role_permissions')->where('role_id', $manager->id)->delete();
        $inserts = $managerPerms->map(fn($pid) => ['role_id' => $manager->id, 'permission_id' => $pid, 'created_at' => now(), 'updated_at' => now()])->toArray();
        DB::table('role_permissions')->insert($inserts);

        // Accountant: accounts + reports + purchases (view/export only on purchases)
        $accountant = Role::where('name', 'accountant')->first();
        $acctPerms = Permission::whereIn('module', ['accounts', 'reports'])
            ->orWhere(fn($q) => $q->where('module', 'purchases')->whereIn('action', ['view', 'export']))
            ->pluck('id');
        DB::table('role_permissions')->where('role_id', $accountant->id)->delete();
        $inserts = $acctPerms->map(fn($pid) => ['role_id' => $accountant->id, 'permission_id' => $pid, 'created_at' => now(), 'updated_at' => now()])->toArray();
        if ($inserts) DB::table('role_permissions')->insert($inserts);

        // Storekeeper: inventory + production (view/create/edit)
        $storekeeper = Role::where('name', 'storekeeper')->first();
        $storePerms = Permission::whereIn('module', ['inventory', 'production'])
            ->whereIn('action', ['view', 'create', 'edit', 'export'])->pluck('id');
        DB::table('role_permissions')->where('role_id', $storekeeper->id)->delete();
        $inserts = $storePerms->map(fn($pid) => ['role_id' => $storekeeper->id, 'permission_id' => $pid, 'created_at' => now(), 'updated_at' => now()])->toArray();
        if ($inserts) DB::table('role_permissions')->insert($inserts);

        // Production user: production + inventory view
        $prodUser = Role::where('name', 'production_user')->first();
        $prodPerms = Permission::where('module', 'production')
            ->orWhere(fn($q) => $q->where('module', 'inventory')->where('action', 'view'))
            ->pluck('id');
        DB::table('role_permissions')->where('role_id', $prodUser->id)->delete();
        $inserts = $prodPerms->map(fn($pid) => ['role_id' => $prodUser->id, 'permission_id' => $pid, 'created_at' => now(), 'updated_at' => now()])->toArray();
        if ($inserts) DB::table('role_permissions')->insert($inserts);

        // Sales user: sales + crm + reports view
        $salesUser = Role::where('name', 'sales_user')->first();
        $salesPerms = Permission::whereIn('module', ['sales', 'crm'])
            ->orWhere(fn($q) => $q->where('module', 'reports')->where('action', 'view'))
            ->pluck('id');
        DB::table('role_permissions')->where('role_id', $salesUser->id)->delete();
        $inserts = $salesPerms->map(fn($pid) => ['role_id' => $salesUser->id, 'permission_id' => $pid, 'created_at' => now(), 'updated_at' => now()])->toArray();
        if ($inserts) DB::table('role_permissions')->insert($inserts);

        // Viewer: view + export only across all modules
        $viewer = Role::where('name', 'viewer')->first();
        $viewerPerms = Permission::whereIn('action', ['view', 'export'])->pluck('id');
        DB::table('role_permissions')->where('role_id', $viewer->id)->delete();
        $inserts = $viewerPerms->map(fn($pid) => ['role_id' => $viewer->id, 'permission_id' => $pid, 'created_at' => now(), 'updated_at' => now()])->toArray();
        if ($inserts) DB::table('role_permissions')->insert($inserts);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
