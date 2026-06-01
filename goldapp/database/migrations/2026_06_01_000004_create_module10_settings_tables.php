<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 80)->unique();
            $table->text('setting_value')->nullable();
            $table->enum('setting_type', ['string','number','boolean','json','file'])->default('string');
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('company_profile', function (Blueprint $table) {
            $table->id();
            $table->string('name', 180);
            $table->string('legal_name', 180)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 80)->nullable();
            $table->string('state', 80)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('country', 80)->default('India');
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('gst_no', 30)->nullable();
            $table->string('pan_no', 20)->nullable();
            $table->string('cin_no', 30)->nullable();
            $table->string('fssai_no', 50)->nullable();
            $table->string('logo_path', 255)->nullable();
            $table->tinyInteger('financial_year_start')->default(4);
            $table->string('currency', 10)->default('INR');
            $table->string('currency_symbol', 5)->default('₹');
            $table->string('date_format', 20)->default('d/m/Y');
            $table->tinyInteger('decimal_places')->default(2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });

        Schema::create('financial_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20);
            $table->date('from_date');
            $table->date('to_date');
            $table->tinyInteger('is_current')->default(0);
            $table->tinyInteger('is_locked')->default(0);
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sequence_configs', function (Blueprint $table) {
            $table->id();
            $table->string('module', 60);
            $table->string('prefix', 20);
            $table->string('suffix', 20)->nullable();
            $table->integer('current_seq')->default(0);
            $table->enum('reset_cycle', ['never','yearly','monthly','daily'])->default('yearly');
            $table->string('format', 50)->default('{PREFIX}/{YY}/{SEQ4}');
            $table->tinyInteger('branch_specific')->default(0);
            $table->timestamps();
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('subject', 255);
            $table->text('body');
            $table->text('variables_list')->nullable();
            $table->string('module', 60);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('sms_whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->enum('channel', ['sms','whatsapp']);
            $table->string('template_id', 80)->nullable();
            $table->text('message');
            $table->text('variables_list')->nullable();
            $table->string('module', 60);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('backup_type', ['full','incremental','manual']);
            $table->string('file_name', 255);
            $table->decimal('file_size_mb', 10, 2)->default(0);
            $table->enum('status', ['running','completed','failed'])->default('running');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('module', 60);
            $table->string('event_type', 60);
            $table->tinyInteger('email')->default(1);
            $table->tinyInteger('sms')->default(0);
            $table->tinyInteger('push')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('backup_logs');
        Schema::dropIfExists('sms_whatsapp_templates');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('sequence_configs');
        Schema::dropIfExists('financial_years');
        Schema::dropIfExists('company_profile');
        Schema::dropIfExists('company_settings');
    }
};
