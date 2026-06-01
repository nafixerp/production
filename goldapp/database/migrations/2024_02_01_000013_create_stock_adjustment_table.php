<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_adjustment', function (Blueprint $table) {
            $table->id();
            $table->string('slno')->unique();
            $table->date('adj_date');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->string('reason')->nullable();
            $table->text('narration')->nullable();
            $table->enum('status', ['draft','approved'])->default('draft');
            $table->timestamps();
        });
        Schema::create('stock_adjustment_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('adj_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('unit')->nullable();
            $table->decimal('system_qty', 15, 3)->default(0);
            $table->decimal('physical_qty', 15, 3)->default(0);
            $table->decimal('difference', 15, 3)->default(0);
            $table->timestamps();
            $table->foreign('adj_id')->references('id')->on('stock_adjustment')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('stock_adjustment_detail');
        Schema::dropIfExists('stock_adjustment');
    }
};
