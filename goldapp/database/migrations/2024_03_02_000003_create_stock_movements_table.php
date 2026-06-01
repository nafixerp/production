<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->date('movement_date');
            $table->string('item_type', 10)->default('RM');
            $table->unsignedBigInteger('item_id');
            $table->string('item_code', 30)->nullable();
            $table->string('item_name', 180);
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->enum('movement_type', ['IN', 'OUT', 'TRANSFER', 'ADJUSTMENT', 'RETURN', 'WRITEOFF'])->default('IN');
            $table->string('ref_type', 30)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();
            $table->decimal('qty', 18, 4)->default(0);
            $table->string('unit', 20)->default('KG');
            $table->decimal('cost_rate', 18, 4)->default(0);
            $table->decimal('cost_amount', 18, 2)->default(0);
            $table->decimal('running_balance', 18, 4)->default(0);
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['item_type', 'item_id', 'movement_date']);
        });
    }
    public function down(): void { Schema::dropIfExists('stock_movements'); }
};
