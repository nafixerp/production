<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vendor_payment', function (Blueprint $table) {
            $table->id();
            $table->string('slno')->unique();
            $table->string('vch_no')->nullable();
            $table->date('vch_date');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('payment_mode')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            $table->text('narration')->nullable();
            $table->enum('status', ['draft','approved','cleared'])->default('draft');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('vendor_payment');
    }
};
