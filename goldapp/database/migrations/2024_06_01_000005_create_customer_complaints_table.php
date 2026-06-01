<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customer_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->date('complaint_date');
            $table->unsignedBigInteger('customer_id');
            $table->string('customer_name', 180);
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->enum('complaint_type', ['quality','delivery','billing','service','product','other']);
            $table->string('subject', 255);
            $table->text('description');
            $table->enum('priority', ['low','medium','high','critical'])->default('medium');
            $table->enum('status', ['open','acknowledged','investigating','resolved','closed'])->default('open');
            $table->text('resolution')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->tinyInteger('escalated')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('customer_complaints'); }
};
