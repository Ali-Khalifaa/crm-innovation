<?php

namespace Database\Seeders;

use App\Models\LandingWhyItem;
use App\Models\LandingWhySection;
use Illuminate\Database\Seeder;

class LandingWhySeeder extends Seeder
{
    public function run(): void
    {
        LandingWhySection::query()->delete();

        LandingWhySection::create([
            'subtitle' => [
                'ar' => 'مزايانا',
                'en' => 'Our Advantages',
            ],
            'headline' => [
                ['title' => ['ar' => 'لماذا ', 'en' => 'Why '], 'check' => 0],
                ['title' => ['ar' => 'تختارنا', 'en' => 'Choose Us'], 'check' => 1],
                ['title' => ['ar' => '؟', 'en' => '?'], 'check' => 0],
            ],
            'is_active' => true,
        ]);

        LandingWhyItem::query()->delete();

        $items = [
            [
                'title' => ['ar' => 'سريع', 'en' => 'Fast'],
                'description' => [
                    'ar' => 'نظام سريع الاستجابة يعمل بكفاءة عالية.',
                    'en' => 'A fast and highly responsive system.',
                ],
                'sort_order' => 1,
            ],
            [
                'title' => ['ar' => 'آمن', 'en' => 'Secure'],
                'description' => [
                    'ar' => 'حماية بياناتك بأعلى معايير الأمن والخصوصية.',
                    'en' => 'Protect your data with the highest security and privacy standards.',
                ],
                'sort_order' => 2,
            ],
            [
                'title' => ['ar' => 'سحابي', 'en' => 'Cloud-Based'],
                'description' => [
                    'ar' => 'وصول إلى نظامك من أي مكان وفي أي وقت.',
                    'en' => 'Access your system from anywhere, anytime.',
                ],
                'sort_order' => 3,
            ],
            [
                'title' => ['ar' => 'متعدد الشركات', 'en' => 'Multi-Company'],
                'description' => [
                    'ar' => 'إدارة عدة شركات من خلال نظام واحد.',
                    'en' => 'Manage multiple companies through one system.',
                ],
                'sort_order' => 4,
            ],
            [
                'title' => ['ar' => 'متعدد اللغات', 'en' => 'Multilingual'],
                'description' => [
                    'ar' => 'دعم عدة لغات بما في ذلك العربية.',
                    'en' => 'Support for multiple languages including Arabic.',
                ],
                'sort_order' => 5,
            ],
            [
                'title' => ['ar' => 'متوافق مع الجوال', 'en' => 'Mobile Friendly'],
                'description' => [
                    'ar' => 'تصميم متجاوب يعمل على جميع الأجهزة.',
                    'en' => 'Responsive design that works on all devices.',
                ],
                'sort_order' => 6,
            ],
            [
                'title' => ['ar' => 'دعم فني', 'en' => 'Technical Support'],
                'description' => [
                    'ar' => 'دعم فني متخصص على مدار الساعة.',
                    'en' => 'Specialized technical support around the clock.',
                ],
                'sort_order' => 7,
            ],
            [
                'title' => ['ar' => 'تحديثات مستمرة', 'en' => 'Continuous Updates'],
                'description' => [
                    'ar' => 'تحديثات دورية لإضافة ميزات جديدة.',
                    'en' => 'Regular updates to add new features.',
                ],
                'sort_order' => 8,
            ],
        ];

        foreach ($items as $item) {
            LandingWhyItem::create(array_merge($item, [
                'image'     => null,
                'is_active' => true,
            ]));
        }
    }
}
