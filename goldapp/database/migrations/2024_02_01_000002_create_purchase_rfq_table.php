<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchase_rfq', function (Blueprint $table) {
            $table->id();
            $table->string('slno')->unique();
            $table->unsignedBigInteger('req_id')->nullable();
            $table->date('rfq_date');
            $table->date('due_date')->nullable();
            $table->text('narration')->nullable();
            $table->enum('status', ['draft','sent','received','closed'])->default('draft');
            $table->timestamps();
        });
        Schema::create('purchase_rfq_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rfq_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('unit')->nullable();
            $table->decimal('qty', 15, 3)->default(0);
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('quoted_rate', 15, 4)->default(0);
            $table->timestamps();
            $table->foreign('rfq_id')->references('id')->on('purchase_rfq')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('purchase_rfq_detail');
        Schema::dropIfExists('purchase_rfq');
    }
};
