<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_batch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_type')->nullable();
            $table->string('batch_no')->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('qty', 15, 3)->default(0);
            $table->string('unit')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->decimal('cost_rate', 15, 4)->default(0);
            $table->enum('status', ['active','expired','consumed'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('stock_batch'); }
};
