<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('daybook');
        Schema::create('daybook', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->index();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('account_code', 30)->nullable();
            $table->string('account_name', 180)->nullable();
            $table->decimal('amount', 18, 4)->default(0);
            $table->string('particular', 255)->nullable();
            $table->date('tdate')->nullable()->index();
            $table->string('vtype', 20)->nullable()->index();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();
            $table->index('account_id');
        });
    }
    public function down(): void { Schema::dropIfExists('daybook'); }
};
