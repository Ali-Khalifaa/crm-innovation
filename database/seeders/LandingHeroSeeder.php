<?php

namespace Database\Seeders;

use App\Models\LandingHero;
use Illuminate\Database\Seeder;

class LandingHeroSeeder extends Seeder
{
    public function run(): void
    {
        LandingHero::firstOrCreate([], [
            'title_en'              => 'Grow Your <span>Sales Pipeline</span> &amp; Close More <span>Deals</span>',
            'title_ar'              => 'طوّر <span>خط مبيعاتك</span> وأغلق <span>المزيد من الصفقات</span>',
            'subtitle_en'           => 'Smart CRM for Modern Businesses',
            'subtitle_ar'           => 'CRM ذكي للشركات الحديثة',
            'description_en'        => 'Manage contacts, track deals, send invoices and grow your business — all in one beautifully simple CRM platform.',
            'description_ar'        => 'أدر جهات الاتصال وتتبع الصفقات وأرسل الفواتير وطوّر أعمالك — كل ذلك في منصة CRM بسيطة واحترافية.',
            'btn_primary_text_en'   => 'Start Free Trial',
            'btn_primary_text_ar'   => 'ابدأ مجاناً',
            'btn_primary_url'       => '/register',
            'btn_secondary_text_en' => 'View Pricing',
            'btn_secondary_text_ar' => 'عرض الأسعار',
            'btn_secondary_url'     => '/pricing',
            'image'                 => null,
            'is_active'             => true,
        ]);
    }
}
