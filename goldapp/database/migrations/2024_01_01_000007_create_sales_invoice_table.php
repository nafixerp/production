<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('sales_invoice_detail');
        Schema::dropIfExists('sales_invoice');
        Schema::create('sales_invoice', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('invoice_no', 30)->nullable();
            $table->date('invoice_date')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 180)->nullable();
            $table->decimal('taxable_amount', 18, 4)->default(0);
            $table->decimal('discount', 18, 4)->default(0);
            $table->decimal('sgst', 18, 4)->default(0);
            $table->decimal('cgst', 18, 4)->default(0);
            $table->decimal('igst', 18, 4)->default(0);
            $table->decimal('other_charges', 18, 4)->default(0);
            $table->decimal('round_off', 18, 4)->default(0);
            $table->decimal('net_amount', 18, 4)->default(0);
            $table->decimal('received_amount', 18, 4)->default(0);
            $table->enum('payment_mode', ['cash','bank','credit','cheque'])->default('credit');
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->string('cheque_no', 50)->nullable();
            $table->date('cheque_date')->nullable();
            $table->text('narration')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sales_invoice_detail');
        Schema::dropIfExists('sales_invoice');
    }
};
