<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->string('contact_person', 100)->nullable();
            $table->string('phone', 20);
            $table->string('alt_phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('state', 80)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('country', 50)->default('India');
            $table->string('gst_no', 30)->nullable();
            $table->string('pan_no', 20)->nullable();
            $table->enum('customer_type', ['retail','wholesale','distributor','online','export'])->default('retail');
            $table->decimal('credit_limit', 18, 2)->default(0);
            $table->integer('credit_days')->default(0);
            $table->string('payment_terms', 100)->nullable();
            $table->enum('price_tier', ['standard','tier1','tier2','tier3'])->default('standard');
            $table->decimal('outstanding_balance', 18, 2)->default(0);
            $table->integer('loyalty_points')->default(0);
            $table->unsignedBigInteger('route_id')->nullable();
            $table->unsignedBigInteger('distributor_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->date('dob')->nullable();
            $table->date('anniversary')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('customers'); }
};
