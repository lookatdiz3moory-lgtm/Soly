<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'short_description_ar')) {
                $table->text('short_description_ar')->nullable()->after('short_description');
            }
            if (!Schema::hasColumn('services', 'description_ar')) {
                $table->longText('description_ar')->nullable()->after('description');
            }
        });

        Schema::table('doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('doctors', 'name_ar')) {
                $table->string('name_ar', 120)->nullable()->after('name');
            }
            if (!Schema::hasColumn('doctors', 'specialty_ar')) {
                $table->string('specialty_ar', 120)->nullable()->after('specialty');
            }
            if (!Schema::hasColumn('doctors', 'bio_ar')) {
                $table->text('bio_ar')->nullable()->after('bio');
            }
        });

        Schema::table('testimonials', function (Blueprint $table) {
            if (!Schema::hasColumn('testimonials', 'name_ar')) {
                $table->string('name_ar', 120)->nullable()->after('name');
            }
            if (!Schema::hasColumn('testimonials', 'review_ar')) {
                $table->text('review_ar')->nullable()->after('review');
            }
        });

        // faqs.question_ar and faqs.answer_ar already exist (initial migration).
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            foreach (['short_description_ar', 'description_ar'] as $col) {
                if (Schema::hasColumn('services', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('doctors', function (Blueprint $table) {
            foreach (['name_ar', 'specialty_ar', 'bio_ar'] as $col) {
                if (Schema::hasColumn('doctors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('testimonials', function (Blueprint $table) {
            foreach (['name_ar', 'review_ar'] as $col) {
                if (Schema::hasColumn('testimonials', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
