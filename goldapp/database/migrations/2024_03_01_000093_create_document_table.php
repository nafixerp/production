<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('document', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->nullable();
            $table->string('name', 180)->nullable();
            $table->string('status', 20)->default('active');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('document'); }
};
