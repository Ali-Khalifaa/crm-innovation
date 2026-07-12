<?php

namespace App\Http\Controllers;

use App\Models\LandingAbout;
use App\Models\LandingBlogPost;
use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingHero;
use App\Models\LandingHowItWork;
use App\Models\LandingPartner;
use App\Models\LandingSetting;
use App\Models\LandingStat;
use App\Models\LandingTeamMember;
use App\Models\LandingTestimonial;
use Modules\Plans\Models\Plan;

class LandingController extends Controller
{
    public function index()
    {
        $plans      = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $hero       = LandingHero::first();
        $stats      = LandingStat::where('is_active', true)->orderBy('sort_order')->get();
        $features   = LandingFeature::where('is_active', true)->orderBy('sort_order')->get();
        $howItWorks = LandingHowItWork::where('is_active', true)->orderBy('sort_order')->get();
        $faqs       = LandingFaq::where('is_active', true)->orderBy('sort_order')->get();
        $partners      = LandingPartner::where('is_active', true)->where('image', '!=', '')->orderBy('sort_order')->get();
        $settings      = LandingSetting::first();
        $about         = LandingAbout::where('is_active', true)->first();
        $testimonials  = LandingTestimonial::where('is_active', true)->orderBy('sort_order')->get();
        $team          = LandingTeamMember::where('is_active', true)->orderBy('sort_order')->get();
        $blogPosts     = LandingBlogPost::where('is_published', true)->orderBy('sort_order')->orderByDesc('published_at')->take(3)->get();

        return view('landing.index', compact('plans', 'hero', 'stats', 'features', 'howItWorks', 'faqs', 'partners', 'settings', 'about', 'testimonials', 'team', 'blogPosts'));
    }

    public function pricing()
    {
        $plans    = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $settings = LandingSetting::first();

        return view('landing.pricing', compact('plans', 'settings'));
    }

    public function register()
    {
        $plans    = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $settings = LandingSetting::first();

        return view('landing.register', compact('plans', 'settings'));
    }

    public function login()
    {
        $settings = LandingSetting::first();

        return view('landing.login', compact('settings'));
    }
}
