<?php

namespace Database\Seeders;

use App\Models\LandingStatItem;
use App\Models\LandingStatSection;
use Illuminate\Database\Seeder;

class LandingStatSeeder extends Seeder
{
    public function run(): void
    {
        LandingStatSection::query()->delete();

        LandingStatSection::create([
            'subtitle' => [
                'ar' => 'إحصائياتنا',
                'en' => 'Our Statistics',
            ],
            'headline' => [
                ['title' => ['ar' => 'أرقام ', 'en' => 'Numbers '], 'check' => 0],
                ['title' => ['ar' => 'تحدث', 'en' => 'That Speak'], 'check' => 1],
                ['title' => ['ar' => ' عن ', 'en' => ' of '], 'check' => 0],
                ['title' => ['ar' => 'نجاحنا', 'en' => 'Our Success'], 'check' => 1],
            ],
            'bg_image'  => null,
            'is_active' => true,
        ]);

        LandingStatItem::query()->delete();

        $stats = [
            [
                'value' => ['ar' => '10,000', 'en' => '10,000'],
                'suffix'  => '+',
                'label' => ['ar' => 'عدد العملاء', 'en' => 'Customers'],
                'sort_order' => 1,
            ],
            [
                'value' => ['ar' => '5,000', 'en' => '5,000'],
                'suffix'  => '+',
                'label' => ['ar' => 'عدد الشركات', 'en' => 'Companies'],
                'sort_order' => 2,
            ],
            [
                'value' => ['ar' => '1M', 'en' => '1M'],
                'suffix'  => '+',
                'label' => ['ar' => 'عدد العمليات', 'en' => 'Operations'],
                'sort_order' => 3,
            ],
            [
                'value' => ['ar' => '99.9', 'en' => '99.9'],
                'suffix'  => '%',
                'label' => ['ar' => 'نسبة الاستقرار', 'en' => 'Uptime Rate'],
                'sort_order' => 4,
            ],
        ];

        foreach ($stats as $stat) {
            LandingStatItem::create(array_merge($stat, [
                'image'     => null,
                'is_active' => true,
            ]));
        }
    }
}
