<?php

namespace Database\Seeders;

use App\Models\LandingPartner;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class LandingPartnerSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        $permissions = [
            ['name' => 'landing partner read',   'category' => 'Landing Page'],
            ['name' => 'landing partner create', 'category' => 'Landing Page'],
            ['name' => 'landing partner edit',   'category' => 'Landing Page'],
            ['name' => 'landing partner delete', 'category' => 'Landing Page'],
        ];
        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        // Demo partners (using placeholder names — images will be uploaded via admin)
        $partners = [
            ['name' => 'Google',    'url' => 'https://google.com',    'sort_order' => 1],
            ['name' => 'Microsoft', 'url' => 'https://microsoft.com', 'sort_order' => 2],
            ['name' => 'Slack',     'url' => 'https://slack.com',     'sort_order' => 3],
            ['name' => 'Stripe',    'url' => 'https://stripe.com',    'sort_order' => 4],
            ['name' => 'Notion',    'url' => 'https://notion.so',     'sort_order' => 5],
            ['name' => 'Zapier',    'url' => 'https://zapier.com',    'sort_order' => 6],
        ];

        foreach ($partners as $partner) {
            LandingPartner::firstOrCreate(
                ['name' => $partner['name']],
                array_merge($partner, ['image' => '', 'is_active' => false])
            );
        }
    }
}
