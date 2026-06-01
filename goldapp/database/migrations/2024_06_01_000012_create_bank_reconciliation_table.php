<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bank_reconciliation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_account_id');
            $table->date('statement_date');
            $table->decimal('statement_closing_balance', 18, 2)->default(0);
            $table->decimal('book_balance', 18, 2)->default(0);
            $table->decimal('difference', 18, 2)->default(0);
            $table->enum('status', ['in_progress','reconciled'])->default('in_progress');
            $table->unsignedBigInteger('reconciled_by')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bank_recon_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recon_id');
            $table->date('tdate');
            $table->string('description', 255)->nullable();
            $table->decimal('amount', 18, 2);
            $table->enum('dr_cr', ['dr','cr'])->default('cr');
            $table->unsignedBigInteger('daybook_id')->nullable();
            $table->tinyInteger('is_matched')->default(0);
            $table->tinyInteger('statement_line')->default(0);
            $table->timestamps();
            $table->foreign('recon_id')->references('id')->on('bank_reconciliation')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('bank_recon_lines');
        Schema::dropIfExists('bank_reconciliation');
    }
};
