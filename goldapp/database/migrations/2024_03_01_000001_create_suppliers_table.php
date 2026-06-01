<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 180);
            $table->string('contact_person', 120)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 120)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('state', 80)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('country', 80)->default('India');
            $table->string('gst_no', 20)->nullable();
            $table->string('pan_no', 15)->nullable();
            $table->decimal('credit_limit', 18, 2)->default(0);
            $table->integer('credit_days')->default(0);
            $table->string('payment_terms', 100)->nullable();
            $table->string('bank_name', 120)->nullable();
            $table->string('bank_account', 30)->nullable();
            $table->string('ifsc', 15)->nullable();
            $table->string('currency', 10)->default('INR');
            $table->decimal('rating', 3, 1)->default(0);
            $table->enum('status', ['active', 'inactive', 'blacklisted'])->default('active');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('suppliers'); }
};
