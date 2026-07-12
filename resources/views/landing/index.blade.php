@extends('landing.layout')

@php
    $locale = app()->getLocale();
    $isAr   = $locale === 'ar';
@endphp

@section('title', $settings
    ? ($isAr ? $settings->meta_title_ar : $settings->meta_title_en)
    : ($isAr ? 'CRM إنوفيشن — CRM ذكي لنمو أعمالك' : 'CRM Innovation — Smart CRM for Growing Businesses'))

@section('meta_description', $settings
    ? ($isAr ? $settings->meta_description_ar : $settings->meta_description_en)
    : '')

@section('content')

{{-- ===== HERO ===== --}}
<div class="banner-14 banner-area bg-relative banner-area-1 pd-bottom-100 bg-cover"
    style="background-image: url('{{ asset('landing/img/home-14/banner/banner-bg.png') }}');">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8 col-xl-6">
                <div class="banner-inner">
                    <h6 class="subtitle wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.3s">
                        {{ $hero ? ($isAr ? $hero->subtitle_ar : $hero->subtitle_en) : __('crm.hero_subtitle') }}
                    </h6>
                    <h2 class="title wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.6s">
                        {!! $hero ? ($isAr ? $hero->title_ar : $hero->title_en) : __('crm.hero_title') !!}
                    </h2>
                    <p class="content pe-xl-5 wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.9s">
                        {{ $hero ? ($isAr ? $hero->description_ar : $hero->description_en) : __('crm.hero_desc') }}
                    </p>
                    <a class="btn btn-base wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.6s"
                        href="{{ $hero && $hero->btn_primary_url ? $hero->btn_primary_url : route('register') }}">
                        {{ $hero ? ($isAr ? $hero->btn_primary_text_ar : $hero->btn_primary_text_en) : __('crm.hero_btn_trial') }}
                        <i class="fa fa-arrow-right ms-2"></i>
                    </a>
                    <a class="btn btn-border-base ms-3 wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.7s"
                        href="{{ $hero && $hero->btn_secondary_url ? $hero->btn_secondary_url : route('pricing') }}">
                        {{ $hero ? ($isAr ? $hero->btn_secondary_text_ar : $hero->btn_secondary_text_en) : __('crm.hero_btn_pricing') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <img class="animate-img-1 top_image_bounce" src="{{ asset('landing/img/banner/2.png') }}" alt="">
    <img class="animate-img-2 left_image_bounce" src="{{ asset('landing/img/banner/5.svg') }}" alt="">
    <div class="banner-14-thumb">
        @if($hero && $hero->image)
            <img src="{{ asset('upload/general/' . $hero->image) }}" alt="">
        @else
            <img src="{{ asset('landing/img/home-14/banner/banner-thumb.png') }}" alt="">
        @endif
    </div>
    <div class="banner-13-thumb-sm">
        <img src="{{ asset('landing/img/home-14/banner/banner-shape.png') }}" alt="">
    </div>
</div>

{{-- ===== PARTNERS / TRUSTED BY ===== --}}
@if(isset($partners) && $partners->isNotEmpty())
<div class="partner-14">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="partner__inner">
                    <div class="partner__slider owl-carousel">
                        @foreach($partners as $partner)
                        <div class="partner__slider-single">
                            @if($partner->url)
                            <a href="{{ $partner->url }}" target="_blank" rel="noopener">
                                <img src="{{ asset('upload/general/' . $partner->image) }}"
                                     alt="{{ $partner->name ?? '' }}">
                            </a>
                            @else
                            <img src="{{ asset('upload/general/' . $partner->image) }}"
                                 alt="{{ $partner->name ?? '' }}">
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="partner-14">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="partner__inner">
                    <div class="partner__slider owl-carousel">
                        @foreach(['one','two','three','four','five','six'] as $p)
                        <div class="partner__slider-single">
                            <img src="{{ asset('landing/img/home-14/partner/' . $p . '.png') }}" alt="">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ===== STATS ===== --}}
