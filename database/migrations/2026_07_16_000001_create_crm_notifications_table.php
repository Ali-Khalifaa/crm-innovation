<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50); // task_due, task_assigned, deal_won, deal_lost, deal_assigned, contact_created, invoice_overdue
            $table->string('title');
            $table->string('body')->nullable();
            $table->json('data')->nullable(); // { url, id, model }
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_notifications');
    }
};
