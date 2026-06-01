<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cost_centres', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->decimal('budget', 18, 2)->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('cost_centres'); }
};
