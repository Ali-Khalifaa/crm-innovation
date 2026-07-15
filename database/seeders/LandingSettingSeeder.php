<?php

namespace Database\Seeders;

use App\Models\LandingSetting;
use Illuminate\Database\Seeder;

class LandingSettingSeeder extends Seeder
{
    public function run(): void
    {
        LandingSetting::firstOrCreate([], [
            'site_name_en' => 'CRM Innovation',
            'site_name_ar' => 'CRM إنوفيشن',
            'email'        => 'info@crm-innovation.com',
            'phone'        => '+20 100 123 4567',
            'whatsapp'     => '+20 100 123 4567',
            'address_en'   => 'Cairo, Egypt',
            'address_ar'   => 'القاهرة، مصر',
            'facebook'     => 'https://facebook.com/crminnovation',
            'twitter'      => 'https://twitter.com/crminnovation',
            'linkedin'     => 'https://linkedin.com/company/crminnovation',
            'instagram'    => 'https://instagram.com/crminnovation',
            'youtube'      => null,
        ]);
    }
}
