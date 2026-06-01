<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('purchase_requisition', function (Blueprint $table) {
            $table->id();
            $table->string('slno')->unique();
            $table->date('req_date');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('requested_by')->nullable();
            $table->enum('status', ['draft','approved','ordered'])->default('draft');
            $table->text('narration')->nullable();
            $table->timestamps();
        });
        Schema::create('purchase_requisition_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('req_id');
            $table->string('item_type')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('unit')->nullable();
            $table->decimal('qty_required', 15, 3)->default(0);
            $table->decimal('qty_approved', 15, 3)->default(0);
            $table->timestamps();
            $table->foreign('req_id')->references('id')->on('purchase_requisition')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('purchase_requisition_detail');
        Schema::dropIfExists('purchase_requisition');
    }
};
