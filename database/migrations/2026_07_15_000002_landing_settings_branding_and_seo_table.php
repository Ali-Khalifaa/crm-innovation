<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('site_name_ar');
            $table->string('logo_footer')->nullable()->after('logo');
            $table->string('favicon')->nullable()->after('logo_footer');
        });

        Schema::create('landing_seo', function (Blueprint $table) {
            $table->id();
            $table->json('meta_title')->nullable();
            $table->json('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->json('og_title')->nullable();
            $table->json('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->json('twitter_title')->nullable();
            $table->json('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->string('robots')->default('index, follow, max-image-preview:large');
            $table->string('author')->nullable();
            $table->string('theme_color')->default('#246bfd');
            $table->string('canonical_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $settings = DB::table('landing_settings')->first();

        DB::table('landing_seo')->insert([
            'meta_title' => json_encode([
                'ar' => $settings?->meta_title_ar ?? 'CRM إنوفيشن — نظام CRM ذكي لنمو أعمالك',
                'en' => $settings?->meta_title_en ?? 'CRM Innovation — Smart CRM for Growing Businesses',
            ], JSON_UNESCAPED_UNICODE),
            'meta_description' => json_encode([
                'ar' => $settings?->meta_description_ar ?? 'أدر جهات الاتصال وتتبع الصفقات وأرسل الفواتير وطوّر أعمالك — كل ذلك في منصة CRM بسيطة واحترافية.',
                'en' => $settings?->meta_description_en ?? 'Manage contacts, track deals, send invoices and grow your business — all in one beautifully simple CRM platform.',
            ], JSON_UNESCAPED_UNICODE),
            'meta_keywords' => json_encode([
                'ar' => 'نظام CRM, CRM سحابي, إدارة العملاء, إدارة المبيعات, نظام إدارة أعمال, برنامج CRM عربي',
                'en' => 'CRM system, cloud CRM, contact management, sales management, business software, CRM software',
            ], JSON_UNESCAPED_UNICODE),
            'og_title' => json_encode([
                'ar' => $settings?->meta_title_ar ?? 'CRM إنوفيشن — نظام CRM ذكي لنمو أعمالك',
                'en' => $settings?->meta_title_en ?? 'CRM Innovation — Smart CRM for Growing Businesses',
            ], JSON_UNESCAPED_UNICODE),
            'og_description' => json_encode([
                'ar' => $settings?->meta_description_ar ?? '',
                'en' => $settings?->meta_description_en ?? '',
            ], JSON_UNESCAPED_UNICODE),
            'twitter_title' => json_encode([
                'ar' => $settings?->meta_title_ar ?? 'CRM إنوفيشن — نظام CRM ذكي لنمو أعمالك',
                'en' => $settings?->meta_title_en ?? 'CRM Innovation — Smart CRM for Growing Businesses',
            ], JSON_UNESCAPED_UNICODE),
            'twitter_description' => json_encode([
                'ar' => $settings?->meta_description_ar ?? '',
                'en' => $settings?->meta_description_en ?? '',
            ], JSON_UNESCAPED_UNICODE),
            'robots'       => 'index, follow, max-image-preview:large',
            'author'       => $settings?->site_name_en ?? 'CRM Innovation',
            'theme_color'  => '#246bfd',
            'canonical_url'=> null,
            'is_active'    => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title_en',
                'meta_title_ar',
                'meta_description_en',
                'meta_description_ar',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            $table->string('meta_title_en')->nullable();
            $table->string('meta_title_ar')->nullable();
            $table->text('meta_description_en')->nullable();
            $table->text('meta_description_ar')->nullable();
        });

        $seo = DB::table('landing_seo')->first();
        if ($seo) {
            $metaTitle = json_decode($seo->meta_title, true) ?: [];
            $metaDesc  = json_decode($seo->meta_description, true) ?: [];
            DB::table('landing_settings')->update([
                'meta_title_en'       => $metaTitle['en'] ?? null,
                'meta_title_ar'       => $metaTitle['ar'] ?? null,
                'meta_description_en' => $metaDesc['en'] ?? null,
                'meta_description_ar' => $metaDesc['ar'] ?? null,
            ]);
        }

        Schema::dropIfExists('landing_seo');

        Schema::table('landing_settings', function (Blueprint $table) {
            $table->dropColumn(['logo', 'logo_footer', 'favicon']);
        });
    }
};
