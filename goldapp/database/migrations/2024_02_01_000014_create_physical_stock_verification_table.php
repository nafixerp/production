<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('physical_stock_verification', function (Blueprint $table) {
            $table->id();
            $table->string('slno')->unique();
            $table->date('verify_date');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->string('verified_by')->nullable();
            $table->enum('status', ['draft','in_progress','completed'])->default('draft');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('physical_stock_verification'); }
};
