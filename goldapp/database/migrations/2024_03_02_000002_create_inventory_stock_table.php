<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_stock', function (Blueprint $table) {
            $table->id();
            $table->enum('item_type', ['RM', 'PM', 'FG', 'SFG'])->default('RM');
            $table->unsignedBigInteger('item_id');
            $table->string('item_code', 30)->nullable();
            $table->string('item_name', 180);
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->string('batch_no', 50)->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('qty_in', 18, 4)->default(0);
            $table->decimal('qty_out', 18, 4)->default(0);
            $table->decimal('qty_reserved', 18, 4)->default(0);
            $table->string('unit', 20)->default('KG');
            $table->decimal('cost_rate', 18, 4)->default(0);
            $table->enum('status', ['available', 'expired', 'on_hold', 'consumed'])->default('available');
            $table->timestamps();
            $table->index(['item_type', 'item_id', 'warehouse_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('inventory_stock'); }
};
