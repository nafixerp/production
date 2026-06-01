<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('recipe_bom', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fg_id')->nullable();
            $table->string('code', 20)->nullable();
            $table->string('name', 180);
            $table->string('version', 20)->default('1.0');
            $table->decimal('yield_qty', 12, 3)->default(0);
            $table->string('yield_unit', 30)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('fg_id')->references('id')->on('finished_goods')->onDelete('set null');
        });

        Schema::create('recipe_bom_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipe_id');
            $table->enum('item_type', ['RM','PM','SFG'])->default('RM');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name', 180)->nullable();
            $table->decimal('qty', 12, 3)->default(0);
            $table->string('unit', 30)->nullable();
            $table->decimal('wastage_pct', 6, 2)->default(0);
            $table->timestamps();
            $table->foreign('recipe_id')->references('id')->on('recipe_bom')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('recipe_bom_detail');
        Schema::dropIfExists('recipe_bom');
    }
};
