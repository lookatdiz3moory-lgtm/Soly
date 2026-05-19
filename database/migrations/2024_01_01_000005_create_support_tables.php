<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Offers ────────────────────────────────────────────────
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('original_price', 10, 2)->unsigned();
            $table->decimal('offer_price',    10, 2)->unsigned();
            $table->string('badge_text', 50)->nullable();   // "Limited Time"
            $table->text('terms')->nullable();
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'expires_at']);
        });

        // ── Testimonials ──────────────────────────────────────────
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('title')->nullable();
            $table->text('review');
            $table->string('service')->nullable();
            $table->enum('source', ['internal', 'google', 'facebook', 'whatsapp'])->default('internal');
            $table->string('google_review_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'is_approved', 'is_featured']);
        });

        // ── Gallery ───────────────────────────────────────────────
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['before_after', 'general', 'clinic', 'team'])->default('general');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('before_image')->nullable();
            $table->string('after_image')->nullable();
            $table->string('image')->nullable();
            $table->string('treatment')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active', 'sort_order']);
        });

        // ── FAQs ──────────────────────────────────────────────────
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('general');
            $table->text('question');
            $table->text('question_ar')->nullable();
            $table->longText('answer');
            $table->longText('answer_ar')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'is_featured', 'sort_order']);
        });

        // ── Contact Messages ──────────────────────────────────────
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 25)->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->enum('status', ['new', 'read', 'replied', 'archived'])->default('new');
            $table->unsignedBigInteger('replied_by')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->text('reply_message')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // ── Blocked Slots ─────────────────────────────────────────
        Schema::create('blocked_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('blocked_date');
            $table->time('slot_start')->nullable();
            $table->time('slot_end')->nullable();
            $table->boolean('is_full_day')->default(false);
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'blocked_date']);
        });

        // ── Notifications ─────────────────────────────────────────
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable(); // null = broadcast
            $table->string('type');
            $table->string('title');
            $table->text('message')->nullable();
            $table->json('data')->nullable();
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['admin_id', 'is_read']);
        });

        // ── Settings ──────────────────────────────────────────────
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->enum('type', ['text', 'number', 'boolean', 'json', 'html'])->default('text');
            $table->string('group')->default('general');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('blocked_slots');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('offers');
    }
};
