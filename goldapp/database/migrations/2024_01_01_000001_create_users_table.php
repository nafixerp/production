<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('email', 150)->unique()->nullable();
            $table->string('password', 255);
            $table->string('role', 50)->default('user');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};
