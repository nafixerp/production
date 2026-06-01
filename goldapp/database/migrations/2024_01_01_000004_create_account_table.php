<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('account');
        Schema::create('account', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 180);
            $table->unsignedBigInteger('group_id')->nullable();
            $table->string('atype', 50)->nullable();
            $table->decimal('opening_balance', 18, 4)->default(0);
            $table->enum('ob_type', ['dr','cr'])->default('dr');
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('gst', 30)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('account'); }
};
