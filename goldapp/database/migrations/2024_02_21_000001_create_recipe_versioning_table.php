<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('recipe_versioning', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipe_id');
            $table->string('version', 20);
            $table->text('change_notes')->nullable();
            $table->string('changed_by', 120)->nullable();
            $table->timestamps();
            $table->foreign('recipe_id')->references('id')->on('recipe_bom')->onDelete('cascade');
        });
    }
    public function down(): void { Schema::dropIfExists('recipe_versioning'); }
};
