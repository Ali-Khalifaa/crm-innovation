<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('landing_about', function (Blueprint $table) {
            $table->id();
            $table->string('subtitle_en')->nullable();
            $table->string('subtitle_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->string('image')->nullable();
            // Box 1
            $table->string('box1_icon')->nullable();
            $table->string('box1_title_en')->nullable();
            $table->string('box1_title_ar')->nullable();
            $table->text('box1_desc_en')->nullable();
            $table->text('box1_desc_ar')->nullable();
            // Box 2
            $table->string('box2_icon')->nullable();
            $table->string('box2_title_en')->nullable();
            $table->string('box2_title_ar')->nullable();
            $table->text('box2_desc_en')->nullable();
            $table->text('box2_desc_ar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('landing_about'); }
};
