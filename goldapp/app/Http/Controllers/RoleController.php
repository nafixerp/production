<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller {
    public function index() {
        $roles = Role::withCount('users')->orderBy('name')->get();
        // Add user count manually since we use UserRole not direct relation
        $roles->each(function ($role) {
            $role->user_count = UserRole::where('role_id', $role->id)->count();
        });
        return view('admin.roles.index', compact('roles'));
    }

    public function create() {
        return view('admin.roles.form');
    }

    public function store(Request $request) {
        $request->validate([
            'name'         => 'required|string|max:80|unique:roles,name',
            'display_name' => 'required|string|max:100',
        ]);

        $role = Role::create([
            'name'         => $request->name,
            'display_name' => $request->display_name,
            'description'  => $request->description,
            'is_system'    => 0,
        ]);

        return redirect()->route('roles.permissions', $role->id)
            ->with('success', 'Role created. Now assign permissions.');
    }

    public function show($id) {
        $role = Role::with('permissions')->findOrFail($id);
        return redirect()->route('roles.permissions', $id);
    }

    public function edit($id) {
        $role = Role::findOrFail($id);
        return view('admin.roles.form', compact('role'));
    }

    public function update(Request $request, $id) {
        $role = Role::findOrFail($id);

        $request->validate([
            'name'         => "required|string|max:80|unique:roles,name,{$id}",
            'display_name' => 'required|string|max:100',
        ]);

        $role->update([
            'name'         => $request->name,
            'display_name' => $request->display_name,
            'description'  => $request->description,
        ]);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy($id) {
        $role = Role::findOrFail($id);
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted.');
    }

    public function permissions($id) {
        $role = Role::with('permissions')->findOrFail($id);

        $modules = DB::table('permissions')->distinct()->pluck('module')->sort()->values();
        $actions = ['view', 'create', 'edit', 'delete', 'approve', 'export'];

        // All permissions indexed by module+action
        $all_permissions = Permission::get()->keyBy(fn($p) => $p->module . '.' . $p->action);

        // Role's current permissions
        $assigned = $role->permissions->pluck('id')->flip();

        return view('admin.roles.permissions', compact('role', 'modules', 'actions', 'all_permissions', 'assigned'));
    }

    public function savePermissions(Request $request, $id) {
        $role = Role::findOrFail($id);

        $permission_ids = $request->input('permissions', []);

        DB::table('role_permissions')->where('role_id', $id)->delete();

        if (!empty($permission_ids)) {
            $inserts = array_map(fn($pid) => [
                'role_id'       => $id,
                'permission_id' => $pid,
                'created_at'    => now(),
                'updated_at'    => now(),
            ], $permission_ids);
            DB::table('role_permissions')->insert($inserts);
        }

        DB::table('audit_logs')->insert([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name ?? 'System',
            'action'     => 'update',
            'module'     => 'Role',
            'record_id'  => $id,
            'new_values' => json_encode(['permissions_count' => count($permission_ids)]),
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Permissions saved successfully.');
    }
}
