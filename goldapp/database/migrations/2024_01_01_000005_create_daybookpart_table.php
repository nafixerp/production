<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('daybookpart', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique()->nullable();
            $table->string('vchno', 30)->nullable();
            $table->text('particular')->nullable();
            $table->date('tdate')->nullable();
            $table->string('vtype', 20)->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->string('cheque_no', 50)->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->text('narration')->nullable();
            $table->string('control', 20)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('daybookpart'); }
};
