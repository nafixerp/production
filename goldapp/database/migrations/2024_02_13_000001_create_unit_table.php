<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();
            $table->string('name', 80);
            $table->string('base_unit', 80)->nullable();
            $table->decimal('conversion_factor', 14, 6)->default(1);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('unit'); }
};
