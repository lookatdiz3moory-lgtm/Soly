<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();            // emoji or icon class
            $table->string('category')->nullable();       // "cosmetic", "restorative", etc.
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('benefits')->nullable();          // ["Pain-free", "Natural look"]
            $table->string('image')->nullable();
            $table->decimal('price_from', 10, 2)->unsigned()->nullable();
            $table->decimal('price_to',   10, 2)->unsigned()->nullable();
            $table->string('price_label')->nullable();     // "From 150 EGP"
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bookable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('slug');
            $table->index(['is_active', 'is_featured', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
