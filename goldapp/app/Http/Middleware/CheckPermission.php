<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckPermission {
    public function handle(Request $request, Closure $next, string $permission) {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Super admin bypasses all permission checks
        if ($user->roleModel && $user->roleModel->name === 'super_admin') {
            return $next($request);
        }

        // Check permission via role_permissions using the user's role_id
        $hasPermission = false;
        if ($user->role_id) {
            $hasPermission = DB::table('role_permissions')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->where('role_permissions.role_id', $user->role_id)
                ->where('permissions.name', $permission)
                ->exists();
        }

        // Also check user_roles pivot table
        if (!$hasPermission) {
            $hasPermission = DB::table('role_permissions')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->join('user_roles', 'role_permissions.role_id', '=', 'user_roles.role_id')
                ->where('user_roles.user_id', $user->id)
                ->where('permissions.name', $permission)
                ->exists();
        }

        if (!$hasPermission) {
            abort(403, 'Access denied. You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
