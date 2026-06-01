<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_type')->nullable();
            $table->date('tdate');
            $table->string('ref_no')->nullable();
            $table->string('vtype')->nullable();
            $table->decimal('in_qty', 15, 3)->default(0);
            $table->decimal('out_qty', 15, 3)->default(0);
            $table->decimal('balance_qty', 15, 3)->default(0);
            $table->decimal('rate', 15, 4)->default(0);
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->string('batch_no')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('stock_ledger'); }
};
