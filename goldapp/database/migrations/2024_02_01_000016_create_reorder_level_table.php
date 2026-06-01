<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reorder_level', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_type')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->decimal('reorder_qty', 15, 3)->default(0);
            $table->decimal('reorder_point', 15, 3)->default(0);
            $table->decimal('max_stock', 15, 3)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('reorder_level'); }
};
