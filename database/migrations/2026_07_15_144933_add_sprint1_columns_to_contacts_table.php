<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->enum('lead_source', ['website', 'referral', 'social', 'cold', 'event', 'other'])
                  ->nullable()
                  ->after('status');
            $table->unsignedBigInteger('owner_id')->nullable()->after('lead_source');
            $table->unsignedBigInteger('company_id')->nullable()->after('owner_id');

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['company_id']);
            $table->dropColumn(['lead_source', 'owner_id', 'company_id']);
        });
    }
};
