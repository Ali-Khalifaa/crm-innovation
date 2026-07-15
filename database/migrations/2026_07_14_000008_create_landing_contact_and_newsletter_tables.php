<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_contact_section', function (Blueprint $table) {
            $table->id();
            $table->json('cta_subtitle')->nullable();
            $table->json('cta_headline')->nullable();
            $table->json('cta_intro')->nullable();
            $table->json('cta_btn1_text')->nullable();
            $table->string('cta_btn1_link')->nullable();
            $table->json('cta_btn2_text')->nullable();
            $table->string('cta_btn2_link')->nullable();
            $table->json('form_subtitle')->nullable();
            $table->json('form_title')->nullable();
            $table->json('form_intro')->nullable();
            $table->string('bg_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_newsletter_section', function (Blueprint $table) {
            $table->id();
            $table->json('title')->nullable();
            $table->json('placeholder')->nullable();
            $table->json('button_text')->nullable();
            $table->string('bg_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('landing_newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_newsletter_subscribers');
        Schema::dropIfExists('landing_newsletter_section');
        Schema::dropIfExists('landing_contact_section');
    }
};
