<?php

namespace Database\Seeders;

use App\Models\LandingStat;
use Illuminate\Database\Seeder;

class LandingStatSeeder extends Seeder
{
    public function run(): void
    {
        $stats = [
            [
                'value_en'   => '2500',
                'value_ar'   => '2500',
                'suffix'     => '+',
                'label_en'   => 'Active Users',
                'label_ar'   => 'مستخدم نشط',
                'sort_order' => 1,
                'is_active'  => true,
            ],
            [
                'value_en'   => '98',
                'value_ar'   => '98',
                'suffix'     => '%',
                'label_en'   => 'Customer Satisfaction',
                'label_ar'   => 'رضا العملاء',
                'sort_order' => 2,
                'is_active'  => true,
            ],
            [
                'value_en'   => '1',
                'value_ar'   => '1',
                'suffix'     => 'M+',
                'label_en'   => 'Contacts Managed',
                'label_ar'   => 'جهة اتصال مُدارة',
                'sort_order' => 3,
                'is_active'  => true,
            ],
            [
                'value_en'   => '50',
                'value_ar'   => '50',
                'suffix'     => '+',
                'label_en'   => 'Countries Worldwide',
                'label_ar'   => 'دولة حول العالم',
                'sort_order' => 4,
                'is_active'  => true,
            ],
        ];

        foreach ($stats as $stat) {
            LandingStat::firstOrCreate(
                ['label_en' => $stat['label_en']],
                $stat
            );
        }
    }
}
