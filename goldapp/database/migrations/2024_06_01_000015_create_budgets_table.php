<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cost_centre_id')->nullable();
            $table->unsignedBigInteger('account_id');
            $table->string('account_name', 180)->nullable();
            $table->string('financial_year', 10);
            $table->tinyInteger('month');
            $table->decimal('budgeted_amount', 18, 2)->default(0);
            $table->decimal('actual_amount', 18, 2)->default(0);
            $table->decimal('variance', 18, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('budgets'); }
};
