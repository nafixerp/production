<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchase_return', function (Blueprint $table) {
            $table->id();
            $table->string('slno')->unique();
            $table->unsignedBigInteger('grn_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('supplier_name')->nullable();
            $table->date('return_date');
            $table->text('reason')->nullable();
            $table->enum('status', ['draft','approved','dispatched'])->default('draft');
            $table->timestamps();
        });
        Schema::create('purchase_return_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('unit')->nullable();
            $table->decimal('qty', 15, 3)->default(0);
            $table->decimal('rate', 15, 4)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
            $table->foreign('return_id')->references('id')->on('purchase_return')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('purchase_return_detail');
        Schema::dropIfExists('purchase_return');
    }
};
