<?php

namespace Database\Seeders;

use App\Models\LandingHowItWork;
use Illuminate\Database\Seeder;

class LandingHowItWorkSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'step_number'    => 1,
                'image'          => null,
                'title_en'       => 'Sign Up & Choose Your Plan',
                'title_ar'       => 'سجّل واختر خطتك',
                'description_en' => 'Create your account in seconds. Choose the plan that fits your business size and needs — no credit card required for the free trial.',
                'description_ar' => 'أنشئ حسابك في ثوانٍ. اختر الخطة المناسبة لحجم عملك — لا بطاقة ائتمانية مطلوبة للتجربة المجانية.',
                'sort_order'     => 1,
                'is_active'      => true,
            ],
            [
                'step_number'    => 2,
                'image'          => null,
                'title_en'       => 'Import Your Contacts & Setup',
                'title_ar'       => 'استورد جهات اتصالك وابدأ الإعداد',
                'description_en' => 'Import your existing contacts, configure your deal stages to match your sales process, and invite your team members.',
                'description_ar' => 'استورد جهات اتصالك الحالية، اضبط مراحل الصفقات لتناسب مسار مبيعاتك، وادعُ أعضاء فريقك.',
                'sort_order'     => 2,
                'is_active'      => true,
            ],
            [
                'step_number'    => 3,
                'image'          => null,
                'title_en'       => 'Grow & Close More Deals',
                'title_ar'       => 'نمّ أعمالك وأغلق المزيد من الصفقات',
                'description_en' => 'Track every interaction, follow up on tasks, send invoices, and use data-driven reports to continuously improve your sales.',
                'description_ar' => 'تتبّع كل تفاعل، تابع المهام، أرسل الفواتير، واستخدم التقارير لتحسين مبيعاتك باستمرار.',
                'sort_order'     => 3,
                'is_active'      => true,
            ],
        ];

        foreach ($steps as $step) {
            LandingHowItWork::firstOrCreate(
                ['step_number' => $step['step_number']],
                $step
            );
        }
    }
}
