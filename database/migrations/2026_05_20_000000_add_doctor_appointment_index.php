<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add a [doctor_id, appointment_date] composite index.
 *
 * Doctor::availableSlots() filters by doctor_id first, then by
 * appointment_date. The existing [appointment_date, doctor_id] index
 * only helps when the leading column (appointment_date) is in the
 * WHERE clause. Adding the reverse composite gives the planner an
 * index it can use when the doctor filter comes first.
 *
 * Safe to run on SQLite and MySQL — both support CREATE INDEX on
 * existing columns. Down migration drops the index by name.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['doctor_id', 'appointment_date'], 'appointments_doctor_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('appointments_doctor_date_index');
        });
    }
};
