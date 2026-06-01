<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'client_id','endpoint','method','request_body','response_code',
        'response_time_ms','ip_address','created_at',
    ];

    protected $casts = [
        'request_body' => 'array',
        'created_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(ApiClient::class, 'client_id');
    }
}
