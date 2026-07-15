<?php

namespace App\Http\Controllers;

use App\Models\LandingContactSection;
use App\Models\LandingFaq;
use App\Models\LandingFaqSection;
use App\Models\LandingFeatureItem;
use App\Models\LandingFeatureSection;
use App\Models\LandingHero;
use App\Models\LandingHeroSlide;
use App\Models\LandingHowItem;
use App\Models\LandingHowSection;
use App\Models\LandingLegalPage;
use App\Models\LandingNewsletterSection;
use App\Models\LandingPartner;
use App\Models\LandingProblemItem;
use App\Models\LandingProblemSection;
use App\Models\LandingSetting;
use App\Models\LandingSeo;
use App\Models\LandingSolutionPoint;
use App\Models\LandingSolutionSection;
use App\Models\LandingSolutionSlide;
use App\Models\LandingStatItem;
use App\Models\LandingStatSection;
use App\Models\LandingTestimonial;
use App\Models\LandingTestimonialSection;
use App\Models\LandingWhyItem;
use App\Models\LandingWhySection;
use App\Support\LandingBranding;
use Modules\Plans\Models\Plan;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index', $this->sharedLayoutData([
            'plans'             => Plan::where('is_active', true)->orderBy('sort_order')->get(),
            'hero'              => LandingHero::where('is_active', true)->first(),
            'heroSlides'        => LandingHeroSlide::where('is_active', true)->orderBy('sort_order')->get(),
            'featureSection'    => LandingFeatureSection::where('is_active', true)->first(),
            'featureItems'      => LandingFeatureItem::where('is_active', true)->orderBy('sort_order')->get(),
            'howSection'        => LandingHowSection::where('is_active', true)->first(),
            'howItems'          => LandingHowItem::where('is_active', true)->orderBy('sort_order')->get(),
            'whySection'        => LandingWhySection::where('is_active', true)->first(),
            'whyItems'          => LandingWhyItem::where('is_active', true)->orderBy('sort_order')->get(),
            'statSection'       => LandingStatSection::where('is_active', true)->first(),
            'statItems'         => LandingStatItem::where('is_active', true)->orderBy('sort_order')->get(),
            'faqSection'        => LandingFaqSection::where('is_active', true)->first(),
            'faqItems'          => LandingFaq::where('is_active', true)->orderBy('sort_order')->get(),
            'partners'          => LandingPartner::where('is_active', true)
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->orderBy('sort_order')
                ->get(),
            'problemSection'    => LandingProblemSection::where('is_active', true)->first(),
            'problemItems'      => LandingProblemItem::where('is_active', true)->orderBy('sort_order')->get(),
            'solutionSection'   => LandingSolutionSection::where('is_active', true)->first(),
            'solutionSlides'    => LandingSolutionSlide::where('is_active', true)->orderBy('sort_order')->get(),
            'solutionPoints'    => LandingSolutionPoint::where('is_active', true)->orderBy('sort_order')->get(),
            'testimonialSection'=> LandingTestimonialSection::where('is_active', true)->first(),
            'testimonials'      => LandingTestimonial::where('is_active', true)->orderBy('sort_order')->get(),
            'contactSection'    => LandingContactSection::where('is_active', true)->first(),
        ]));
    }

    public function pricing()
    {
        return view('landing.pricing', $this->sharedLayoutData([
            'plans' => Plan::where('is_active', true)->orderBy('sort_order')->get(),
        ]));
    }

    public function register()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $planSlug = request('plan');
        $defaultPlan = ($planSlug ? $plans->firstWhere('slug', $planSlug) : null)
            ?? $plans->firstWhere('is_featured', true)
            ?? $plans->first();

        return view('landing.register', $this->authLayoutData([
            'plans'       => $plans,
            'defaultPlan' => $defaultPlan,
        ]));
    }

    public function login()
    {
        return view('landing.login', $this->authLayoutData());
    }

    public function privacy()
    {
        return $this->legalPage(LandingLegalPage::TYPE_PRIVACY);
    }

    public function terms()
    {
        return $this->legalPage(LandingLegalPage::TYPE_TERMS);
    }

    private function legalPage(int $type)
    {
        $page = LandingLegalPage::where('type', $type)->where('is_active', true)->firstOrFail();

        return view('landing.legal', $this->sharedLayoutData([
            'page' => $page,
        ]));
    }

    private function sharedLayoutData(array $extra = []): array
    {
        $settings = LandingSetting::first();
        $seo      = LandingSeo::where('is_active', true)->first();

        return array_merge(
            LandingBranding::resolve($settings, $seo),
            [
                'settings'          => $settings,
                'seo'               => $seo,
                'newsletterSection' => LandingNewsletterSection::where('is_active', true)->first(),
            ],
            $extra
        );
    }

    private function authLayoutData(array $extra = []): array
    {
        $settings = LandingSetting::first();

        return array_merge(
            LandingBranding::resolve($settings, null),
            ['settings' => $settings],
            $extra
        );
    }
}
