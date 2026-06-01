<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDetail extends Model
{
    protected $fillable = [
        'payroll_month_id', 'employee_id', 'employee_name', 'designation',
        'basic', 'hra', 'transport_allowance', 'other_allowance', 'gross_salary',
        'overtime_amount', 'total_earnings',
        'pf_employee', 'pf_employer', 'esi_employee', 'esi_employer', 'tds',
        'other_deductions', 'total_deductions', 'net_salary',
        'working_days', 'present_days', 'leave_days', 'absent_days',
        'status', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function payrollMonth()
    {
        return $this->belongsTo(PayrollMonth::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
