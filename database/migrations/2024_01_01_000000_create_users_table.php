<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Users Table
 *
 * Covers both clinic staff (superadmin, admin, secretary, doctor)
 * and registered patients (role = patient).
 *
 * Timestamp 000000 ensures this runs before all other migrations.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Role-based access
            $table->enum('role', [
                'superadmin',
                'admin',
                'secretary',
                'doctor',
                'patient',
            ])->default('patient');

            // Additional profile fields
            $table->string('phone', 25)->nullable();
            $table->string('avatar')->nullable();   // storage path
            $table->boolean('is_active')->default(true);

            // Session / auth
            $table->rememberToken();
            $table->timestamps();

            // Indexes
            $table->index('role');
            $table->index('is_active');
            $table->index(['role', 'is_active']);
        });

        // Password reset tokens (Laravel convention)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions table (optional — only needed if SESSION_DRIVER=database)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
