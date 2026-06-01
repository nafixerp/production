<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model {
    protected $fillable = ['user_id', 'role_id', 'branch_id'];

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
