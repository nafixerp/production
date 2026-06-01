<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'name', 'code', 'start_time', 'end_time', 'break_minutes', 'working_hours', 'status',
    ];
}
