<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 180);
            $table->string('category', 80)->nullable();
            $table->string('sub_category', 80)->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('hsn_code', 20)->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('reorder_qty', 18, 4)->default(0);
            $table->decimal('min_stock', 18, 4)->default(0);
            $table->decimal('max_stock', 18, 4)->default(0);
            $table->integer('lead_time_days')->default(0);
            $table->integer('shelf_life_days')->nullable();
            $table->string('storage_temp', 50)->nullable();
            $table->json('allergen_ids')->nullable();
            $table->tinyInteger('is_fssai_regulated')->default(0);
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('raw_materials'); }
};
