<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tax_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period_name', 50);
            $table->date('from_date');
            $table->date('to_date');
            $table->enum('tax_type', ['GSTR1','GSTR3B','TDS','TCS']);
            $table->enum('status', ['open','filed','amended'])->default('open');
            $table->unsignedBigInteger('filed_by')->nullable();
            $table->timestamp('filed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tax_periods'); }
};
