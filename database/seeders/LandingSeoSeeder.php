<?php

namespace Database\Seeders;

use App\Models\LandingSeo;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class LandingSeoSeeder extends Seeder
{
    public function run(): void
    {
        Permission::updateOrCreate(
            ['name' => 'landing seo read'],
            ['name' => 'landing seo read', 'category' => 'Landing Page']
        );
        Permission::updateOrCreate(
            ['name' => 'landing seo edit'],
            ['name' => 'landing seo edit', 'category' => 'Landing Page']
        );

        LandingSeo::firstOrCreate([], [
            'meta_title' => [
                'ar' => 'CRM إنوفيشن — نظام CRM ذكي لنمو أعمالك',
                'en' => 'CRM Innovation — Smart CRM for Growing Businesses',
            ],
            'meta_description' => [
                'ar' => 'أدر جهات الاتصال وتتبع الصفقات وأرسل الفواتير وطوّر أعمالك — كل ذلك في منصة CRM بسيطة واحترافية.',
                'en' => 'Manage contacts, track deals, send invoices and grow your business — all in one beautifully simple CRM platform.',
            ],
            'meta_keywords' => [
                'ar' => 'نظام CRM, CRM سحابي, إدارة العملاء, إدارة المبيعات, نظام إدارة أعمال, برنامج CRM عربي',
                'en' => 'CRM system, cloud CRM, contact management, sales management, business software, CRM software',
            ],
            'og_title' => [
                'ar' => 'CRM إنوفيشن — نظام CRM ذكي لنمو أعمالك',
                'en' => 'CRM Innovation — Smart CRM for Growing Businesses',
            ],
            'og_description' => [
                'ar' => 'أدر جهات الاتصال وتتبع الصفقات وأرسل الفواتير وطوّر أعمالك — كل ذلك في منصة CRM بسيطة واحترافية.',
                'en' => 'Manage contacts, track deals, send invoices and grow your business — all in one beautifully simple CRM platform.',
            ],
            'twitter_title' => [
                'ar' => 'CRM إنوفيشن — نظام CRM ذكي لنمو أعمالك',
                'en' => 'CRM Innovation — Smart CRM for Growing Businesses',
            ],
            'twitter_description' => [
                'ar' => 'أدر جهات الاتصال وتتبع الصفقات وأرسل الفواتير وطوّر أعمالك — كل ذلك في منصة CRM بسيطة واحترافية.',
                'en' => 'Manage contacts, track deals, send invoices and grow your business — all in one beautifully simple CRM platform.',
            ],
            'robots'      => 'index, follow, max-image-preview:large',
            'author'      => 'CRM Innovation',
            'theme_color' => '#246bfd',
            'is_active'   => true,
        ]);
    }
}
