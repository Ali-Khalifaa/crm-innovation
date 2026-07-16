<?php

namespace Database\Seeders;

use App\Models\LandingContactSection;
use App\Models\LandingNewsletterSection;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class LandingContactNewsletterSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'landing contact edit',    'category' => 'Landing Page'],
            ['name' => 'landing newsletter read', 'category' => 'Landing Page'],
            ['name' => 'landing newsletter edit', 'category' => 'Landing Page'],
            ['name' => 'landing newsletter delete', 'category' => 'Landing Page'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(['name' => $p['name']], $p);
        }

        LandingContactSection::firstOrCreate([], [
            'cta_subtitle' => [
                'ar' => 'جاهز للتغيير؟',
                'en' => 'Ready for Change?',
            ],
            'cta_headline' => [
                ['title' => ['ar' => 'جاهز لتطوير ', 'en' => 'Ready to Grow '], 'check' => 0],
                ['title' => ['ar' => 'أعمالك', 'en' => 'Your Business'], 'check' => 1],
                ['title' => ['ar' => '؟ ابدأ ', 'en' => '? Start Your '], 'check' => 0],
                ['title' => ['ar' => 'تجربتك', 'en' => 'Free Trial'], 'check' => 1],
                ['title' => ['ar' => ' المجانية الآن', 'en' => ' Now'], 'check' => 0],
            ],
            'cta_intro' => [
                'ar' => 'انضم إلى آلاف الشركات التي ثقت بنظامنا لإدارة أعمالها. جرب النظام مجاناً لمدة 14 يوماً بدون أي التزامات.',
                'en' => 'Join thousands of companies that trust our system to manage their business. Try it free for 14 days with no commitment.',
            ],
            'cta_btn1_text' => ['ar' => 'ابدأ مجاناً', 'en' => 'Start Free'],
            'cta_btn1_link' => '/register',
            'cta_btn2_text' => ['ar' => 'احجز عرضاً', 'en' => 'Book a Demo'],
            'cta_btn2_link' => '#demo',
            'form_subtitle' => ['ar' => 'تواصل معنا', 'en' => 'Contact Us'],
            'form_title'    => ['ar' => 'احصل على استشارة مجانية', 'en' => 'Get a Free Consultation'],
            'form_intro'    => [
                'ar' => 'اتصل بنا للحصول على استشارة مجانية حول كيفية تحسين إدارة أعمالك.',
                'en' => 'Contact us for a free consultation on how to improve your business management.',
            ],
            'is_active' => true,
        ]);

        LandingNewsletterSection::firstOrCreate([], [
            'title' => [
                'ar' => 'اشترك في نشرتنا البريدية',
                'en' => 'Subscribe to Our Newsletter',
            ],
            'placeholder' => [
                'ar' => 'بريدك الإلكتروني',
                'en' => 'Your Email',
            ],
            'button_text' => [
                'ar' => 'اشترك الآن',
                'en' => 'Subscribe Now',
            ],
            'is_active' => true,
        ]);
    }
}
