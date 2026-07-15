<?php

namespace Database\Seeders;

use App\Models\LandingHowItem;
use App\Models\LandingHowSection;
use Illuminate\Database\Seeder;

class LandingHowItWorkSeeder extends Seeder
{
    public function run(): void
    {
        LandingHowSection::query()->delete();

        LandingHowSection::create([
            'subtitle' => [
                'ar' => 'خطوات بسيطة',
                'en' => 'Simple Steps',
            ],
            'headline' => [
                ['title' => ['ar' => 'كيف ', 'en' => 'How to '], 'check' => 0],
                ['title' => ['ar' => 'تبدأ', 'en' => 'Get Started'], 'check' => 1],
                ['title' => ['ar' => ' استخدام النظام؟', 'en' => ' Using the System?'], 'check' => 0],
            ],
            'is_active' => true,
        ]);

        LandingHowItem::query()->delete();

        $steps = [
            [
                'title' => ['ar' => 'أنشئ حسابك', 'en' => 'Create Your Account'],
                'description' => [
                    'ar' => 'سجل في النظام للحصول على حساب مجاني. عملية التسجيل بسيطة وسريعة.',
                    'en' => 'Sign up for a free account. Registration is simple and fast.',
                ],
                'sort_order' => 1,
            ],
            [
                'title' => ['ar' => 'أضف فريق العمل', 'en' => 'Add Your Team'],
                'description' => [
                    'ar' => 'أضف موظفيك وتعيين صلاحياتهم المناسبة لكل منهم.',
                    'en' => 'Add your employees and assign appropriate permissions to each.',
                ],
                'sort_order' => 2,
            ],
            [
                'title' => ['ar' => 'استورد العملاء', 'en' => 'Import Customers'],
                'description' => [
                    'ar' => 'استورد بيانات عملائك الحالية أو أضف عملاء جديد.',
                    'en' => 'Import your existing customer data or add new customers.',
                ],
                'sort_order' => 3,
            ],
            [
                'title' => ['ar' => 'ابدأ إدارة أعمالك', 'en' => 'Start Managing Your Business'],
                'description' => [
                    'ar' => 'ابدأ في إدارة العملاء، المبيعات، المهام، والفواتير.',
                    'en' => 'Start managing customers, sales, tasks, and invoices.',
                ],
                'sort_order' => 4,
            ],
            [
                'title' => ['ar' => 'راقب التقارير', 'en' => 'Monitor Reports'],
                'description' => [
                    'ar' => 'راقب أداء أعمالك من خلال التقارير الشاملة.',
                    'en' => 'Monitor your business performance through comprehensive reports.',
                ],
                'sort_order' => 5,
            ],
        ];

        foreach ($steps as $step) {
            LandingHowItem::create(array_merge($step, [
                'image'     => null,
                'is_active' => true,
            ]));
        }
    }
}
