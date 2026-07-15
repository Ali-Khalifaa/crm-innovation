<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class LandingPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // General Settings
            ['name' => 'landing settings read', 'category' => 'Landing Page'],
            ['name' => 'landing settings edit', 'category' => 'Landing Page'],
            ['name' => 'landing seo read', 'category' => 'Landing Page'],
            ['name' => 'landing seo edit', 'category' => 'Landing Page'],

            // Hero Section
            ['name' => 'landing hero read', 'category' => 'Landing Page'],
            ['name' => 'landing hero edit', 'category' => 'Landing Page'],

            // Problems Section
            ['name' => 'landing problem read', 'category' => 'Landing Page'],
            ['name' => 'landing problem edit', 'category' => 'Landing Page'],

            // Stats
            ['name' => 'landing stat read',   'category' => 'Landing Page'],
            ['name' => 'landing stat create', 'category' => 'Landing Page'],
            ['name' => 'landing stat edit',   'category' => 'Landing Page'],
            ['name' => 'landing stat delete', 'category' => 'Landing Page'],

            // Features
            ['name' => 'landing feature read',   'category' => 'Landing Page'],
            ['name' => 'landing feature create', 'category' => 'Landing Page'],
            ['name' => 'landing feature edit',   'category' => 'Landing Page'],
            ['name' => 'landing feature delete', 'category' => 'Landing Page'],

            // How It Works
            ['name' => 'landing how it works read',   'category' => 'Landing Page'],
            ['name' => 'landing how it works create', 'category' => 'Landing Page'],
            ['name' => 'landing how it works edit',   'category' => 'Landing Page'],
            ['name' => 'landing how it works delete', 'category' => 'Landing Page'],

            // Why Us
            ['name' => 'landing why read', 'category' => 'Landing Page'],
            ['name' => 'landing why edit', 'category' => 'Landing Page'],

            // Solutions
            ['name' => 'landing solutions read', 'category' => 'Landing Page'],
            ['name' => 'landing solutions edit', 'category' => 'Landing Page'],

            // FAQs
            ['name' => 'landing faq read',   'category' => 'Landing Page'],
            ['name' => 'landing faq create', 'category' => 'Landing Page'],
            ['name' => 'landing faq edit',   'category' => 'Landing Page'],
            ['name' => 'landing faq delete', 'category' => 'Landing Page'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                ['name' => $permission['name'], 'category' => $permission['category']]
            );
        }
    }
}
