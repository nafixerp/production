<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('code', 20)->nullable();
            $table->string('name', 180);
            $table->string('designation', 100)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->date('dob')->nullable();
            $table->date('doj')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('department_id')->references('id')->on('department')->onDelete('set null');
            $table->foreign('branch_id')->references('id')->on('branch')->onDelete('set null');
        });
    }
    public function down(): void { Schema::dropIfExists('staff'); }
};
