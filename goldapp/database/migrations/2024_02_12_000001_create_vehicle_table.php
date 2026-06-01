<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicle', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();
            $table->string('registration_no', 30)->nullable();
            $table->string('type', 60)->nullable();
            $table->decimal('capacity', 10, 2)->nullable();
            $table->string('driver_name', 120)->nullable();
            $table->string('driver_phone', 30)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('vehicle'); }
};
