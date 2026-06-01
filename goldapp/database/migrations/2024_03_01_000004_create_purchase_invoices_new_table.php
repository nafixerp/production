<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('invoice_no', 30)->nullable();
            $table->date('invoice_date');
            $table->unsignedBigInteger('grn_id')->nullable();
            $table->unsignedBigInteger('po_id')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_name', 180);
            $table->string('supplier_bill_no', 50)->nullable();
            $table->date('supplier_bill_date')->nullable();
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('discount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('tcs', 18, 2)->default(0);
            $table->decimal('other_charges', 18, 2)->default(0);
            $table->decimal('round_off', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->decimal('balance_amount', 18, 2)->default(0);
            $table->enum('payment_mode', ['cash', 'bank', 'cheque', 'credit'])->default('credit');
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->string('cheque_no', 50)->nullable();
            $table->date('cheque_date')->nullable();
            $table->enum('status', ['draft', 'posted', 'paid', 'partial', 'cancelled'])->default('draft');
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('item_type', 10)->default('RM');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_code', 30)->nullable();
            $table->string('item_name', 180);
            $table->string('hsn_code', 20)->nullable();
            $table->string('unit', 20)->default('KG');
            $table->decimal('qty', 18, 4)->default(0);
            $table->decimal('rate', 18, 4)->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->timestamps();
            $table->foreign('invoice_id')->references('id')->on('purchase_invoices')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('purchase_invoice_items');
        Schema::dropIfExists('purchase_invoices');
    }
};
