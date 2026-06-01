<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fssai_compliance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('license_no', 30);
            $table->string('license_type', 80)->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('branch_id')->references('id')->on('branch')->onDelete('set null');
        });
    }
    public function down(): void { Schema::dropIfExists('fssai_compliance'); }
};
