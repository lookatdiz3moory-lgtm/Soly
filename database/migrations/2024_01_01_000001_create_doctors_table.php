<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();           // e.g. "BDS, MSc"
            $table->string('specialty')->nullable();       // e.g. "Cosmetic Dentistry"
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();           // storage path
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('qualifications')->nullable();    // ["BDS Cairo 2010", ...]
            $table->json('languages')->nullable();         // ["Arabic", "English"]
            $table->json('schedule')->nullable();          // weekly schedule object
            $table->unsignedSmallInteger('slot_duration')->default(30); // minutes
            $table->string('experience')->nullable();      // "10+ Years"
            $table->json('specialties')->nullable();       // ["Implants", "Veneers"]
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
