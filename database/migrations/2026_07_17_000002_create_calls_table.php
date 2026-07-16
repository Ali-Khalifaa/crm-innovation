<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->enum('outcome', ['answered', 'no_answer', 'voicemail', 'busy'])->default('answered');
            $table->text('notes')->nullable();
            $table->dateTime('called_at');
            $table->morphs('callable'); // callable_type + callable_id → Contact | Deal
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['tenant_id', 'called_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
