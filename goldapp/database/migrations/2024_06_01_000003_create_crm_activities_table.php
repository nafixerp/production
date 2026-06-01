<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->date('activity_date');
            $table->enum('activity_type', ['call','email','visit','demo','follow_up','whatsapp','meeting']);
            $table->string('subject', 255);
            $table->text('notes');
            $table->text('outcome')->nullable();
            $table->string('next_action', 255)->nullable();
            $table->date('next_action_date')->nullable();
            $table->unsignedBigInteger('done_by');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('crm_activities'); }
};
