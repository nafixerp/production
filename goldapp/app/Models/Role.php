<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    protected $fillable = ['name', 'display_name', 'description', 'is_system'];

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users() {
        return $this->hasMany(UserRole::class);
    }

    public function getUserCountAttribute() {
        return UserRole::where('role_id', $this->id)->count();
    }
}
