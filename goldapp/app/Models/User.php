<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'status',
        'role_id', 'branch_id', 'last_login_at', 'is_active', 'avatar'
    ];

    protected $hidden = ['password'];

    protected function casts(): array {
        return [
            'password'      => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function roleModel() {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function userRoles() {
        return $this->hasMany(UserRole::class);
    }

    public function hasPermission(string $permission): bool {
        if (!$this->role_id) return false;
        return \DB::table('role_permissions')
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->where('role_permissions.role_id', $this->role_id)
            ->where('permissions.name', $permission)
            ->exists();
    }
}
