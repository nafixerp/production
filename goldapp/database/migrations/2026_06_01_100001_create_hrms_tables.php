<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 20)->unique();
            $table->string('name', 150);
            $table->string('designation', 100)->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->date('date_of_joining')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('address')->nullable();
            $table->string('bank_account', 30)->nullable();
            $table->string('bank_ifsc', 20)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->string('aadhaar_number', 20)->nullable();
            $table->string('pf_number', 30)->nullable();
            $table->string('esi_number', 30)->nullable();
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('hra', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('other_allowance', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->boolean('pf_applicable')->default(false);
            $table->boolean('esi_applicable')->default(false);
            $table->boolean('tds_applicable')->default(false);
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->timestamps();
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->decimal('hours_worked', 5, 2)->default(0);
            $table->enum('status', ['present', 'absent', 'half_day', 'leave', 'holiday', 'weekend'])->default('absent');
            $table->decimal('overtime_hours', 5, 2)->default(0);
            $table->string('remarks', 255)->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'date']);
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->enum('leave_type', ['casual', 'sick', 'earned', 'maternity', 'unpaid'])->default('casual');
            $table->date('from_date');
            $table->date('to_date');
            $table->decimal('days', 5, 1)->default(1);
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('break_minutes')->default(30);
            $table->decimal('working_hours', 5, 2)->default(8);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('overtime_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->decimal('hours', 5, 2);
            $table->decimal('rate_per_hour', 8, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        Schema::create('payroll_months', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->enum('status', ['draft', 'processed', 'approved', 'paid'])->default('draft');
            $table->unsignedInteger('total_employees')->default(0);
            $table->decimal('total_gross', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('total_net', 12, 2)->default(0);
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->unique(['month', 'year']);
        });

        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_month_id');
            $table->unsignedBigInteger('employee_id');
            $table->string('employee_name', 150);
            $table->string('designation', 100)->nullable();
            $table->decimal('basic', 10, 2)->default(0);
            $table->decimal('hra', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->decimal('other_allowance', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->decimal('overtime_amount', 10, 2)->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0);
            $table->decimal('pf_employee', 10, 2)->default(0);
            $table->decimal('pf_employer', 10, 2)->default(0);
            $table->decimal('esi_employee', 10, 2)->default(0);
            $table->decimal('esi_employer', 10, 2)->default(0);
            $table->decimal('tds', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->unsignedTinyInteger('working_days')->default(0);
            $table->unsignedTinyInteger('present_days')->default(0);
            $table->unsignedTinyInteger('leave_days')->default(0);
            $table->unsignedTinyInteger('absent_days')->default(0);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->foreign('payroll_month_id')->references('id')->on('payroll_months')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        Schema::create('pf_esi_tds_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_month_id');
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->decimal('pf_employee_total', 12, 2)->default(0);
            $table->decimal('pf_employer_total', 12, 2)->default(0);
            $table->decimal('esi_employee_total', 12, 2)->default(0);
            $table->decimal('esi_employer_total', 12, 2)->default(0);
            $table->decimal('tds_total', 12, 2)->default(0);
            $table->string('challan_number', 50)->nullable();
            $table->date('challan_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
            $table->foreign('payroll_month_id')->references('id')->on('payroll_months')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pf_esi_tds_records');
        Schema::dropIfExists('payroll_details');
        Schema::dropIfExists('payroll_months');
        Schema::dropIfExists('overtime_records');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('leave_applications');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('employees');
    }
};
