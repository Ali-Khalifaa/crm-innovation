<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_solution_section', function (Blueprint $table) {
            $table->id();
            $table->json('subtitle')->nullable();
            $table->json('headline')->nullable();
            $table->json('description')->nullable();
            $table->json('button')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_solution_slides', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('focus_position')->default('center');
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('sort_order');
        });

        Schema::create('landing_solution_points', function (Blueprint $table) {
            $table->id();
            $table->json('text');
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('sort_order');
        });

        Schema::create('landing_testimonial_section', function (Blueprint $table) {
            $table->id();
            $table->json('subtitle')->nullable();
            $table->json('headline')->nullable();
            $table->string('bg_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::dropIfExists('landing_testimonials');

        Schema::create('landing_testimonials', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->json('designation');
            $table->json('review');
            $table->tinyInteger('rating')->default(5);
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_testimonials');

        Schema::create('landing_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('designation_en');
            $table->string('designation_ar');
            $table->text('review_en');
            $table->text('review_ar');
            $table->tinyInteger('rating')->default(5);
            $table->string('photo')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::dropIfExists('landing_testimonial_section');
        Schema::dropIfExists('landing_solution_points');
        Schema::dropIfExists('landing_solution_slides');
        Schema::dropIfExists('landing_solution_section');
    }
};
