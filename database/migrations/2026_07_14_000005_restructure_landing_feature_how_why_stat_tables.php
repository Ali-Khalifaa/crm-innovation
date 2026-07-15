<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('landing_features');
        Schema::dropIfExists('landing_how_it_works');
        Schema::dropIfExists('landing_stats');

        Schema::create('landing_feature_section', function (Blueprint $table) {
            $table->id();
            $table->json('subtitle')->nullable();
            $table->json('headline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_feature_items', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description');
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('sort_order');
        });

        Schema::create('landing_how_section', function (Blueprint $table) {
            $table->id();
            $table->json('subtitle')->nullable();
            $table->json('headline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_how_items', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description');
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('sort_order');
        });

        Schema::create('landing_why_section', function (Blueprint $table) {
            $table->id();
            $table->json('subtitle')->nullable();
            $table->json('headline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_why_items', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description');
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('sort_order');
        });

        Schema::create('landing_stat_section', function (Blueprint $table) {
            $table->id();
            $table->json('subtitle')->nullable();
            $table->json('headline')->nullable();
            $table->string('bg_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_stat_items', function (Blueprint $table) {
            $table->id();
            $table->json('value');
            $table->string('suffix')->default('');
            $table->json('label');
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_stat_items');
        Schema::dropIfExists('landing_stat_section');
        Schema::dropIfExists('landing_why_items');
        Schema::dropIfExists('landing_why_section');
        Schema::dropIfExists('landing_how_items');
        Schema::dropIfExists('landing_how_section');
        Schema::dropIfExists('landing_feature_items');
        Schema::dropIfExists('landing_feature_section');
    }
};
