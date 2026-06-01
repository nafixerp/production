<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('semi_finished_goods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();
            $table->string('name', 180);
            $table->string('category', 100)->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('unit_id')->references('id')->on('unit')->onDelete('set null');
        });
    }
    public function down(): void { Schema::dropIfExists('semi_finished_goods'); }
};
