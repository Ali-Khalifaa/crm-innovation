<?php

namespace Database\Seeders;

use App\Models\LandingHero;
use App\Models\LandingHeroSlide;
use Illuminate\Database\Seeder;

class LandingHeroSeeder extends Seeder
{
    public function run(): void
    {
        LandingHero::query()->delete();

        LandingHero::create([
            'subtitle' => [
                'ar' => 'نظام CRM سحابي متكامل',
                'en' => 'Integrated Cloud CRM System',
            ],
            'headline' => [
                ['title' => ['ar' => 'إدارة', 'en' => 'Manage'], 'check' => 0],
                ['title' => ['ar' => 'أعمالك', 'en' => 'your business'], 'check' => 1],
                ['title' => ['ar' => 'وعملائك', 'en' => 'and customers'], 'check' => 0],
                ['title' => ['ar' => 'من مكان واحد', 'en' => 'from one place'], 'check' => 1],
            ],
            'description' => [
                'ar' => 'نظام CRM سحابي يساعدك على إدارة العملاء، المبيعات، المهام، الفواتير، والتقارير بكل سهولة. حل متكامل لإدارة أعمالك بكفاءة وفعالية.',
                'en' => 'A cloud CRM system that helps you manage customers, sales, tasks, invoices, and reports with ease. An integrated solution to run your business efficiently.',
            ],
            'btn_primary' => [
                'text' => ['ar' => 'ابدأ مجاناً', 'en' => 'Start Free'],
                'url'  => '/register',
                'icon' => 'fa-plus',
            ],
            'btn_secondary' => [
                'text' => ['ar' => 'كيف يعمل النظام', 'en' => 'How it works'],
                'url'  => 'https://www.youtube.com/embed/Wimkqo8gDZ0',
                'type' => 'video',
            ],
            'bg_image'         => null,
            'bg_overlay_image' => null,
            'is_active'        => true,
        ]);

        LandingHeroSlide::query()->delete();

        $slides = [
            [
                'title' => [
                    'ar' => 'لوحة تحكم متكاملة',
                    'en' => 'Integrated Dashboard',
                ],
                'subtitle' => [
                    'ar' => 'كل بياناتك في مكان واحد',
                    'en' => 'All your data in one place',
                ],
                'image'          => null,
                'focus_position' => 'top',
                'sort_order'     => 1,
                'is_active'      => true,
            ],
            [
                'title' => [
                    'ar' => 'واجهة سهلة وبديهية',
                    'en' => 'Simple & Intuitive Interface',
                ],
                'subtitle' => [
                    'ar' => 'تبدأ العمل بدون تعقيد',
                    'en' => 'Get started without complexity',
                ],
                'image'          => null,
                'focus_position' => 'center',
                'sort_order'     => 2,
                'is_active'      => true,
            ],
            [
                'title' => [
                    'ar' => 'تقارير وتحليلات فورية',
                    'en' => 'Instant Reports & Analytics',
                ],
                'subtitle' => [
                    'ar' => 'قرارات أسرع بناءً على بيانات حقيقية',
                    'en' => 'Faster decisions based on real data',
                ],
                'image'          => null,
                'focus_position' => 'bottom',
                'sort_order'     => 3,
                'is_active'      => true,
            ],
        ];

        foreach ($slides as $slide) {
            LandingHeroSlide::create($slide);
        }
    }
}
