<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntegrationSetting extends Model
{
    protected $fillable = [
        'integration_type','provider','config','is_active','last_sync_at',
    ];

    protected $casts = [
        'config' => 'array',
        'last_sync_at' => 'datetime',
    ];
}
