<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('receipt');
        Schema::create('receipt', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('vch_no', 30)->nullable();
            $table->date('vch_date')->nullable();
            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_name', 180)->nullable();
            $table->decimal('amount', 18, 4)->default(0);
            $table->decimal('discount', 18, 4)->default(0);
            $table->enum('payment_mode', ['cash','bank','cheque','pdc'])->default('cash');
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->string('cheque_no', 50)->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->text('narration')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('receipt'); }
};
