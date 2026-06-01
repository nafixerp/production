<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('grn', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('grn_no', 30)->nullable();
            $table->date('grn_date');
            $table->unsignedBigInteger('po_id')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_name', 180);
            $table->string('supplier_invoice_no', 50)->nullable();
            $table->date('supplier_invoice_date')->nullable();
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->enum('status', ['draft', 'qc_pending', 'approved', 'rejected'])->default('draft');
            $table->enum('qc_result', ['pass', 'fail', 'partial'])->nullable();
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id');
            $table->unsignedBigInteger('po_item_id')->nullable();
            $table->string('item_type', 10)->default('RM');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_code', 30)->nullable();
            $table->string('item_name', 180);
            $table->string('unit', 20)->default('KG');
            $table->decimal('ordered_qty', 18, 4)->default(0);
            $table->decimal('received_qty', 18, 4)->default(0);
            $table->decimal('accepted_qty', 18, 4)->default(0);
            $table->decimal('rejected_qty', 18, 4)->default(0);
            $table->decimal('rate', 18, 4)->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('batch_no', 50)->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->timestamps();
            $table->foreign('grn_id')->references('id')->on('grn')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('grn_items');
        Schema::dropIfExists('grn');
    }
};
