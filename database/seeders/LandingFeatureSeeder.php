<?php

namespace Database\Seeders;

use App\Models\LandingFeature;
use Illuminate\Database\Seeder;

class LandingFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'icon'           => 'fa-address-book',
                'title_en'       => 'Contact Management',
                'title_ar'       => 'إدارة جهات الاتصال',
                'description_en' => 'Organize all your contacts, leads, and customers in one powerful database with smart filtering and search.',
                'description_ar' => 'نظّم جميع جهات اتصالك والعملاء المحتملين في قاعدة بيانات واحدة بحث وتصفية ذكية.',
                'sort_order'     => 1,
                'is_active'      => true,
            ],
            [
                'icon'           => 'fa-funnel-dollar',
                'title_en'       => 'Deal Pipeline',
                'title_ar'       => 'خط الصفقات',
                'description_en' => 'Visualize your sales funnel with a Kanban board. Drag & drop deals through stages and never miss an opportunity.',
                'description_ar' => 'تصوّر مسار مبيعاتك بلوحة Kanban. اسحب الصفقات عبر المراحل ولا تفوّت أي فرصة.',
                'sort_order'     => 2,
                'is_active'      => true,
            ],
            [
                'icon'           => 'fa-tasks',
                'title_en'       => 'Task Management',
                'title_ar'       => 'إدارة المهام',
                'description_en' => 'Create tasks linked to contacts or deals, set priorities, assign to team members, and track completion.',
                'description_ar' => 'أنشئ مهاماً مرتبطة بجهات الاتصال أو الصفقات، حدد الأولويات، عيّن الفريق وتابع الإنجاز.',
                'sort_order'     => 3,
                'is_active'      => true,
            ],
            [
                'icon'           => 'fa-file-invoice-dollar',
                'title_en'       => 'Invoicing',
                'title_ar'       => 'الفواتير',
                'description_en' => 'Create professional invoices, add line items, apply taxes and discounts, and export to PDF in one click.',
                'description_ar' => 'أنشئ فواتير احترافية، أضف بنوداً، طبّق الضرائب والخصومات، وصدّرها PDF بنقرة واحدة.',
                'sort_order'     => 4,
                'is_active'      => true,
            ],
            [
                'icon'           => 'fa-chart-line',
                'title_en'       => 'Reports & Analytics',
                'title_ar'       => 'التقارير والتحليلات',
                'description_en' => 'Get real-time insights into your sales performance, revenue trends, and team productivity with visual dashboards.',
                'description_ar' => 'احصل على رؤى فورية حول أداء مبيعاتك واتجاهات الإيرادات وإنتاجية الفريق.',
                'sort_order'     => 5,
                'is_active'      => true,
            ],
            [
                'icon'           => 'fa-users',
                'title_en'       => 'Team Collaboration',
                'title_ar'       => 'التعاون الجماعي',
                'description_en' => 'Invite team members, assign roles and permissions, and work together seamlessly within your workspace.',
                'description_ar' => 'ادعُ أعضاء الفريق، حدد الأدوار والصلاحيات، واعملوا معاً بكفاءة في مساحة عملكم.',
                'sort_order'     => 6,
                'is_active'      => true,
            ],
        ];

        foreach ($features as $feature) {
            LandingFeature::firstOrCreate(
                ['title_en' => $feature['title_en']],
                $feature
            );
        }
    }
}
