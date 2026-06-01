<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('nutritional_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('item_type', 30)->default('FG');
            $table->decimal('per_100g_calories', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('carbs', 8, 2)->default(0);
            $table->decimal('fat', 8, 2)->default(0);
            $table->decimal('fiber', 8, 2)->default(0);
            $table->decimal('sodium', 8, 2)->default(0);
            $table->text('allergens_text')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('nutritional_info'); }
};
