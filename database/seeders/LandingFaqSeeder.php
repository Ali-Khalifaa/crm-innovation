<?php

namespace Database\Seeders;

use App\Models\LandingFaq;
use App\Models\LandingFaqSection;
use Illuminate\Database\Seeder;

class LandingFaqSeeder extends Seeder
{
    public function run(): void
    {
        LandingFaqSection::firstOrCreate([], [
            'subtitle' => [
                'ar' => 'دعمنا',
                'en' => 'Support',
            ],
            'headline' => [
                ['title' => ['ar' => 'الأسئلة', 'en' => 'Frequently'], 'check' => 1],
                ['title' => ['ar' => 'الشائعة', 'en' => 'Asked Questions'], 'check' => 1],
            ],
            'intro' => [
                'ar' => 'إجابات على أكثر الأسئلة شيوعاً حول نظامنا CRM.',
                'en' => 'Answers to the most common questions about our CRM system.',
            ],
            'is_active' => true,
        ]);

        $faqs = [
            [
                'question' => ['ar' => 'ما هو نظام CRM؟', 'en' => 'What is a CRM system?'],
                'answer'   => [
                    'ar' => 'نظام CRM (إدارة علاقة العملاء) هو نظام يساعد الشركات على إدارة تفاعلاتها مع العملاء الحاليين والمحتملين. فهو يسهل إدارة البيانات، المبيعات، التسويق، ودعم العملاء.',
                    'en' => 'A CRM (Customer Relationship Management) system helps companies manage interactions with current and potential customers, including data, sales, marketing, and support.',
                ],
                'sort_order' => 1,
            ],
            [
                'question' => ['ar' => 'كيف أبدأ استخدام النظام؟', 'en' => 'How do I get started?'],
                'answer'   => [
                    'ar' => 'يمكنك البدء بتسجيل حساب مجاني من خلال موقعنا الإلكتروني. بعد التسجيل، ستتلقى رابطاً لتفعيل حسابك والبدء في استخدام النظام فوراً.',
                    'en' => 'Sign up for a free account on our website. After registration, you will receive an activation link and can start using the system immediately.',
                ],
                'sort_order' => 2,
            ],
            [
                'question' => ['ar' => 'هل النظام آمن؟', 'en' => 'Is the system secure?'],
                'answer'   => [
                    'ar' => 'نعم، نستخدم أحدث تقنيات التشفير وحماية البيانات. جميع بياناتك مخزنة في مراكز بيانات آمنة مع نسخ احتياطية دورية.',
                    'en' => 'Yes. We use modern encryption and data protection. Your data is stored in secure data centers with regular backups.',
                ],
                'sort_order' => 3,
            ],
            [
                'question' => ['ar' => 'هل يمكن استيراد بياناتي الحالية؟', 'en' => 'Can I import my existing data?'],
                'answer'   => [
                    'ar' => 'نعم، نقدم أدوات لاستيراد بياناتك من Excel و CSV و العديد من أنظمة CRM الأخرى. فريق الدعم لدينا جاهز لمساعدتك في عملية الاستيراد.',
                    'en' => 'Yes. We provide tools to import data from Excel, CSV, and many other CRM systems. Our support team can help with migration.',
                ],
                'sort_order' => 4,
            ],
            [
                'question' => ['ar' => 'ما هي طرق الدفع المتاحة؟', 'en' => 'What payment methods are available?'],
                'answer'   => [
                    'ar' => 'نقبل بطاقات الائتمان (Visa, MasterCard), PayPal، والتحويل البنكي. جميع المعاملات مشفرة وآمنة.',
                    'en' => 'We accept credit cards (Visa, MasterCard), PayPal, and bank transfers. All transactions are encrypted and secure.',
                ],
                'sort_order' => 5,
            ],
            [
                'question' => ['ar' => 'هل يوجد تطبيق جوال للنظام؟', 'en' => 'Is there a mobile app?'],
                'answer'   => [
                    'ar' => 'نعم، يتوفر تطبيق جوال لأنظمة iOS و Android يتيح لك إدارة عملائك ومتابعة أعمالك من أي مكان وفي أي وقت.',
                    'en' => 'Yes. Mobile apps for iOS and Android let you manage customers and track your business from anywhere.',
                ],
                'sort_order' => 6,
            ],
            [
                'question' => ['ar' => 'هل يدعم النظام اللغة العربية؟', 'en' => 'Does the system support Arabic?'],
                'answer'   => [
                    'ar' => 'نعم، النظام يدعم اللغة العربية بشكل كامل مع دعم الكتابة من اليمين إلى اليسار (RTL) في جميع أقسام النظام.',
                    'en' => 'Yes. The system fully supports Arabic with RTL layout across all sections.',
                ],
                'sort_order' => 7,
            ],
            [
                'question' => ['ar' => 'هل يمكنني إلغاء الاشتراك في أي وقت؟', 'en' => 'Can I cancel anytime?'],
                'answer'   => [
                    'ar' => 'نعم، يمكنك إلغاء اشتراكك في أي وقت دون أي رسوم إضافية أو غرامات. بياناتك ستبقى محفوظة لمدة 30 يوماً بعد الإلغاء.',
                    'en' => 'Yes. You can cancel anytime with no extra fees. Your data remains available for 30 days after cancellation.',
                ],
                'sort_order' => 8,
            ],
            [
                'question' => ['ar' => 'كم عدد المستخدمين المسموح بهم؟', 'en' => 'How many users are allowed?'],
                'answer'   => [
                    'ar' => 'يختلف عدد المستخدمين حسب الخطة المختارة. الخطة المجانية تدعم مستخدماً واحداً، الخطة الاحترافية تدعم حتى 10 مستخدمين، وخطة الشركات بلا حدود.',
                    'en' => 'User limits depend on your plan: free supports 1 user, professional up to 10, and enterprise unlimited users.',
                ],
                'sort_order' => 9,
            ],
            [
                'question' => ['ar' => 'هل يتكامل النظام مع أدوات أخرى؟', 'en' => 'Does it integrate with other tools?'],
                'answer'   => [
                    'ar' => 'نعم، يتكامل نظامنا مع أكثر من 50 أداة وتطبيقاً شائعاً مثل Google Workspace، Slack، Zapier، WhatsApp Business، وغيرها عبر واجهة API مفتوحة.',
                    'en' => 'Yes. We integrate with 50+ popular tools including Google Workspace, Slack, Zapier, WhatsApp Business, and more via open API.',
                ],
                'sort_order' => 10,
            ],
        ];

        foreach ($faqs as $faq) {
            LandingFaq::updateOrCreate(
                ['sort_order' => $faq['sort_order']],
                array_merge($faq, ['is_active' => true])
            );
        }
    }
}
