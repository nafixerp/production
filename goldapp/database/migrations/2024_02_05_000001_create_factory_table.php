<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('factory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('code', 20)->nullable();
            $table->string('name', 180);
            $table->text('address')->nullable();
            $table->decimal('capacity', 12, 2)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('branch_id')->references('id')->on('branch')->onDelete('set null');
        });
    }
    public function down(): void { Schema::dropIfExists('factory'); }
};