<div class="counter-area bg-relative bg-cover pd-top-110 pd-bottom-100"
    style="background-image: url('{{ asset('landing/img/bg/5.png') }}');">
    <div class="container pd-bottom-90">
        <div class="row">
            @if($stats->isNotEmpty())
                @foreach($stats as $i => $stat)
                <div class="col-lg-3 col-md-6 wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="{{ $i * 0.2 }}s">
                    <div class="single-counter-inner">
                        <div class="thumb">
                            <img src="{{ asset('landing/img/icon/5.svg') }}" alt="">
                        </div>
                        <h2 class="text-white mt-4 mb-2">
                            <span class="counter">{{ $isAr ? $stat->value_ar : $stat->value_en }}</span>{{ $stat->suffix }}
                        </h2>
                        <p class="text-white mb-0">{{ $isAr ? $stat->label_ar : $stat->label_en }}</p>
                    </div>
                </div>
                @endforeach
            @else
                @php
                $defaultStats = [
                    ['num' => '2500', 'suffix' => '+', 'label' => __('crm.stats_users'),       'delay' => '0'],
                    ['num' => '98',   'suffix' => '%', 'label' => __('crm.stats_satisfaction'), 'delay' => '0.2'],
                    ['num' => '1',    'suffix' => 'M+','label' => __('crm.stats_contacts'),     'delay' => '0.4'],
                    ['num' => '50',   'suffix' => '+', 'label' => __('crm.stats_countries'),    'delay' => '0.6'],
                ];
                @endphp
                @foreach($defaultStats as $stat)
                <div class="col-lg-3 col-md-6 wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="{{ $stat['delay'] }}s">
                    <div class="single-counter-inner">
                        <div class="thumb">
                            <img src="{{ asset('landing/img/icon/5.svg') }}" alt="">
                        </div>
                        <h2 class="text-white mt-4 mb-2">
                            <span class="counter">{{ $stat['num'] }}</span>{{ $stat['suffix'] }}
                        </h2>
                        <p class="text-white mb-0">{{ $stat['label'] }}</p>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{-- ===== ABOUT ===== --}}
@if(isset($about) && $about)
<div class="about-area pd-top-120 pd-bottom-90">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0 wow animated fadeInLeft" data-wow-duration="0.8s">
                <div class="about-thumb-inner pe-xl-4">
                    @if($about->image)
                    <img src="{{ asset('upload/general/' . $about->image) }}" alt="{{ $isAr ? $about->title_ar : $about->title_en }}" class="w-100" style="border-radius:16px;">
                    @else
                    <img src="{{ asset('landing/img/about/1.png') }}" alt="" class="w-100" style="border-radius:16px;">
                    @endif
                </div>
            </div>
            <div class="col-lg-6 wow animated fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.3s">
                <div class="section-title mt-5 mt-lg-0">
                    <h6 class="sub-title">{{ $isAr ? $about->subtitle_ar : $about->subtitle_en }}</h6>
                    <h2 class="title">{!! $isAr ? $about->title_ar : $about->title_en !!}</h2>
                    <p class="content pb-4">{{ $isAr ? $about->description_ar : $about->description_en }}</p>
                    @if($about->box1_title_en)
                    <div class="d-flex gap-3 mb-3 align-items-start">
                        <div class="flex-shrink-0" style="width:48px;height:48px;background:var(--crm-primary);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas {{ $about->box1_icon ?? 'fa-bullseye' }} text-white fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $isAr ? $about->box1_title_ar : $about->box1_title_en }}</h6>
                            <p class="mb-0 text-muted" style="font-size:14px;">{{ $isAr ? $about->box1_desc_ar : $about->box1_desc_en }}</p>
                        </div>
                    </div>
                    @endif
                    @if($about->box2_title_en)
                    <div class="d-flex gap-3 align-items-start">
                        <div class="flex-shrink-0" style="width:48px;height:48px;background:var(--crm-accent);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                            <i class="fas {{ $about->box2_icon ?? 'fa-rocket' }} text-white fa-lg"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ $isAr ? $about->box2_title_ar : $about->box2_title_en }}</h6>
                            <p class="mb-0 text-muted" style="font-size:14px;">{{ $isAr ? $about->box2_desc_ar : $about->box2_desc_en }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ===== FEATURES ===== --}}
