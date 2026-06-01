<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('production_journal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id');
            $table->date('journal_date');
            $table->enum('stage', ['material_issue', 'wip', 'fg_receipt', 'rework', 'writeoff'])->default('material_issue');
            $table->decimal('qty', 18, 4)->default(0);
            $table->string('unit', 20)->default('KG');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('done_by')->nullable();
            $table->timestamps();
        });

        Schema::create('production_costing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id');
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('fg_name', 180)->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->decimal('order_qty', 18, 4)->default(0);
            $table->decimal('produced_qty', 18, 4)->default(0);
            $table->decimal('rm_cost', 18, 2)->default(0);
            $table->decimal('pm_cost', 18, 2)->default(0);
            $table->decimal('labour_cost', 18, 2)->default(0);
            $table->decimal('overhead_cost', 18, 2)->default(0);
            $table->decimal('total_cost', 18, 2)->default(0);
            $table->decimal('cost_per_unit', 18, 4)->default(0);
            $table->decimal('variance', 18, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('wip_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id');
            $table->string('stage', 50)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->decimal('input_qty', 18, 4)->default(0);
            $table->decimal('output_qty', 18, 4)->default(0);
            $table->decimal('loss_qty', 18, 4)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('wip_tracking');
        Schema::dropIfExists('production_costing');
        Schema::dropIfExists('production_journal');
    }
};
