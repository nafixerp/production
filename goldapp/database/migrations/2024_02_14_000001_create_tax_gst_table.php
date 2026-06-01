<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tax_gst', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();
            $table->string('name', 80);
            $table->decimal('sgst_pct', 6, 2)->default(0);
            $table->decimal('cgst_pct', 6, 2)->default(0);
            $table->decimal('igst_pct', 6, 2)->default(0);
            $table->decimal('cess_pct', 6, 2)->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tax_gst'); }
};
