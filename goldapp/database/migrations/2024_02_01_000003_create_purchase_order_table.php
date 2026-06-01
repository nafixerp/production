<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->string('slno')->unique();
            $table->string('po_no')->nullable();
            $table->date('po_date');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('payment_terms')->nullable();
            $table->decimal('taxable_amount', 15, 2)->default(0);
            $table->decimal('sgst', 15, 2)->default(0);
            $table->decimal('cgst', 15, 2)->default(0);
            $table->decimal('igst', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->enum('status', ['draft','approved','received','cancelled'])->default('draft');
            $table->text('narration')->nullable();
            $table->timestamps();
        });
        Schema::create('purchase_order_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('hsn_code')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('qty', 15, 3)->default(0);
            $table->decimal('rate', 15, 4)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('sgst_pct', 5, 2)->default(0);
            $table->decimal('cgst_pct', 5, 2)->default(0);
            $table->decimal('igst_pct', 5, 2)->default(0);
            $table->timestamps();
            $table->foreign('po_id')->references('id')->on('purchase_order')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('purchase_order_detail');
        Schema::dropIfExists('purchase_order');
    }
};
