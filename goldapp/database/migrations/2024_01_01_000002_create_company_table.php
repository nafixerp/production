<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('company', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();
            $table->string('name', 180)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('gst', 30)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('company'); }
};
