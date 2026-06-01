<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('contract_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('unit')->nullable();
            $table->decimal('rate', 15, 4)->default(0);
            $table->decimal('min_qty', 15, 3)->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->enum('status', ['active','expired','cancelled'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('contract_pricing'); }
};
