<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DealStagesSeeder extends Seeder
{
    public static array $defaultStages = [
        ['name' => 'Lead',         'color' => '#6366F1', 'order' => 1],
        ['name' => 'Qualified',    'color' => '#3B82F6', 'order' => 2],
        ['name' => 'Proposal',     'color' => '#F59E0B', 'order' => 3],
        ['name' => 'Negotiation',  'color' => '#8B5CF6', 'order' => 4],
        ['name' => 'Closed Won',   'color' => '#10B981', 'order' => 5],
        ['name' => 'Closed Lost',  'color' => '#EF4444', 'order' => 6],
    ];

    public static function createForTenant(int $tenantId): void
    {
        foreach (self::$defaultStages as $stage) {
            DB::table('deal_stages')->insert([
                'tenant_id'  => $tenantId,
                'name'       => $stage['name'],
                'color'      => $stage['color'],
                'order'      => $stage['order'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function run(): void
    {
        $tenants = DB::table('tenants')->pluck('id');

        foreach ($tenants as $tenantId) {
            $hasStages = DB::table('deal_stages')->where('tenant_id', $tenantId)->exists();
            if (! $hasStages) {
                self::createForTenant($tenantId);
            }
        }
    }
}
