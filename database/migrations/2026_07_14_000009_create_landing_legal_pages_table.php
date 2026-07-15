<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_legal_pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->unique()->comment('1=privacy, 2=terms');
            $table->json('title');
            $table->json('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_legal_pages');
    }
};
