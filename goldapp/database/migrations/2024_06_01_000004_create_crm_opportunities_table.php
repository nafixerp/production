<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_opportunities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('title', 255);
            $table->decimal('value', 18, 2);
            $table->integer('probability')->default(50);
            $table->date('expected_close');
            $table->enum('stage', ['prospect','qualified','proposal','negotiation','closed_won','closed_lost'])->default('prospect');
            $table->json('product_ids')->nullable();
            $table->unsignedBigInteger('assigned_to');
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('crm_opportunities'); }
};
