<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('journal_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->string('jv_no', 30)->nullable();
            $table->date('jv_date');
            $table->enum('voucher_type', ['JV','CO','DN','CN'])->default('JV');
            $table->text('narration')->nullable();
            $table->decimal('total_debit', 18, 2)->default(0);
            $table->decimal('total_credit', 18, 2)->default(0);
            $table->enum('status', ['draft','approved','posted'])->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('journal_voucher_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jv_id');
            $table->unsignedBigInteger('account_id');
            $table->string('account_code', 30)->nullable();
            $table->string('account_name', 180)->nullable();
            $table->decimal('amount', 18, 4)->comment('negative=Dr positive=Cr');
            $table->string('particular', 255)->nullable();
            $table->string('cost_centre', 80)->nullable();
            $table->timestamps();
            $table->foreign('jv_id')->references('id')->on('journal_vouchers')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('journal_voucher_lines');
        Schema::dropIfExists('journal_vouchers');
    }
};
