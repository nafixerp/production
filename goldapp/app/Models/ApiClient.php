<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiClient extends Model
{
    protected $fillable = [
        'name','client_key','client_secret','permissions','rate_limit_per_minute',
        'is_active','last_used_at','created_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'last_used_at' => 'datetime',
    ];

    public function logs()
    {
        return $this->hasMany(ApiLog::class, 'client_id');
    }
}
