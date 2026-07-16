<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // contacts: fast filtering by status within a tenant
        Schema::table('contacts', function (Blueprint $table) {
            if (!$this->indexExists('contacts', 'contacts_tenant_id_status_index')) {
                $table->index(['tenant_id', 'status'], 'contacts_tenant_id_status_index');
            }
        });

        // deals: fast kanban/list queries
        Schema::table('deals', function (Blueprint $table) {
            if (!$this->indexExists('deals', 'deals_tenant_id_stage_id_status_index')) {
                $table->index(['tenant_id', 'stage_id', 'status'], 'deals_tenant_id_stage_id_status_index');
            }
        });

        // crm_activities: polymorphic lookup
        Schema::table('crm_activities', function (Blueprint $table) {
            if (!$this->indexExists('crm_activities', 'crm_activities_subject_type_subject_id_index')) {
                $table->index(['subject_type', 'subject_id'], 'crm_activities_subject_type_subject_id_index');
            }
        });

        // tasks: fast lookup per tenant + due_date sorting
        Schema::table('tasks', function (Blueprint $table) {
            if (!$this->indexExists('tasks', 'tasks_tenant_id_status_due_date_index')) {
                $table->index(['tenant_id', 'status', 'due_date'], 'tasks_tenant_id_status_due_date_index');
            }
        });

        // invoices: status + due_date for overdue checks
        Schema::table('invoices', function (Blueprint $table) {
            if (!$this->indexExists('invoices', 'invoices_tenant_id_status_due_date_index')) {
                $table->index(['tenant_id', 'status', 'due_date'], 'invoices_tenant_id_status_due_date_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('contacts_tenant_id_status_index');
        });
        Schema::table('deals', function (Blueprint $table) {
            $table->dropIndex('deals_tenant_id_stage_id_status_index');
        });
        Schema::table('crm_activities', function (Blueprint $table) {
            $table->dropIndex('crm_activities_subject_type_subject_id_index');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_tenant_id_status_due_date_index');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_tenant_id_status_due_date_index');
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        return collect(\DB::select("SHOW INDEX FROM `{$table}`"))
            ->pluck('Key_name')
            ->contains($index);
    }
};
