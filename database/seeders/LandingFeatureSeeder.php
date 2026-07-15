<?php

namespace Database\Seeders;

use App\Models\LandingFeatureItem;
use App\Models\LandingFeatureSection;
use Illuminate\Database\Seeder;

class LandingFeatureSeeder extends Seeder
{
    public function run(): void
    {
        LandingFeatureSection::query()->delete();

        LandingFeatureSection::create([
            'subtitle' => [
                'ar' => 'ميزات النظام',
                'en' => 'System Features',
            ],
            'headline' => [
                ['title' => ['ar' => 'كل ما ', 'en' => 'Everything '], 'check' => 0],
                ['title' => ['ar' => 'تحتاجه', 'en' => 'You Need'], 'check' => 1],
                ['title' => ['ar' => ' لإدارة ', 'en' => ' to Manage '], 'check' => 0],
                ['title' => ['ar' => 'أعمالك', 'en' => 'Your Business'], 'check' => 1],
            ],
            'is_active' => true,
        ]);

        LandingFeatureItem::query()->delete();

        $features = [
            [
                'title' => ['ar' => 'إدارة العملاء', 'en' => 'Customer Management'],
                'description' => [
                    'ar' => 'إدارة كاملة لبيانات العملاء وتاريخ تعاملاتهم.',
                    'en' => 'Complete management of customer data and interaction history.',
                ],
                'sort_order' => 1,
            ],
            [
                'title' => ['ar' => 'إدارة العملاء المحتملين', 'en' => 'Lead Management'],
                'description' => [
                    'ar' => 'متابعة العملاء المحتملين وتحويلهم إلى عملاء فعليين.',
                    'en' => 'Track leads and convert them into active customers.',
                ],
                'sort_order' => 2,
            ],
            [
                'title' => ['ar' => 'إدارة المبيعات', 'en' => 'Sales Management'],
                'description' => [
                    'ar' => 'متابعة عملية البيع من البداية حتى الإغلاق.',
                    'en' => 'Track the sales process from start to close.',
                ],
                'sort_order' => 3,
            ],
            [
                'title' => ['ar' => 'الفواتير', 'en' => 'Invoicing'],
                'description' => [
                    'ar' => 'إنشاء وإدارة الفواتير الإلكترونية بسهولة.',
                    'en' => 'Create and manage electronic invoices with ease.',
                ],
                'sort_order' => 4,
            ],
            [
                'title' => ['ar' => 'المهام', 'en' => 'Tasks'],
                'description' => [
                    'ar' => 'إنشاء وتعيين المهام لموظفينك ومتابعة تنفيذها.',
                    'en' => 'Create and assign tasks to your team and track completion.',
                ],
                'sort_order' => 5,
            ],
            [
                'title' => ['ar' => 'التقارير', 'en' => 'Reports'],
                'description' => [
                    'ar' => 'توليد تقارير شاملة عن أداء أعمالك.',
                    'en' => 'Generate comprehensive reports on your business performance.',
                ],
                'sort_order' => 6,
            ],
            [
                'title' => ['ar' => 'الإشعارات', 'en' => 'Notifications'],
                'description' => [
                    'ar' => 'إشعارات فورية بالمهام والمواعيد الهامة.',
                    'en' => 'Instant notifications for important tasks and appointments.',
                ],
                'sort_order' => 7,
            ],
            [
                'title' => ['ar' => 'إدارة الموظفين', 'en' => 'Employee Management'],
                'description' => [
                    'ar' => 'إدارة بيانات الموظفين وصلاحياتهم.',
                    'en' => 'Manage employee data and their permissions.',
                ],
                'sort_order' => 8,
            ],
            [
                'title' => ['ar' => 'الصلاحيات', 'en' => 'Permissions'],
                'description' => [
                    'ar' => 'تعيين صلاحيات مختلفة للموظفين حسب أدوارهم.',
                    'en' => 'Assign different permissions to employees based on their roles.',
                ],
                'sort_order' => 9,
            ],
            [
                'title' => ['ar' => 'Multi-Tenant', 'en' => 'Multi-Tenant'],
                'description' => [
                    'ar' => 'دعم متعدد الشركات في نظام واحد.',
                    'en' => 'Support multiple companies in a single system.',
                ],
                'sort_order' => 10,
            ],
            [
                'title' => ['ar' => 'API', 'en' => 'API'],
                'description' => [
                    'ar' => 'تكامل مع الأنظمة الأخرى من خلال واجهة برمجة التطبيقات.',
                    'en' => 'Integrate with other systems through the application programming interface.',
                ],
                'sort_order' => 11,
            ],
            [
                'title' => ['ar' => 'Automation', 'en' => 'Automation'],
                'description' => [
                    'ar' => 'أتمتة العمليات التجارية لتوفير الوقت والجهد.',
                    'en' => 'Automate business processes to save time and effort.',
                ],
                'sort_order' => 12,
            ],
        ];

        foreach ($features as $feature) {
            LandingFeatureItem::create(array_merge($feature, [
                'image'     => null,
                'is_active' => true,
            ]));
        }
    }
}
