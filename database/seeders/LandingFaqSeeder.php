<?php

namespace Database\Seeders;

use App\Models\LandingFaq;
use Illuminate\Database\Seeder;

class LandingFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question_en' => 'Is there a free trial available?',
                'question_ar' => 'هل يوجد تجربة مجانية؟',
                'answer_en'   => 'Yes! Every new account starts with a 14-day free trial with full access to all Professional plan features. No credit card required.',
                'answer_ar'   => 'نعم! كل حساب جديد يبدأ بتجربة مجانية لمدة 14 يوماً مع الوصول الكامل لجميع مميزات خطة Professional. لا بطاقة ائتمانية مطلوبة.',
                'sort_order'  => 1,
                'is_active'   => true,
            ],
            [
                'question_en' => 'Can I change my plan at any time?',
                'question_ar' => 'هل يمكنني تغيير خطتي في أي وقت؟',
                'answer_en'   => 'Absolutely. You can upgrade or downgrade your plan at any time. Upgrades take effect immediately, and any unused portion is credited towards your new plan.',
                'answer_ar'   => 'بالتأكيد. يمكنك الترقية أو تخفيض خطتك في أي وقت. تسري الترقيات فوراً، ويُحسب الرصيد غير المستخدم في خطتك الجديدة.',
                'sort_order'  => 2,
                'is_active'   => true,
            ],
            [
                'question_en' => 'How many users can I add to my account?',
                'question_ar' => 'كم عدد المستخدمين الذي يمكنني إضافتهم؟',
                'answer_en'   => 'The Starter plan supports 1 user, Professional supports up to 10 users, and Enterprise supports unlimited users. You can always upgrade if you need more.',
                'answer_ar'   => 'خطة Starter تدعم مستخدماً واحداً، Professional تدعم حتى 10 مستخدمين، وEnterprise تدعم مستخدمين غير محدودين. يمكنك الترقية دائماً.',
                'sort_order'  => 3,
                'is_active'   => true,
            ],
            [
                'question_en' => 'Is my data secure?',
                'question_ar' => 'هل بياناتي آمنة؟',
                'answer_en'   => 'Your data security is our top priority. All data is encrypted in transit and at rest. Each company\'s data is fully isolated and never shared with other tenants.',
                'answer_ar'   => 'أمان بياناتك هو أولويتنا القصوى. جميع البيانات مشفّرة أثناء النقل والتخزين. بيانات كل شركة معزولة تماماً ولا تُشارك مع مستأجرين آخرين.',
                'sort_order'  => 4,
                'is_active'   => true,
            ],
            [
                'question_en' => 'Can I export my data?',
                'question_ar' => 'هل يمكنني تصدير بياناتي؟',
                'answer_en'   => 'Yes. You can export your contacts to Excel at any time. Invoices can be exported as PDF. We believe your data belongs to you.',
                'answer_ar'   => 'نعم. يمكنك تصدير جهات اتصالك إلى Excel في أي وقت. يمكن تصدير الفواتير كـ PDF. نؤمن أن بياناتك ملكك.',
                'sort_order'  => 5,
                'is_active'   => true,
            ],
            [
                'question_en' => 'Do you offer customer support?',
                'question_ar' => 'هل تقدمون دعماً للعملاء؟',
                'answer_en'   => 'We offer email support for all plans. Enterprise customers receive priority support with faster response times and dedicated account management.',
                'answer_ar'   => 'نقدم دعماً عبر البريد الإلكتروني لجميع الخطط. يحصل عملاء Enterprise على دعم أولوية مع أوقات استجابة أسرع وإدارة حساب مخصصة.',
                'sort_order'  => 6,
                'is_active'   => true,
            ],
        ];

        foreach ($faqs as $faq) {
            LandingFaq::firstOrCreate(
                ['question_en' => $faq['question_en']],
                $faq
            );
        }
    }
}
