<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('goods_receipt_note', function (Blueprint $table) {
            $table->id();
            $table->string('slno')->unique();
            $table->string('grn_no')->nullable();
            $table->date('grn_date');
            $table->unsignedBigInteger('po_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->enum('status', ['draft','qc_pending','approved','rejected'])->default('draft');
            $table->text('narration')->nullable();
            $table->timestamps();
        });
        Schema::create('grn_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id');
            $table->unsignedBigInteger('po_detail_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('unit')->nullable();
            $table->decimal('ordered_qty', 15, 3)->default(0);
            $table->decimal('received_qty', 15, 3)->default(0);
            $table->decimal('accepted_qty', 15, 3)->default(0);
            $table->decimal('rejected_qty', 15, 3)->default(0);
            $table->decimal('rate', 15, 4)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
            $table->foreign('grn_id')->references('id')->on('goods_receipt_note')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('grn_detail');
        Schema::dropIfExists('goods_receipt_note');
    }
};
