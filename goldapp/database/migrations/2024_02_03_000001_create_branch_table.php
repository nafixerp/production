<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('branch', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('code', 20)->nullable();
            $table->string('name', 180);
            $table->text('address')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('state', 80)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('company')->onDelete('set null');
        });
    }
    public function down(): void { Schema::dropIfExists('branch'); }
};
