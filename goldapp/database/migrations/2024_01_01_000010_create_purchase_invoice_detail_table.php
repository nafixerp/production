<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('purchase_invoice_detail');
        Schema::create('purchase_invoice_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_invoice_id')->nullable();
            $table->string('slno', 30)->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_code', 30)->nullable();
            $table->string('item_name', 180)->nullable();
            $table->string('hsn_code', 20)->nullable();
            $table->string('unit', 20)->nullable();
            $table->decimal('qty', 18, 4)->default(0);
            $table->decimal('rate', 18, 4)->default(0);
            $table->decimal('amount', 18, 4)->default(0);
            $table->decimal('discount', 18, 4)->default(0);
            $table->decimal('sgst_pct', 6, 2)->default(0);
            $table->decimal('cgst_pct', 6, 2)->default(0);
            $table->decimal('igst_pct', 6, 2)->default(0);
            $table->decimal('sgst', 18, 4)->default(0);
            $table->decimal('cgst', 18, 4)->default(0);
            $table->decimal('igst', 18, 4)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('purchase_invoice_detail'); }
};