<div class="service-area bg-relative pd-top-120 pd-bottom-90" id="features">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-7 text-center">
                <div class="section-title wow animated fadeInUp" data-wow-duration="0.8s">
                    <h6 class="sub-title text-uppercase">{{ __('crm.features_subtitle') }}</h6>
                    <h2 class="title">{!! __('crm.features_title') !!}</h2>
                    <p class="content">{{ __('crm.features_desc') }}</p>
                </div>
            </div>
        </div>
        <div class="row g-4">
            @if($features->isNotEmpty())
                @foreach($features as $i => $feature)
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="{{ $i * 0.1 }}s">
                        <div class="feature-icon">
                            <i class="fas {{ $feature->icon }}"></i>
                        </div>
                        <h5>{{ $isAr ? $feature->title_ar : $feature->title_en }}</h5>
                        <p>{{ $isAr ? $feature->description_ar : $feature->description_en }}</p>
                    </div>
                </div>
                @endforeach
            @else
                @php
                $defaultFeatures = [
                    ['icon' => 'fa-address-book',       'title' => __('crm.feature_1_title'), 'desc' => __('crm.feature_1_desc')],
                    ['icon' => 'fa-funnel-dollar',       'title' => __('crm.feature_2_title'), 'desc' => __('crm.feature_2_desc')],
                    ['icon' => 'fa-tasks',               'title' => __('crm.feature_3_title'), 'desc' => __('crm.feature_3_desc')],
                    ['icon' => 'fa-file-invoice-dollar', 'title' => __('crm.feature_4_title'), 'desc' => __('crm.feature_4_desc')],
                    ['icon' => 'fa-chart-line',          'title' => __('crm.feature_5_title'), 'desc' => __('crm.feature_5_desc')],
                    ['icon' => 'fa-users',               'title' => __('crm.feature_6_title'), 'desc' => __('crm.feature_6_desc')],
                ];
                @endphp
                @foreach($defaultFeatures as $i => $feature)
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="{{ $i * 0.1 }}s">
                        <div class="feature-icon">
                            <i class="fas {{ $feature['icon'] }}"></i>
                        </div>
                        <h5>{{ $feature['title'] }}</h5>
                        <p>{{ $feature['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{-- ===== HOW IT WORKS ===== --}}
<section class="total-area pd-top-80 pd-bottom-80 bg-cover"
    style="background-image: url('{{ asset('landing/img/bg/3.png') }}');">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-title wow animated fadeInUp" data-wow-duration="0.8s">
                    <h6 class="sub-title">{{ __('crm.how_subtitle') }}</h6>
                    <h2 class="title">{!! __('crm.how_title') !!}</h2>
                </div>
            </div>
        </div>
        <div class="row tc-p">
            @php
            $stepImages = ['landing/img/steps/two.png', 'landing/img/steps/one.png', 'landing/img/steps/three.png'];
            @endphp
            @if($howItWorks->isNotEmpty())
                @foreach($howItWorks as $i => $step)
                <div class="col-12 col-md-4">
                    <div class="total-single wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="{{ $i * 0.3 }}s">
                        <div class="thumb">
                            @if($step->image)
                                <img src="{{ asset('upload/general/' . $step->image) }}" alt="">
                            @else
                                <img src="{{ asset($stepImages[$i] ?? 'landing/img/steps/two.png') }}" alt="">
                            @endif
                            <span>{{ str_pad($step->step_number, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="content">
                            <h4>{{ $isAr ? $step->title_ar : $step->title_en }}</h4>
                            <p>{{ $isAr ? $step->description_ar : $step->description_en }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                @for($i = 1; $i <= 3; $i++)
                <div class="col-12 col-md-4">
                    <div class="total-single wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="{{ ($i - 1) * 0.3 }}s">
                        <div class="thumb">
                            <img src="{{ asset($stepImages[$i - 1]) }}" alt="">
                            <span>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="content">
                            <h4>{{ __('crm.step_' . $i . '_title') }}</h4>
                            <p>{{ __('crm.step_' . $i . '_desc') }}</p>
                        </div>
                    </div>
                </div>
                @endfor
            @endif
        </div>
    </div>
</section>

{{-- ===== PRICING ===== --}}
<div class="pricing-area pd-top-90 pd-bottom-90" id="pricing">
    <div class="container">
        <div class="section-title text-center wow animated fadeInUp" data-wow-duration="0.8s">
            <h6 class="sub-title">{{ __('crm.pricing_subtitle') }}</h6>
            <h2 class="title">{!! __('crm.pricing_title') !!}</h2>
            <p class="content">{{ __('crm.pricing_desc') }}</p>
        </div>
        <div class="row justify-content-center">
            @foreach($plans as $plan)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="single-pricing-inner style-3 {{ $plan->is_featured ? 'price-active' : '' }} wow animated fadeInUp"
                    data-wow-duration="0.8s" data-wow-delay="{{ $loop->index * 0.2 }}s">
                    @if($plan->is_featured)
                    <span class="pricing-badge">{{ __('crm.pricing_most_popular') }}</span>
                    @endif
                    <h2 class="mb-1">
                        ${{ number_format($plan->monthly_price, 0) }}
                        <sub>{{ __('crm.pricing_per_month') }}</sub>
                    </h2>
                    <p class="text-muted mb-1" style="font-size:13px;">
                        {{ $isAr ? 'أو' : 'or' }}
                        ${{ number_format($plan->yearly_price, 0) }}{{ __('crm.pricing_per_year') }}
                        ({{ __('crm.pricing_save') }} {{ round((1 - $plan->yearly_price / ($plan->monthly_price * 12)) * 100) }}%)
                    </p>
                    <h5 class="mt-3">{{ $plan->name }}</h5>
                    @if($plan->description)
                    <p class="text-muted mb-3" style="font-size:13px;">{{ $plan->description }}</p>
                    @endif
                    <ul>
                        <li>
                            <i class="fa fa-check"></i>
                            {{ $plan->max_users === 0 ? __('crm.pricing_unlimited') : $plan->max_users }}
                            {{ __('crm.pricing_users') }}
                        </li>
                        <li>
                            <i class="fa fa-check"></i>
                            {{ $plan->max_contacts === 0 ? __('crm.pricing_unlimited') : number_format($plan->max_contacts) }}
                            {{ __('crm.pricing_contacts_label') }}
                        </li>
                        @foreach($plan->features ?? [] as $feature)
                        <li><i class="fa fa-check"></i>{{ ucwords(str_replace('_', ' ', $feature)) }}</li>
                        @endforeach
                    </ul>
                    <a class="btn btn-black border-radius border-radius-0 w-100 mt-3"
                        href="{{ route('register') }}?plan={{ $plan->id }}">
                        {{ __('crm.pricing_get_started') }}
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-center text-muted mt-3">
            {{ __('crm.pricing_all_plans') }}
            <a href="{{ route('pricing') }}">{{ __('crm.pricing_compare') }}</a>
        </p>
    </div>
</div>

{{-- ===== TESTIMONIALS ===== --}}
@if(isset($testimonials) && $testimonials->isNotEmpty())
<div class="testimonial-area bg-cover pd-top-110 pd-bottom-90" id="testimonials"
    style="background-image: url('{{ asset('landing/img/bg/7.png') }}');">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-title wow animated fadeInUp" data-wow-duration="0.8s">
                    <h6 class="sub-title">{{ $isAr ? 'آراء العملاء' : 'TESTIMONIALS' }}</h6>
                    <h2 class="title">{!! $isAr ? 'ماذا <span>يقول عملاؤنا</span>' : 'What Our <span>Clients Say</span>' !!}</h2>
                </div>
            </div>
        </div>
        <div class="testimonial-slider-1 owl-carousel">
            @foreach($testimonials as $testimonial)
            <div class="item">
                <div class="single-testimonial-inner style-1">
                    <div class="row align-items-center mb-3">
                        <div class="col-auto">
                            <div class="thumb" style="width:60px;height:60px;border-radius:50%;overflow:hidden;background:#E2E8F0;display:flex;align-items:center;justify-content:center;">
                                @if($testimonial->photo)
                                <img src="{{ asset('upload/general/' . $testimonial->photo) }}" alt="{{ $testimonial->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                <i class="fas fa-user fa-xl text-muted"></i>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <h6 class="mb-0">{{ $testimonial->name }}</h6>
                            <small class="text-muted">{{ $isAr ? $testimonial->designation_ar : $testimonial->designation_en }}</small>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex gap-1">
                                @for($s = 1; $s <= 5; $s++)
                                <i class="fas fa-star {{ $s <= $testimonial->rating ? 'text-warning' : 'text-muted opacity-25' }}" style="font-size:12px;"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <p class="mb-0" style="font-style:italic;font-size:15px;line-height:1.7;">
                        "{{ $isAr ? $testimonial->review_ar : $testimonial->review_en }}"
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ===== TEAM ===== --}}
@if(isset($team) && $team->isNotEmpty())
<div class="team-area pd-top-110 pd-bottom-90">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-title wow animated fadeInUp" data-wow-duration="0.8s">
                    <h6 class="sub-title">{{ $isAr ? 'فريقنا' : 'OUR TEAM' }}</h6>
                    <h2 class="title">{!! $isAr ? 'تعرّف على <span>الفريق</span>' : 'Meet the <span>Team</span>' !!}</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center g-4">
            @foreach($team as $i => $member)
            <div class="col-md-6 col-lg-4">
                <div class="single-team-inner style-1 text-center wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="{{ $i * 0.15 }}s">
                    <div class="thumb" style="width:120px;height:120px;border-radius:50%;overflow:hidden;margin:0 auto 16px;background:#E2E8F0;display:flex;align-items:center;justify-content:center;">
                        @if($member->photo)
                        <img src="{{ asset('upload/general/' . $member->photo) }}" alt="{{ $isAr ? $member->name_ar : $member->name_en }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                        <i class="fas fa-user fa-2x text-muted"></i>
                        @endif
                    </div>
                    <div class="details">
                        <h5 class="mb-1">{{ $isAr ? $member->name_ar : $member->name_en }}</h5>
                        <p class="text-muted mb-3" style="font-size:13px;">{{ $isAr ? $member->role_ar : $member->role_en }}</p>
                        <div class="team-social-inner d-flex justify-content-center gap-2">
                            @if($member->facebook)
                            <a href="{{ $member->facebook }}" target="_blank" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if($member->twitter)
                            <a href="{{ $member->twitter }}" target="_blank" class="social-link"><i class="fab fa-twitter"></i></a>
                            @endif
                            @if($member->linkedin)
                            <a href="{{ $member->linkedin }}" target="_blank" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                            @endif
                            @if($member->instagram)
                            <a href="{{ $member->instagram }}" target="_blank" class="social-link"><i class="fab fa-instagram"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ===== BLOG ===== --}}
@if(isset($blogPosts) && $blogPosts->isNotEmpty())
<div class="blog-area pd-top-110 pd-bottom-90" style="background:#F8FAFC;">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-title wow animated fadeInUp" data-wow-duration="0.8s">
                    <h6 class="sub-title">{{ $isAr ? 'المدونة' : 'BLOG' }}</h6>
                    <h2 class="title">{!! $isAr ? 'آخر <span>المقالات</span>' : 'Latest <span>Articles</span>' !!}</h2>
                </div>
            </div>
        </div>
        <div class="row g-4">
            @foreach($blogPosts as $i => $post)
            <div class="col-md-6 col-lg-4 wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="{{ $i * 0.15 }}s">
                <div class="single-blog-list" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);">
                    <div class="thumb" style="height:200px;overflow:hidden;background:#E2E8F0;">
                        @if($post->image)
                        <img src="{{ asset('upload/general/' . $post->image) }}" alt="{{ $isAr ? $post->title_ar : $post->title_en }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                            <i class="fas fa-newspaper fa-3x text-muted opacity-50"></i>
                        </div>
                        @endif
                    </div>
                    <div class="details p-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span style="font-size:12px;color:var(--crm-text-muted);">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $post->published_at ? $post->published_at->format('M d, Y') : '' }}
                            </span>
                            <span class="text-muted">•</span>
                            <span style="font-size:12px;color:var(--crm-text-muted);">
                                <i class="fas fa-user me-1"></i>{{ $post->author_name }}
                            </span>
                        </div>
                        <h5 class="mb-2" style="font-size:17px;line-height:1.4;">{{ $isAr ? $post->title_ar : $post->title_en }}</h5>
                        <p class="mb-0 text-muted" style="font-size:14px;line-height:1.6;">{{ \Str::limit($isAr ? $post->excerpt_ar : $post->excerpt_en, 100) }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ===== CONTACT FORM ===== --}}
<div class="contact-form-area pd-top-110 pd-bottom-90" id="contact-form">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-title wow animated fadeInUp" data-wow-duration="0.8s">
                    <h6 class="sub-title">{{ $isAr ? 'تواصل معنا' : 'CONTACT US' }}</h6>
                    <h2 class="title">{!! $isAr ? 'كيف <span>يمكننا مساعدتك</span>؟' : 'How Can We <span>Help You</span>?' !!}</h2>
                    <p class="content">{{ $isAr ? 'فريقنا جاهز للإجابة على أسئلتك ومساعدتك في اختيار الباقة المناسبة.' : 'Our team is ready to answer your questions and help you choose the right plan.' }}</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(session('contact_success'))
                <div class="alert alert-success text-center mb-4">
                    {{ $isAr ? 'شكراً! تم إرسال رسالتك بنجاح وسنتواصل معك قريباً.' : 'Thank you! Your message has been sent successfully. We will contact you soon.' }}
                </div>
                @endif
                <form action="{{ route('contact.send') }}" method="POST" class="contact-form-inner wow animated fadeInUp" data-wow-duration="0.8s" style="background:#fff;padding:40px;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,0.08);">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label style="font-size:14px;font-weight:600;color:var(--crm-text);margin-bottom:6px;display:block;">
                                {{ $isAr ? 'الاسم الكامل' : 'Full Name' }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="{{ $isAr ? 'أدخل اسمك' : 'Enter your name' }}" required
                                style="border-radius:8px;padding:12px 16px;border:1.5px solid #E2E8F0;">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label style="font-size:14px;font-weight:600;color:var(--crm-text);margin-bottom:6px;display:block;">
                                {{ $isAr ? 'البريد الإلكتروني' : 'Email Address' }} <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="{{ $isAr ? 'أدخل بريدك الإلكتروني' : 'Enter your email' }}" required
                                style="border-radius:8px;padding:12px 16px;border:1.5px solid #E2E8F0;">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label style="font-size:14px;font-weight:600;color:var(--crm-text);margin-bottom:6px;display:block;">
                                {{ $isAr ? 'رقم الهاتف' : 'Phone Number' }}
                            </label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                class="form-control @error('phone') is-invalid @enderror"
                                placeholder="{{ $isAr ? 'أدخل رقم هاتفك' : 'Enter your phone' }}"
                                style="border-radius:8px;padding:12px 16px;border:1.5px solid #E2E8F0;">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label style="font-size:14px;font-weight:600;color:var(--crm-text);margin-bottom:6px;display:block;">
                                {{ $isAr ? 'الموضوع' : 'Subject' }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="subject" value="{{ old('subject') }}"
                                class="form-control @error('subject') is-invalid @enderror"
                                placeholder="{{ $isAr ? 'موضوع رسالتك' : 'Message subject' }}" required
                                style="border-radius:8px;padding:12px 16px;border:1.5px solid #E2E8F0;">
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label style="font-size:14px;font-weight:600;color:var(--crm-text);margin-bottom:6px;display:block;">
                                {{ $isAr ? 'الرسالة' : 'Message' }} <span class="text-danger">*</span>
                            </label>
                            <textarea name="message" rows="5"
                                class="form-control @error('message') is-invalid @enderror"
                                placeholder="{{ $isAr ? 'اكتب رسالتك هنا...' : 'Write your message here...' }}" required
                                style="border-radius:8px;padding:12px 16px;border:1.5px solid #E2E8F0;resize:none;">{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-base" style="padding:14px 48px;font-size:16px;">
                                <i class="fas fa-paper-plane me-2"></i>
                                {{ $isAr ? 'إرسال الرسالة' : 'Send Message' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===== FAQ ===== --}}
@if($faqs->isNotEmpty())
<div class="faq-area pd-top-90 pd-bottom-90" id="faq">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-title wow animated fadeInUp" data-wow-duration="0.8s">
                    <h6 class="sub-title">{{ __('crm.faq_subtitle') }}</h6>
                    <h2 class="title">{!! __('crm.faq_title') !!}</h2>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    @foreach($faqs as $i => $faq)
                    <div class="accordion-item wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="{{ $i * 0.1 }}s">
                        <h2 class="accordion-header" id="faqHeading{{ $faq->id }}">
                            <button class="accordion-button {{ $i > 0 ? 'collapsed' : '' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $faq->id }}"
                                aria-expanded="{{ $i === 0 ? 'true' : 'false' }}">
                                {{ $isAr ? $faq->question_ar : $faq->question_en }}
                            </button>
                        </h2>
                        <div id="faqCollapse{{ $faq->id }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ $isAr ? $faq->answer_ar : $faq->answer_en }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ===== CTA ===== --}}
<div class="contact-area" id="contact">
    <div class="container">
        <div class="contact-inner-1">
            <div class="row align-items-center tc-p">
                <div class="col-lg-6 wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.3s">
                    <div class="contact-inner">
                        <h6 class="subtitle">{{ __('crm.cta_subtitle') }}</h6>
                        <h2 class="title">{!! __('crm.cta_title') !!}</h2>
                        <p class="content pe-xl-5">{{ __('crm.cta_desc') }}</p>
                        <ul class="mt-3">
                            <li><i class="fas fa-check"></i> {{ __('crm.cta_no_card') }}</li>
                            <li><i class="fas fa-check"></i> {{ __('crm.cta_full_access') }}</li>
                            <li><i class="fas fa-check"></i> {{ __('crm.cta_cancel') }}</li>
                            <li><i class="fas fa-check"></i> {{ __('crm.cta_setup') }}</li>
                        </ul>
                        <a class="btn btn-base wow animated fadeInLeft mt-4" data-wow-duration="0.8s" data-wow-delay="0.6s"
                            href="{{ route('register') }}">{{ __('crm.cta_btn') }}</a>
                    </div>
                </div>
                <div class="col-lg-6 col-xl-5 offset-xl-1 wow animated fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.3s">
                    <div class="section-title mb-0" style="background:#F8FAFC;padding:32px;border-radius:16px;border:1px solid #E2E8F0;">
                        <h6 class="sub-title">{{ __('crm.cta_quick_title') }}</h6>
                        <h2 class="title" style="font-size:24px;">{{ __('crm.cta_create_title') }}</h2>
                        <p class="content">{{ __('crm.cta_fill_desc') }}</p>
                        <a href="{{ route('register') }}" class="btn btn-base w-100 mt-3" style="padding:14px;font-size:16px;">
                            <i class="fas fa-rocket me-2"></i> {{ __('crm.cta_start_trial') }}
                        </a>
                        <p class="text-center text-muted mt-3 mb-0" style="font-size:13px;">
                            {{ __('crm.cta_have_account') }}
                            <a href="{{ route('login') }}" style="color:var(--crm-primary);font-weight:600;">
                                {{ __('crm.cta_sign_in_link') }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
