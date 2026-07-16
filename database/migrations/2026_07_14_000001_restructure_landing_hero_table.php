<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_hero', function (Blueprint $table) {
            $table->dropColumn([
                'title_en',
                'title_ar',
                'subtitle_en',
                'subtitle_ar',
                'description_en',
                'description_ar',
                'btn_primary_text_en',
                'btn_primary_text_ar',
                'btn_primary_url',
                'btn_secondary_text_en',
                'btn_secondary_text_ar',
                'btn_secondary_url',
                'image',
            ]);
        });

        Schema::table('landing_hero', function (Blueprint $table) {
            $table->json('subtitle')->nullable()->after('id');
            $table->json('headline')->nullable()->after('subtitle');
            $table->json('description')->nullable()->after('headline');
            $table->json('btn_primary')->nullable()->after('description');
            $table->json('btn_secondary')->nullable()->after('btn_primary');
            $table->string('bg_image')->nullable()->after('btn_secondary');
            $table->string('bg_overlay_image')->nullable()->after('bg_image');
        });
    }

    public function down(): void
    {
        Schema::table('landing_hero', function (Blueprint $table) {
            $table->dropColumn([
                'subtitle',
                'headline',
                'description',
                'btn_primary',
                'btn_secondary',
                'bg_image',
                'bg_overlay_image',
            ]);
        });

        Schema::table('landing_hero', function (Blueprint $table) {
            $table->string('title_en')->after('id');
            $table->string('title_ar')->after('title_en');
            $table->string('subtitle_en')->nullable();
            $table->string('subtitle_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('btn_primary_text_en')->nullable();
            $table->string('btn_primary_text_ar')->nullable();
            $table->string('btn_primary_url')->nullable();
            $table->string('btn_secondary_text_en')->nullable();
            $table->string('btn_secondary_text_ar')->nullable();
            $table->string('btn_secondary_url')->nullable();
            $table->string('image')->nullable();
        });
    }
};
