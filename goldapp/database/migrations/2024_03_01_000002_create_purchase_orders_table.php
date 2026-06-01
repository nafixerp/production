<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('po_no', 30)->nullable();
            $table->date('po_date');
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_name', 180);
            $table->date('delivery_date')->nullable();
            $table->string('currency', 10)->default('INR');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->string('payment_terms', 100)->nullable();
            $table->text('shipping_address')->nullable();
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('tcs', 18, 2)->default(0);
            $table->decimal('round_off', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'partially_received', 'received', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id');
            $table->enum('item_type', ['RM', 'PM', 'FG'])->default('RM');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_code', 30)->nullable();
            $table->string('item_name', 180);
            $table->string('hsn_code', 20)->nullable();
            $table->string('unit', 20)->default('KG');
            $table->decimal('qty', 18, 4)->default(0);
            $table->decimal('pending_qty', 18, 4)->default(0);
            $table->decimal('rate', 18, 4)->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            $table->decimal('discount_pct', 6, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('sgst_pct', 6, 2)->default(0);
            $table->decimal('cgst_pct', 6, 2)->default(0);
            $table->decimal('igst_pct', 6, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->timestamps();
            $table->foreign('po_id')->references('id')->on('purchase_orders')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
