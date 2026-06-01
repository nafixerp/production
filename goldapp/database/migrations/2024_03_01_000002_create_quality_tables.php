<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('qc_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('unit', 30)->nullable();
            $table->decimal('min_value', 12, 4)->nullable();
            $table->decimal('max_value', 12, 4)->nullable();
            $table->timestamps();
        });

        Schema::create('incoming_qcs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('batch_no', 50)->nullable();
            $table->date('test_date');
            $table->string('tested_by', 100)->nullable();
            $table->enum('result', ['pass','fail','partial'])->default('pass');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('inprocess_qcs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_order_id')->nullable();
            $table->string('stage', 50)->nullable();
            $table->date('check_date');
            $table->string('checked_by', 100)->nullable();
            $table->enum('result', ['pass','fail'])->default('pass');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('final_qcs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fg_receipt_id')->nullable();
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->date('test_date');
            $table->string('tested_by', 100)->nullable();
            $table->enum('result', ['pass','fail'])->default('pass');
            $table->date('release_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('sample_ref', 100)->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->date('test_date');
            $table->string('test_type', 100)->nullable();
            $table->string('lab_name', 150)->nullable();
            $table->text('result')->nullable();
            $table->string('report_path', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('sample_retentions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->date('retain_date');
            $table->decimal('retain_qty', 12, 3)->default(0);
            $table->string('unit', 20)->nullable();
            $table->date('retain_till')->nullable();
            $table->string('storage_location', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('haccp_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('process_step', 200);
            $table->enum('hazard_type', ['B','C','P'])->default('B');
            $table->enum('ccp', ['yes','no'])->default('no');
            $table->text('critical_limit')->nullable();
            $table->text('monitoring_procedure')->nullable();
            $table->text('corrective_action')->nullable();
            $table->timestamps();
        });

        Schema::create('batch_traceabilities', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no', 50);
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->unsignedBigInteger('prod_order_id')->nullable();
            $table->timestamps();
        });

        Schema::create('recall_managements', function (Blueprint $table) {
            $table->id();
            $table->integer('slno')->nullable();
            $table->date('recall_date');
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->text('reason')->nullable();
            $table->text('scope')->nullable();
            $table->enum('status', ['initiated','in-progress','completed'])->default('initiated');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('recall_managements');
        Schema::dropIfExists('batch_traceabilities');
        Schema::dropIfExists('haccp_checklists');
        Schema::dropIfExists('sample_retentions');
        Schema::dropIfExists('lab_tests');
        Schema::dropIfExists('final_qcs');
        Schema::dropIfExists('inprocess_qcs');
        Schema::dropIfExists('incoming_qcs');
        Schema::dropIfExists('qc_parameters');
    }
};
