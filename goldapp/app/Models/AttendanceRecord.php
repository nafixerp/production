<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = [
        'employee_id', 'date', 'in_time', 'out_time', 'hours_worked',
        'status', 'overtime_hours', 'remarks',
    ];

    protected $casts = ['date' => 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
