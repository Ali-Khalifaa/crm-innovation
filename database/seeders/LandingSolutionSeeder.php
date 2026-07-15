<?php

namespace Database\Seeders;

use App\Models\LandingSolutionPoint;
use App\Models\LandingSolutionSection;
use App\Models\LandingSolutionSlide;
use Illuminate\Database\Seeder;

class LandingSolutionSeeder extends Seeder
{
    public function run(): void
    {
        LandingSolutionSection::query()->delete();

        LandingSolutionSection::create([
            'subtitle' => [
                'ar' => 'لوحة التحكم',
                'en' => 'Dashboard',
            ],
            'headline' => [
                ['title' => ['ar' => 'واجهة ', 'en' => 'An '], 'check' => 0],
                ['title' => ['ar' => 'سهلة', 'en' => 'Easy'], 'check' => 1],
                ['title' => ['ar' => ' و', 'en' => ' and '], 'check' => 0],
                ['title' => ['ar' => 'بديهية', 'en' => 'Intuitive'], 'check' => 1],
            ],
            'description' => [
                'ar' => 'لوحة تحكم متكاملة تعرض جميع المعلومات الهامة في مكان واحد. يمكنك الوصول إلى جميع ميزات النظام بسهولة.',
                'en' => 'A comprehensive dashboard that displays all important information in one place. You can easily access all system features.',
            ],
            'button' => [
                'text' => [
                    'ar' => 'جرب النظام الآن',
                    'en' => 'Try the System Now',
                ],
                'url' => '#contact',
            ],
            'is_active' => true,
        ]);

        LandingSolutionSlide::query()->delete();

        $slides = [
            [
                'title' => ['ar' => 'لوحة ويب', 'en' => 'Web Dashboard'],
                'subtitle' => [
                    'ar' => 'تعمل على الكمبيوتر والتابلت بسلاسة',
                    'en' => 'Works smoothly on desktop and tablet',
                ],
                'sort_order' => 1,
            ],
            [
                'title' => ['ar' => 'تطبيق جوال', 'en' => 'Mobile App'],
                'subtitle' => [
                    'ar' => 'تابع عملاءك ومبيعاتك من أي مكان',
                    'en' => 'Track your customers and sales from anywhere',
                ],
                'sort_order' => 2,
            ],
            [
                'title' => ['ar' => 'تكامل API', 'en' => 'API Integration'],
                'subtitle' => [
                    'ar' => 'اتصل بأدواتك الحالية بسهولة',
                    'en' => 'Connect with your existing tools easily',
                ],
                'sort_order' => 3,
            ],
        ];

        foreach ($slides as $slide) {
            LandingSolutionSlide::create(array_merge($slide, [
                'image'     => null,
                'is_active' => true,
            ]));
        }

        LandingSolutionPoint::query()->delete();

        $points = [
            ['text' => ['ar' => 'لوحة تحكم شاملة', 'en' => 'Comprehensive dashboard'], 'sort_order' => 1],
            ['text' => ['ar' => 'واجهة مستخدم سهلة الاستخدام', 'en' => 'Easy-to-use interface'], 'sort_order' => 2],
            ['text' => ['ar' => 'تصميم متجاوب يعمل على جميع الأجهزة', 'en' => 'Responsive design for all devices'], 'sort_order' => 3],
            ['text' => ['ar' => 'تكامل مع الأنظمة الأخرى', 'en' => 'Integration with other systems'], 'sort_order' => 4],
            ['text' => ['ar' => 'دعم فني على مدار الساعة', 'en' => '24/7 technical support'], 'sort_order' => 5],
        ];

        foreach ($points as $point) {
            LandingSolutionPoint::create(array_merge($point, [
                'is_active' => true,
            ]));
        }
    }
}
