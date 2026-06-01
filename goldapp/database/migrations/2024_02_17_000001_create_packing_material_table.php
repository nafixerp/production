<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('packing_material', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();
            $table->string('name', 180);
            $table->string('category', 100)->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('hsn_id')->nullable();
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('reorder_qty', 12, 3)->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('unit_id')->references('id')->on('unit')->onDelete('set null');
            $table->foreign('hsn_id')->references('id')->on('hsn_sac')->onDelete('set null');
            $table->foreign('tax_id')->references('id')->on('tax_gst')->onDelete('set null');
        });
    }
    public function down(): void { Schema::dropIfExists('packing_material'); }
};
