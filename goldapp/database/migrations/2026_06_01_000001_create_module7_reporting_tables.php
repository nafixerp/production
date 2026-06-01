<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 180);
            $table->string('module', 80);
            $table->text('description')->nullable();
            $table->json('query_config')->nullable();
            $table->json('columns_config')->nullable();
            $table->json('filters_config')->nullable();
            $table->tinyInteger('is_system')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('saved_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('name', 180);
            $table->json('parameters')->nullable();
            $table->enum('schedule', ['none', 'daily', 'weekly', 'monthly'])->default('none');
            $table->timestamp('last_run_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('widget_type', 50);
            $table->string('title', 100);
            $table->json('config')->nullable();
            $table->integer('position')->default(0);
            $table->tinyInteger('is_visible')->default(1);
            $table->timestamps();
        });

        Schema::create('kpi_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date');
            $table->string('metric_name', 80);
            $table->decimal('metric_value', 18, 4)->default(0);
            $table->string('metric_unit', 30)->nullable();
            $table->string('period', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('kpi_snapshots');
        Schema::dropIfExists('dashboard_widgets');
        Schema::dropIfExists('saved_reports');
        Schema::dropIfExists('report_templates');
    }
};
