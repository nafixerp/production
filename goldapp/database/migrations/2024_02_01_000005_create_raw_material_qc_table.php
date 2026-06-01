<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('raw_material_qc', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('batch_no')->nullable();
            $table->date('test_date');
            $table->string('tested_by')->nullable();
            $table->enum('result', ['pass','fail','partial'])->default('pass');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('raw_material_qc');
    }
};
