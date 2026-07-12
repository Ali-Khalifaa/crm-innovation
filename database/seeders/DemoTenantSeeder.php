<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        $proPlan = DB::table('plans')->where('slug', 'pro')->first();

        if (! $proPlan) {
            $this->command->warn('Pro plan not found. Run PlansSeeder first.');
            return;
        }

        DB::table('tenants')->where('slug', 'demo')->delete();

        $tenantId = DB::table('tenants')->insertGetId([
            'name'          => 'Demo Company',
            'slug'          => 'demo',
            'email'         => env('CRM_DEMO_EMAIL', 'demo@crm-innovation.com'),
            'phone'         => '+1 555 000 0000',
            'plan_id'       => $proPlan->id,
            'billing_cycle' => 'monthly',
            'status'        => 'active',
            'trial_ends_at' => null,
            'plan_starts_at' => now(),
            'plan_ends_at'   => now()->addYear(),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        DB::table('users')->where('email', env('CRM_DEMO_EMAIL', 'demo@crm-innovation.com'))->delete();

        DB::table('users')->insert([
            'tenant_id'  => $tenantId,
            'name'       => 'Demo Owner',
            'email'      => env('CRM_DEMO_EMAIL', 'demo@crm-innovation.com'),
            'password'   => Hash::make(env('CRM_DEMO_PASSWORD', 'Demo@123456')),
            'role'       => 'owner',
            'status'     => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // create default deal stages for demo tenant
        $this->createDealStages($tenantId);

        $this->command->info("Demo tenant created. Login: " . env('CRM_DEMO_EMAIL', 'demo@crm-innovation.com'));
    }

    private function createDealStages(int $tenantId): void
    {
        $stages = [
            ['name' => 'Lead',         'color' => '#6366F1', 'order' => 1],
            ['name' => 'Qualified',    'color' => '#3B82F6', 'order' => 2],
            ['name' => 'Proposal',     'color' => '#F59E0B', 'order' => 3],
            ['name' => 'Negotiation',  'color' => '#8B5CF6', 'order' => 4],
            ['name' => 'Closed Won',   'color' => '#10B981', 'order' => 5],
            ['name' => 'Closed Lost',  'color' => '#EF4444', 'order' => 6],
        ];

        foreach ($stages as $stage) {
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
}
