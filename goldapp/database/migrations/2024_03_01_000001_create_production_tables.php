<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('production_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('slno')->nullable();
            $table->date('plan_date');
            $table->date('plan_for_date');
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_name');
            $table->decimal('planned_qty', 12, 3);
            $table->string('unit', 20)->nullable();
            $table->unsignedBigInteger('recipe_id')->nullable();
            $table->enum('status', ['draft','approved','in-progress','done'])->default('draft');
            $table->text('narration')->nullable();
            $table->timestamps();
        });

        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('slno')->nullable();
            $table->string('po_no', 50)->nullable();
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->date('order_date');
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_name');
            $table->decimal('order_qty', 12, 3);
            $table->string('unit', 20)->nullable();
            $table->unsignedBigInteger('recipe_id')->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->enum('status', ['open','in-progress','completed','cancelled'])->default('open');
            $table->text('narration')->nullable();
            $table->timestamps();
        });

        Schema::create('material_issues', function (Blueprint $table) {
            $table->id();
            $table->integer('slno')->nullable();
            $table->date('issue_date');
            $table->unsignedBigInteger('prod_order_id')->nullable();
            $table->string('issued_by', 100)->nullable();
            $table->string('status', 30)->default('draft');
            $table->text('narration')->nullable();
            $table->timestamps();
        });

        Schema::create('material_issue_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('issue_id');
            $table->enum('item_type', ['RM','PM'])->default('RM');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('unit', 20)->nullable();
            $table->decimal('required_qty', 12, 3)->default(0);
            $table->decimal('issued_qty', 12, 3)->default(0);
            $table->string('batch_no', 50)->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->timestamps();
        });

        Schema::create('wip', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_order_id')->nullable();
            $table->string('stage', 50)->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->decimal('qty_in', 12, 3)->default(0);
            $table->decimal('qty_out', 12, 3)->default(0);
            $table->decimal('waste_qty', 12, 3)->default(0);
            $table->string('status', 30)->default('open');
            $table->timestamps();
        });

        Schema::create('production_stage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_order_id')->nullable();
            $table->enum('stage', ['preparation','cooking','cooling','packing']);
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->decimal('temperature', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('open');
            $table->timestamps();
        });

        Schema::create('fg_receipts', function (Blueprint $table) {
            $table->id();
            $table->integer('slno')->nullable();
            $table->date('receipt_date');
            $table->unsignedBigInteger('prod_order_id')->nullable();
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_name');
            $table->decimal('received_qty', 12, 3)->default(0);
            $table->string('unit', 20)->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->date('expiry_date')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->decimal('cost_per_unit', 14, 4)->default(0);
            $table->decimal('total_cost', 14, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('yield_wastages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_order_id')->nullable();
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->decimal('expected_qty', 12, 3)->default(0);
            $table->decimal('actual_qty', 12, 3)->default(0);
            $table->decimal('yield_pct', 8, 4)->default(0);
            $table->decimal('waste_qty', 12, 3)->default(0);
            $table->text('waste_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('byproducts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_order_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->decimal('qty', 12, 3)->default(0);
            $table->string('unit', 20)->nullable();
            $table->string('disposal_type', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('reworks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_order_id')->nullable();
            $table->date('rework_date');
            $table->text('reason')->nullable();
            $table->decimal('qty', 12, 3)->default(0);
            $table->string('status', 30)->default('pending');
            $table->timestamps();
        });

        Schema::create('production_costings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_order_id')->nullable();
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_name')->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->decimal('rm_cost', 14, 4)->default(0);
            $table->decimal('pm_cost', 14, 4)->default(0);
            $table->decimal('labour_cost', 14, 4)->default(0);
            $table->decimal('overhead_cost', 14, 4)->default(0);
            $table->decimal('total_cost', 14, 4)->default(0);
            $table->decimal('cost_per_unit', 14, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('machine_masters', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('type', 100)->nullable();
            $table->string('location', 100)->nullable();
            $table->decimal('capacity', 12, 3)->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();
        });

        Schema::create('machine_utilizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machine_id');
            $table->date('tdate');
            $table->string('shift', 20)->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('produced_qty', 12, 3)->default(0);
            $table->string('status', 30)->default('running');
            $table->timestamps();
        });

        Schema::create('downtime_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machine_id');
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('duration_mins')->default(0);
            $table->text('reason')->nullable();
            $table->string('reported_by', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machine_id');
            $table->date('scheduled_date');
            $table->enum('type', ['preventive','breakdown'])->default('preventive');
            $table->text('description')->nullable();
            $table->string('assigned_to', 100)->nullable();
            $table->date('completed_date')->nullable();
            $table->string('status', 30)->default('scheduled');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('maintenance_schedules');
        Schema::dropIfExists('downtime_logs');
        Schema::dropIfExists('machine_utilizations');
        Schema::dropIfExists('machine_masters');
        Schema::dropIfExists('production_costings');
        Schema::dropIfExists('reworks');
        Schema::dropIfExists('byproducts');
        Schema::dropIfExists('yield_wastages');
        Schema::dropIfExists('fg_receipts');
        Schema::dropIfExists('production_stage_logs');
        Schema::dropIfExists('wip');
        Schema::dropIfExists('material_issue_details');
        Schema::dropIfExists('material_issues');
        Schema::dropIfExists('production_orders');
        Schema::dropIfExists('production_plans');
    }
};
