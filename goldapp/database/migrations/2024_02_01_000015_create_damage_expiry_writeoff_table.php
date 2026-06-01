<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('damage_expiry_writeoff', function (Blueprint $table) {
            $table->id();
            $table->string('slno')->unique();
            $table->date('writeoff_date');
            $table->enum('reason', ['damage','expiry'])->default('damage');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('batch_no')->nullable();
            $table->decimal('qty', 15, 3)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('rate', 15, 4)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->text('narration')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('damage_expiry_writeoff'); }
};
