<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();
            $table->string('slno', 30)->unique();
            $table->date('lead_date');
            $table->string('company_name', 180)->nullable();
            $table->string('contact_name', 100);
            $table->string('phone', 20);
            $table->string('email', 150)->nullable();
            $table->string('city', 80);
            $table->enum('source', ['cold_call','referral','website','exhibition','social_media','walk_in','other']);
            $table->text('product_interest')->nullable();
            $table->decimal('estimated_value', 18, 2)->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->enum('status', ['new','contacted','qualified','proposal_sent','negotiating','won','lost'])->default('new');
            $table->enum('priority', ['low','medium','high'])->default('medium');
            $table->text('lost_reason')->nullable();
            $table->date('expected_close_date')->nullable();
            $table->unsignedBigInteger('converted_customer_id')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->text('narration')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('crm_leads'); }
};
