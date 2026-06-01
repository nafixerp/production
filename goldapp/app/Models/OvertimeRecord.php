<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeRecord extends Model
{
    protected $fillable = [
        'employee_id', 'date', 'hours', 'rate_per_hour', 'amount', 'status', 'approved_by',
    ];

    protected $casts = ['date' => 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
