<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->enum('account_group', ['asset','liability','equity','income','expense','tax'])->index();
            $table->string('account_type', 50)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->tinyInteger('is_control_account')->default(0);
            $table->string('currency', 10)->default('INR');
            $table->decimal('opening_balance', 18, 4)->default(0);
            $table->enum('ob_type', ['dr','cr'])->default('dr');
            $table->tinyInteger('is_bank_account')->default(0);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_no', 50)->nullable();
            $table->string('ifsc', 20)->nullable();
            $table->tinyInteger('allow_direct_posting')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('chart_of_accounts'); }
};
