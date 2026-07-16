<?php

namespace Database\Seeders;

use App\Models\LandingProblemItem;
use App\Models\LandingProblemSection;
use Illuminate\Database\Seeder;

class LandingProblemSeeder extends Seeder
{
    public function run(): void
    {
        LandingProblemSection::query()->delete();

        LandingProblemSection::create([
            'subtitle' => [
                'ar' => 'التحديات الشائعة',
                'en' => 'Common Challenges',
            ],
            'headline' => [
                ['title' => ['ar' => 'هل ', 'en' => 'Do '], 'check' => 0],
                ['title' => ['ar' => 'تواجه', 'en' => 'You Face'], 'check' => 1],
                ['title' => ['ar' => ' هذه ', 'en' => ' These '], 'check' => 0],
                ['title' => ['ar' => 'المشاكل؟', 'en' => 'Problems?'], 'check' => 1],
            ],
            'intro' => [
                'ar' => 'معظم الشركات النامية تواجه نفس التحديات — بيانات مشتتة، متابعة ضعيفة، وتقارير غير دقيقة. أنت لست وحدك.',
                'en' => 'Most growing businesses face the same challenges — scattered data, poor follow-up, and inaccurate reports. You are not alone.',
            ],
            'insights' => [
                [
                    'value' => ['ar' => '68%', 'en' => '68%'],
                    'label' => ['ar' => 'تواجه 3+ تحديات يومياً', 'en' => 'Face 3+ daily challenges'],
                ],
                [
                    'value' => ['ar' => '4.2 س', 'en' => '4.2 h'],
                    'label' => ['ar' => 'متوسط الوقت الضائع يومياً', 'en' => 'Average wasted time daily'],
                ],
                [
                    'value' => ['ar' => '-35%', 'en' => '-35%'],
                    'label' => ['ar' => 'انخفاض في المبيعات بدون CRM', 'en' => 'Sales drop without CRM'],
                ],
            ],
            'cta' => [
                'bridge' => [
                    'ar' => 'لا داعي للاستمرار بهذه الطريقة — يمكنك حل كل هذه التحديات من نظام واحد.',
                    'en' => 'No need to continue this way — you can solve all these challenges from one system.',
                ],
                'text' => ['ar' => 'احصل على الحل الآن', 'en' => 'Get the Solution Now'],
                'url'  => '#contact',
            ],
            'is_active' => true,
        ]);

        LandingProblemItem::query()->delete();

        $items = [
            [
                'title' => ['ar' => 'ضياع بيانات العملاء', 'en' => 'Lost Customer Data'],
                'description' => [
                    'ar' => 'فقدان معلومات العملاء المهمة بسبب عدم وجود نظام موثوق لحفظها وتنظيمها.',
                    'en' => 'Losing important customer information due to the lack of a reliable system to store and organize it.',
                ],
                'impact_label' => ['ar' => 'تأثير عالي', 'en' => 'High Impact'],
                'sort_order' => 1,
            ],
            [
                'title' => ['ar' => 'صعوبة متابعة العملاء', 'en' => 'Hard to Follow Up with Customers'],
                'description' => [
                    'ar' => 'عدم القدرة على متابعة العملاء بشكل فعال وفقدان فرص التواصل معهم في الوقت المناسب.',
                    'en' => 'Inability to follow up with customers effectively and missing timely communication opportunities.',
                ],
                'impact_label' => null,
                'sort_order' => 2,
            ],
            [
                'title' => ['ar' => 'الاعتماد على Excel', 'en' => 'Reliance on Excel'],
                'description' => [
                    'ar' => 'استخدام جداول البيانات اليدوية التي تكون عرضة للأخطاء وصعبة في الإدارة.',
                    'en' => 'Using manual spreadsheets that are error-prone and difficult to manage.',
                ],
                'impact_label' => null,
                'sort_order' => 3,
            ],
            [
                'title' => ['ar' => 'ضعف التقارير', 'en' => 'Weak Reporting'],
                'description' => [
                    'ar' => 'عدم القدرة على توليد تقارير دقيقة وشاملة عن أداء الأعمال والمبيعات.',
                    'en' => 'Inability to generate accurate and comprehensive business and sales reports.',
                ],
                'impact_label' => null,
                'sort_order' => 4,
            ],
            [
                'title' => ['ar' => 'عدم تنظيم فريق العمل', 'en' => 'Disorganized Team'],
                'description' => [
                    'ar' => 'صعوبة تنسيق العمل بين أعضاء الفريق وعدم وضوح المهام والمسؤوليات.',
                    'en' => 'Difficulty coordinating team work and unclear tasks and responsibilities.',
                ],
                'impact_label' => null,
                'sort_order' => 5,
            ],
            [
                'title' => ['ar' => 'فقدان فرص البيع', 'en' => 'Lost Sales Opportunities'],
                'description' => [
                    'ar' => 'فقدان فرص بيع مهمة بسبب عدم متابعة العملاء المحتملين في الوقت المناسب.',
                    'en' => 'Missing important sales opportunities due to poor lead follow-up.',
                ],
                'impact_label' => null,
                'sort_order' => 6,
            ],
        ];

        foreach ($items as $item) {
            LandingProblemItem::create(array_merge($item, [
                'image'      => null,
                'is_active'  => true,
            ]));
        }
    }
}
