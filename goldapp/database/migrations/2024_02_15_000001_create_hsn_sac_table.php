<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hsn_sac', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30);
            $table->text('description')->nullable();
            $table->enum('type', ['HSN','SAC'])->default('HSN');
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('tax_id')->references('id')->on('tax_gst')->onDelete('set null');
        });
    }
    public function down(): void { Schema::dropIfExists('hsn_sac'); }
};
