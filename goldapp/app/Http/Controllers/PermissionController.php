<?php
namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller {
    private array $modules = [
        'purchases', 'inventory', 'production', 'sales', 'crm',
        'accounts', 'reports', 'settings', 'users', 'hrm',
    ];

    private array $actions = ['view', 'create', 'edit', 'delete', 'approve', 'export'];

    public function index() {
        $permissions = Permission::orderBy('module')->orderBy('action')->get()->groupBy('module');
        return view('admin.permissions.index', compact('permissions'));
    }

    public function create() {
        return view('admin.permissions.form', ['modules' => $this->modules, 'actions' => $this->actions]);
    }

    public function store(Request $request) {
        $request->validate([
            'name'         => 'required|string|max:120|unique:permissions,name',
            'display_name' => 'required|string|max:150',
            'module'       => 'required|string|max:60',
            'action'       => 'required|in:' . implode(',', $this->actions),
        ]);

        Permission::create($request->only('name', 'display_name', 'module', 'action'));

        return redirect()->route('permissions.index')->with('success', 'Permission created.');
    }

    public function show($id) {
        $permission = Permission::findOrFail($id);
        return view('admin.permissions.show', compact('permission'));
    }

    public function edit($id) {
        $permission = Permission::findOrFail($id);
        return view('admin.permissions.form', [
            'permission' => $permission,
            'modules'    => $this->modules,
            'actions'    => $this->actions,
        ]);
    }

    public function update(Request $request, $id) {
        $permission = Permission::findOrFail($id);
        $request->validate([
            'name'         => "required|string|max:120|unique:permissions,name,{$id}",
            'display_name' => 'required|string|max:150',
            'module'       => 'required|string|max:60',
            'action'       => 'required|in:' . implode(',', $this->actions),
        ]);
        $permission->update($request->only('name', 'display_name', 'module', 'action'));
        return redirect()->route('permissions.index')->with('success', 'Permission updated.');
    }

    public function destroy($id) {
        Permission::findOrFail($id)->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted.');
    }

    public function autoGenerate() {
        $created = 0;
        foreach ($this->modules as $module) {
            foreach ($this->actions as $action) {
                $name = "{$module}.{$action}";
                if (!Permission::where('name', $name)->exists()) {
                    Permission::create([
                        'name'         => $name,
                        'display_name' => ucfirst($module) . ' - ' . ucfirst($action),
                        'module'       => $module,
                        'action'       => $action,
                    ]);
                    $created++;
                }
            }
        }
        return back()->with('success', "{$created} permissions auto-generated.");
    }
}
