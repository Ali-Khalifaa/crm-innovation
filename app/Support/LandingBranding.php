<?php

namespace App\Support;

use App\Models\LandingSeo;
use App\Models\LandingSetting;

class LandingBranding
{
    public static function resolve(?LandingSetting $settings = null, ?LandingSeo $seo = null): array
    {
        $settings = $settings ?? LandingSetting::first();
        $seo      = $seo ?? LandingSeo::where('is_active', true)->first();
        $locale   = app()->getLocale() === 'ar' ? 'ar' : 'en';

        $siteName = $settings
            ? ($locale === 'ar' ? $settings->site_name_ar : $settings->site_name_en)
            : 'CRM Innovation';

        $logo = $settings?->logo
            ? asset(ltrim(upload_general_url($settings->logo), '/'))
            : asset('landing/img/logo.png');

        $logoFooter = $settings?->logo_footer
            ? asset(ltrim(upload_general_url($settings->logo_footer), '/'))
            : ($settings?->logo
                ? asset(ltrim(upload_general_url($settings->logo), '/'))
                : asset('landing/img/logo2.png'));

        $favicon = $settings?->favicon
            ? asset(ltrim(upload_general_url($settings->favicon), '/'))
            : asset('landing/img/favicon.png');

        $seoText = function (?string $field, string $fallback = '') use ($seo, $locale) {
            if (! $seo) {
                return $fallback;
            }

            return $seo->localized($field, $locale) ?: $fallback;
        };

        $pageTitle = $seoText('meta_title', $siteName);
        $pageDescription = $seoText('meta_description');
        $pageKeywords = $seoText('meta_keywords');
        $ogTitle = $seoText('og_title', $pageTitle);
        $ogDescription = $seoText('og_description', $pageDescription);
        $twitterTitle = $seoText('twitter_title', $ogTitle);
        $twitterDescription = $seoText('twitter_description', $ogDescription);

        $ogImage = $seo?->og_image
            ? asset(ltrim(upload_general_url($seo->og_image), '/'))
            : asset('landing/img/banner-3/1.png');

        $twitterImage = $seo?->twitter_image
            ? asset(ltrim(upload_general_url($seo->twitter_image), '/'))
            : $ogImage;

        $phone    = $settings?->phone ?: null;
        $phoneTel = $phone ? preg_replace('/[^\d+]/', '', $phone) : null;

        return [
            'layoutSettings'     => $settings,
            'layoutSeo'          => $seo,
            'layoutLocale'       => $locale,
            'landingSiteName'    => $siteName,
            'landingLogo'        => $logo,
            'landingLogoFooter'  => $logoFooter,
            'landingFavicon'     => $favicon,
            'pageTitle'          => $pageTitle,
            'pageDescription'    => $pageDescription,
            'pageKeywords'       => $pageKeywords,
            'ogTitle'            => $ogTitle,
            'ogDescription'      => $ogDescription,
            'twitterTitle'       => $twitterTitle,
            'twitterDescription' => $twitterDescription,
            'ogImage'            => $ogImage,
            'twitterImage'       => $twitterImage,
            'robots'             => $seo?->robots ?: 'index, follow, max-image-preview:large',
            'author'             => $seo?->author ?: $siteName,
            'themeColor'         => $seo?->theme_color ?: '#246bfd',
            'canonicalUrl'       => $seo?->canonical_url ?: url()->current(),
            'ogLocale'           => $locale === 'ar' ? 'ar_AR' : 'en_US',
            'landingPhone'       => $phone,
            'landingPhoneTel'    => $phoneTel,
        ];
    }
}
