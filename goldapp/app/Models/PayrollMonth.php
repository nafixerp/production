<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollMonth extends Model
{
    protected $fillable = [
        'month', 'year', 'status', 'total_employees', 'total_gross',
        'total_deductions', 'total_net', 'processed_by', 'processed_at',
    ];

    protected $casts = ['processed_at' => 'datetime'];

    public function details()
    {
        return $this->hasMany(PayrollDetail::class);
    }

    public function pfEsiTds()
    {
        return $this->hasOne(PfEsiTdsRecord::class);
    }

    public function getMonthNameAttribute(): string
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }
}
