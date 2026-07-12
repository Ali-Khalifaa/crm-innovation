<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_hero', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_ar');
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
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_hero');
    }
};
