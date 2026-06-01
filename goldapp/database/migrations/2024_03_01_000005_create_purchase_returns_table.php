<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('return_no', 30)->nullable();
            $table->date('return_date');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->unsignedBigInteger('grn_id')->nullable();
            $table->unsignedBigInteger('supplier_id');
            $table->string('supplier_name', 180);
            $table->text('reason')->nullable();
            $table->decimal('taxable_amount', 18, 2)->default(0);
            $table->decimal('sgst', 18, 2)->default(0);
            $table->decimal('cgst', 18, 2)->default(0);
            $table->decimal('igst', 18, 2)->default(0);
            $table->decimal('net_amount', 18, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'completed'])->default('draft');
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->string('item_type', 10)->default('RM');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_code', 30)->nullable();
            $table->string('item_name', 180);
            $table->string('unit', 20)->default('KG');
            $table->decimal('qty', 18, 4)->default(0);
            $table->decimal('rate', 18, 4)->default(0);
            $table->decimal('amount', 18, 2)->default(0);
            $table->string('batch_no', 50)->nullable();
            $table->timestamps();
            $table->foreign('return_id')->references('id')->on('purchase_returns')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('purchase_return_items');
        Schema::dropIfExists('purchase_returns');
    }
};
