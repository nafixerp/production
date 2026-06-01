<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customer_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('fg_id');
            $table->enum('price_type', ['fixed','discount_pct'])->default('fixed');
            $table->decimal('price', 18, 4)->nullable();
            $table->decimal('discount_pct', 6, 2)->nullable();
            $table->decimal('min_qty', 18, 4)->default(1);
            $table->date('valid_from');
            $table->date('valid_to')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('customer_pricing'); }
};
