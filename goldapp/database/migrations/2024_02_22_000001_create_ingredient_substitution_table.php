<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ingredient_substitution', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipe_id');
            $table->unsignedBigInteger('original_item_id')->nullable();
            $table->unsignedBigInteger('substitute_item_id')->nullable();
            $table->decimal('substitution_ratio', 8, 4)->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->foreign('recipe_id')->references('id')->on('recipe_bom')->onDelete('cascade');
        });
    }
    public function down(): void { Schema::dropIfExists('ingredient_substitution'); }
};
