<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ap_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->date('tdate');
            $table->string('slno', 30)->nullable();
            $table->string('vtype', 20)->nullable();
            $table->string('ref_no', 50)->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);
            $table->decimal('balance', 18, 2)->default(0);
            $table->text('narration')->nullable();
            $table->timestamps();
            $table->index(['supplier_id', 'tdate']);
        });
    }
    public function down(): void { Schema::dropIfExists('ap_ledger'); }
};
