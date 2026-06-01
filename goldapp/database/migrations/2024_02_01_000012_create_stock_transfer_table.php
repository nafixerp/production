<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_transfer', function (Blueprint $table) {
            $table->id();
            $table->string('slno')->unique();
            $table->date('transfer_date');
            $table->unsignedBigInteger('from_warehouse_id')->nullable();
            $table->unsignedBigInteger('to_warehouse_id')->nullable();
            $table->text('narration')->nullable();
            $table->enum('status', ['draft','approved','transferred'])->default('draft');
            $table->timestamps();
        });
        Schema::create('stock_transfer_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('unit')->nullable();
            $table->decimal('qty', 15, 3)->default(0);
            $table->string('batch_no')->nullable();
            $table->timestamps();
            $table->foreign('transfer_id')->references('id')->on('stock_transfer')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('stock_transfer_detail');
        Schema::dropIfExists('stock_transfer');
    }
};
