<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('plans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::table('plans')->insert([
            [
                'name'          => 'Starter',
                'slug'          => 'starter',
                'description'   => 'Perfect for freelancers and small teams getting started.',
                'monthly_price' => 19.00,
                'yearly_price'  => 190.00,
                'max_users'     => 1,
                'max_contacts'  => 500,
                'features'      => json_encode(['contacts', 'deals', 'tasks']),
                'is_active'     => true,
                'is_featured'   => false,
                'sort_order'    => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Professional',
                'slug'          => 'pro',
                'description'   => 'For growing businesses that need more power and features.',
                'monthly_price' => 49.00,
                'yearly_price'  => 490.00,
                'max_users'     => 10,
                'max_contacts'  => 0,
                'features'      => json_encode(['contacts', 'deals', 'tasks', 'invoices', 'reports']),
                'is_active'     => true,
                'is_featured'   => true,
                'sort_order'    => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Enterprise',
                'slug'          => 'enterprise',
                'description'   => 'Unlimited everything. Priority support. Built for scale.',
                'monthly_price' => 99.00,
                'yearly_price'  => 990.00,
                'max_users'     => 0,
                'max_contacts'  => 0,
                'features'      => json_encode(['contacts', 'deals', 'tasks', 'invoices', 'reports', 'priority_support']),
                'is_active'     => true,
                'is_featured'   => false,
                'sort_order'    => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}
