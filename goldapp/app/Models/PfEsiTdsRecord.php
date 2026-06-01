<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PfEsiTdsRecord extends Model
{
    protected $table = 'pf_esi_tds_records';

    protected $fillable = [
        'payroll_month_id', 'month', 'year',
        'pf_employee_total', 'pf_employer_total',
        'esi_employee_total', 'esi_employer_total', 'tds_total',
        'challan_number', 'challan_date', 'payment_date', 'status',
    ];

    protected $casts = [
        'challan_date' => 'date',
        'payment_date' => 'date',
    ];

    public function payrollMonth()
    {
        return $this->belongsTo(PayrollMonth::class);
    }
}
