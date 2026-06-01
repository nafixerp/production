<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80)->unique();
            $table->string('display_name', 100)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_system')->default(0);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->unique();
            $table->string('display_name', 150)->nullable();
            $table->string('module', 60)->nullable();
            $table->enum('action', ['view', 'create', 'edit', 'delete', 'approve', 'export', 'all']);
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->bigInteger('branch_id')->nullable();
            $table->timestamps();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name', 100)->nullable();
            $table->enum('action', ['login', 'logout', 'create', 'update', 'delete', 'view', 'export', 'approve']);
            $table->string('module', 60)->nullable();
            $table->bigInteger('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();
        });

        // Add columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('role_id')->nullable()->after('password');
            $table->bigInteger('branch_id')->nullable()->after('role_id');
            $table->timestamp('last_login_at')->nullable()->after('branch_id');
            $table->tinyInteger('is_active')->default(1)->after('last_login_at');
            $table->string('avatar', 255)->nullable()->after('is_active');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role_id', 'branch_id', 'last_login_at', 'is_active', 'avatar']);
        });
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
