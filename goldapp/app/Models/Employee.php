<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_code', 'name', 'designation', 'department_id', 'branch_id',
        'date_of_joining', 'date_of_birth', 'gender', 'mobile', 'email', 'address',
        'bank_account', 'bank_ifsc', 'bank_name', 'pan_number', 'aadhaar_number',
        'pf_number', 'esi_number', 'basic_salary', 'hra', 'transport_allowance',
        'other_allowance', 'gross_salary', 'pf_applicable', 'esi_applicable',
        'tds_applicable', 'status',
    ];

    protected $casts = [
        'pf_applicable'  => 'boolean',
        'esi_applicable' => 'boolean',
        'tds_applicable' => 'boolean',
        'date_of_joining' => 'date',
        'date_of_birth'   => 'date',
    ];

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function overtimeRecords()
    {
        return $this->hasMany(OvertimeRecord::class);
    }

    public function payrollDetails()
    {
        return $this->hasMany(PayrollDetail::class);
    }
}
