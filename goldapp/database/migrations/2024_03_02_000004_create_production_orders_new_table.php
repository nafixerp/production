<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('po_no', 30)->nullable();
            $table->date('order_date');
            $table->date('planned_start')->nullable();
            $table->date('planned_end')->nullable();
            $table->date('actual_start')->nullable();
            $table->date('actual_end')->nullable();
            $table->unsignedBigInteger('fg_id');
            $table->string('fg_code', 30)->nullable();
            $table->string('fg_name', 180);
            $table->unsignedBigInteger('recipe_id')->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->decimal('order_qty', 18, 4)->default(0);
            $table->decimal('produced_qty', 18, 4)->default(0);
            $table->string('unit', 20)->default('KG');
            $table->enum('status', ['draft', 'released', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->string('cost_centre', 80)->nullable();
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('production_bom', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_order_id');
            $table->enum('item_type', ['RM', 'PM', 'SFG'])->default('RM');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_code', 30)->nullable();
            $table->string('item_name', 180);
            $table->string('unit', 20)->default('KG');
            $table->decimal('required_qty', 18, 4)->default(0);
            $table->decimal('issued_qty', 18, 4)->default(0);
            $table->decimal('wastage_pct', 6, 2)->default(0);
            $table->decimal('actual_wastage', 18, 4)->default(0);
            $table->decimal('cost_rate', 18, 4)->default(0);
            $table->decimal('cost_amount', 18, 2)->default(0);
            $table->string('batch_no', 50)->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->timestamps();
            $table->foreign('production_order_id')->references('id')->on('production_orders')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('production_bom');
        Schema::dropIfExists('production_orders');
    }
};
