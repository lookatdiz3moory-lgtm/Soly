<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique(); // SDC-2024XXXXX

            // Relations
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('offer_id')->nullable()->constrained('offers')->nullOnDelete();

            // Patient snapshot (preserved even if user record changes)
            $table->string('patient_name');
            $table->string('patient_phone', 25);
            $table->string('patient_email')->nullable();

            // Scheduling
            $table->date('appointment_date');
            $table->time('slot_start');
            $table->time('slot_end');

            // Status workflow
            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'completed',
                'no_show',
                'rescheduled',
            ])->default('pending');

            $table->enum('source', [
                'website', 'whatsapp', 'phone', 'walkin', 'admin',
            ])->default('website');

            // Notes
            $table->text('notes')->nullable();        // patient-facing
            $table->text('admin_notes')->nullable();  // internal only

            // Admin tracking
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();

            // Pricing
            $table->decimal('price_quoted', 10, 2)->unsigned()->nullable();
            $table->decimal('price_paid',   10, 2)->unsigned()->nullable();
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'card', 'online'])->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index('reference');
            $table->index('patient_phone');
            $table->index('appointment_date');
            $table->index(['appointment_date', 'doctor_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
