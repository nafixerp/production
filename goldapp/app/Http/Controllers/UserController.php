<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller {
    public function index() {
        $users = User::with('roleModel')
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create() {
        $roles = Role::orderBy('display_name')->get();
        return view('admin.users.form', compact('roles'));
    }

    public function store(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id'  => 'nullable|exists:roles,id',
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role_id'   => $request->role_id,
            'branch_id' => $request->branch_id,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        if ($request->role_id) {
            UserRole::create([
                'user_id'   => $user->id,
                'role_id'   => $request->role_id,
                'branch_id' => $request->branch_id,
            ]);
        }

        DB::table('audit_logs')->insert([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name ?? 'System',
            'action'     => 'create',
            'module'     => 'User',
            'record_id'  => $user->id,
            'new_values' => json_encode(['name' => $user->name, 'email' => $user->email]),
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show($id) {
        $user = User::with('roleModel')->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit($id) {
        $user  = User::findOrFail($id);
        $roles = Role::orderBy('display_name')->get();
        return view('admin.users.form', compact('user', 'roles'));
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => "required|email|unique:users,email,{$id}",
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $old = $user->toArray();

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'role_id'   => $request->role_id,
            'branch_id' => $request->branch_id,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        if ($request->role_id) {
            UserRole::updateOrCreate(
                ['user_id' => $user->id],
                ['role_id' => $request->role_id, 'branch_id' => $request->branch_id]
            );
        }

        DB::table('audit_logs')->insert([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name ?? 'System',
            'action'     => 'update',
            'module'     => 'User',
            'record_id'  => $user->id,
            'old_values' => json_encode($old),
            'new_values' => json_encode($user->toArray()),
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->update(['is_active' => 0]);

        DB::table('audit_logs')->insert([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name ?? 'System',
            'action'     => 'delete',
            'module'     => 'User',
            'record_id'  => $user->id,
            'old_values' => json_encode(['name' => $user->name, 'email' => $user->email]),
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'User deactivated successfully.');
    }

    public function resetPassword(Request $request, $id) {
        $user = User::findOrFail($id);

        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user->update(['password' => Hash::make($request->new_password)]);

        DB::table('audit_logs')->insert([
            'user_id'    => auth()->id(),
            'user_name'  => auth()->user()->name ?? 'System',
            'action'     => 'update',
            'module'     => 'User',
            'record_id'  => $user->id,
            'new_values' => json_encode(['action' => 'password_reset', 'target_user' => $user->name]),
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'Password reset successfully.');
    }

    public function impersonate(Request $request, $id) {
        $target = User::findOrFail($id);

        if (!auth()->user()->roleModel || auth()->user()->roleModel->name !== 'super_admin') {
            abort(403, 'Only super admins can impersonate users.');
        }

        $request->session()->put('impersonating', auth()->id());
        auth()->login($target);

        return redirect('/')->with('info', "Now acting as {$target->name}.");
    }
}
