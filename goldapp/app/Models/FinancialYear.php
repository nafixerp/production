<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialYear extends Model
{
    protected $fillable = [
        'name','from_date','to_date','is_current','is_locked','locked_by','locked_at',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'locked_at' => 'datetime',
    ];
}
