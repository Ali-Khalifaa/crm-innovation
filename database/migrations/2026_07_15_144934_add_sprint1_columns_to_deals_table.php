<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->string('lost_reason')->nullable()->after('status');
            $table->timestamp('closed_at')->nullable()->after('lost_reason');
            $table->unsignedBigInteger('company_id')->nullable()->after('closed_at');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['lost_reason', 'closed_at', 'company_id']);
        });
    }
};
