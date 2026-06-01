<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->nullable();
            $table->string('name', 180);
            $table->string('contact_person', 120)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('state', 80)->nullable();
            $table->string('gst_no', 30)->nullable();
            $table->string('pan_no', 20)->nullable();
            $table->decimal('credit_limit', 14, 2)->nullable();
            $table->integer('credit_days')->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->unsignedBigInteger('distributor_id')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('route_id')->references('id')->on('route_master')->onDelete('set null');
            $table->foreign('distributor_id')->references('id')->on('distributor')->onDelete('set null');
        });
    }
    public function down(): void { Schema::dropIfExists('customer'); }
};
