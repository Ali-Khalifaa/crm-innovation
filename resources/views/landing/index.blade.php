@extends('landing.layouts.Index.master')

@section('content')

    <!-- banner start -->
    @php
        $locale = app()->getLocale();
        $isAr   = $locale === 'ar';
        $lang = $isAr ? 'ar' : 'en';
        $heroText = fn ($field, $key = null) => $key
            ? ($hero?->{$field}[$key][$lang] ?? '')
            : ($hero?->{$field}[$lang] ?? '');
        $itemText = fn ($item, $field) => $item->{$field}[$lang] ?? '';
        $defaultCardIcon = asset('landing/img/multiple-users-silhouette.png');
        $itemImage = fn ($path) => $path ? asset(ltrim(upload_general_url($path), '/')) : $defaultCardIcon;
        $statBg = $statSection?->bg_image
            ? asset(ltrim(upload_general_url($statSection->bg_image), '/'))
            : asset('landing/img/bg/5.png');
        $heroBg = $hero?->bg_image
            ? asset('upload/general/' . $hero->bg_image)
            : asset('landing/img/banner-3/5.png');
        $heroBgOverlay = $hero?->bg_overlay_image
            ? asset('upload/general/' . $hero->bg_overlay_image)
            : asset('landing/img/banner-3/4.png');
    @endphp
    @if($hero?->is_active)
    <div class="banner-area bg-relative banner-area-2 bg-cover" id="hero" style="background-image: url({{ $heroBg }});">
        <img class="bg-img-2" src="{{ $heroBgOverlay }}" alt="img">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 align-self-center wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.3s">
                    <div class="banner-inner ps-xl-5">
                        @if($heroText('subtitle'))
                            <h6 class="subtitle wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.3s">{{ $heroText('subtitle') }}</h6>
                        @endif
                        <h1 class="title wow animated fadeInLeft arabic-title" data-wow-duration="0.8s" data-wow-delay="0.6s">
                            @foreach($hero->headline ?? [] as $segment)
                                @php
                                    $segmentText = $segment['title'][$lang] ?? '';
                                    $isHighlighted = !empty($segment['check']);
                                @endphp
                                @if($isHighlighted)
                                    <span>{{ $segmentText }}</span>
                                @else
                                    {{ $segmentText }}
                                @endif
                            @endforeach
                        </h1>
                        @if($heroText('description'))
                            <p class="content ps-xl-5 wow animated fadeInLeft arabic-subtitle" data-wow-duration="0.8s" data-wow-delay="0.9s">{{ $heroText('description') }}</p>
                        @endif
                        <div class="d-flex flex-wrap gap-3 wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="1.1s">
                            @if($heroText('btn_primary', 'text') && ($hero->btn_primary['url'] ?? null))
                                <a class="btn btn-border-base" href="{{ $hero->btn_primary['url'] }}">
                                    @if(!empty($hero->btn_primary['icon']))
                                        <i class="fa {{ $hero->btn_primary['icon'] }} me-2"></i>
                                    @endif
                                    {{ $heroText('btn_primary', 'text') }}
                                </a>
                            @endif
                            @if($heroText('btn_secondary', 'text') && ($hero->btn_secondary['url'] ?? null))
                                <div class="d-inline-block align-self-center wow animated fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.7s">
                                    <a class="video-play-btn-hover" href="{{ $hero->btn_secondary['url'] }}">
                                        <img src="{{ asset('landing/img/video.svg') }}" alt="img">
                                        <h6 class="d-inline-block">{{ $heroText('btn_secondary', 'text') }}</h6>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if($heroSlides->isNotEmpty())
                <div class="col-lg-6 col-md-10 wow animated fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.3s">
                    <div class="banner-thumb-3">
                        <div class="main-img-wrap">
                            <div class="hero-img-slider">
                                @foreach($heroSlides as $index => $slide)
                                <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-focus="center">
                                    <div class="hero-img-frame">
                                        <img class="main-img"
                                            src="{{ $slide->image ? upload_general_url($slide->image) : asset('landing/img/banner-3/1.png') }}"
                                            alt="{{ $slide->title[$lang] ?? '' }}">
                                    </div>
                                    <div class="hero-slide-caption">
                                        <span class="hero-slide-badge">{{ str_pad($slide->sort_order ?: ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                        <div>
                                            <strong>{{ $slide->title[$lang] ?? '' }}</strong>
                                            @if(!empty($slide->subtitle[$lang]))
                                                <p>{{ $slide->subtitle[$lang] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="hero-slider-dots" aria-label="{{ $isAr ? 'شرائح العرض' : 'Hero slides' }}">
                                @foreach($heroSlides as $index => $slide)
                                    <button type="button" class="{{ $index === 0 ? 'active' : '' }}" aria-label="{{ $isAr ? 'الشريحة' : 'Slide' }} {{ $index + 1 }}"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
    <!-- banner end -->

    <!-- partner start -->
    @if($partners->isNotEmpty())
    <div class="partner-14" id="partners">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="partner__inner">
                        <div class="crm-partner-slider owl-carousel">
                            @foreach($partners as $partner)
                            <div class="partner__slider-single">
                                @if($partner->url)
                                <a href="{{ $partner->url }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset(ltrim(upload_general_url($partner->image), '/')) }}" alt="{{ $partner->name ?: 'شريك' }}">
                                </a>
                                @else
                                <img src="{{ asset(ltrim(upload_general_url($partner->image), '/')) }}" alt="{{ $partner->name ?: 'شريك' }}">
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- partner end -->

    <!-- Problems Section Start -->
    @if($problemSection?->is_active && $problemItems->isNotEmpty())
    @php
        $problemText = fn ($field, $key = null) => $key
            ? ($problemSection?->{$field}[$key][$lang] ?? '')
            : ($problemSection?->{$field}[$lang] ?? '');
    @endphp
    <div class="about-area pd-top-120 pd-bottom-90" id="problems">
        <div class="container">
            <div class="section-title text-center wow animated fadeInUp" data-wow-duration="0.8s">
                @if($problemText('subtitle'))
                    <h6 class="sub-title">{{ $problemText('subtitle') }}</h6>
                @endif
                <h2 class="title arabic-title">
                    @foreach($problemSection->headline ?? [] as $segment)
                        @php
                            $segmentText = $segment['title'][$lang] ?? '';
                            $isHighlighted = !empty($segment['check']);
                        @endphp
                        @if($isHighlighted)
                            <span>{{ $segmentText }}</span>
                        @else
                            {{ $segmentText }}
                        @endif
                    @endforeach
                </h2>
                @if($problemText('intro'))
                    <p class="content problems-intro">{{ $problemText('intro') }}</p>
                @endif
            </div>
            @if(is_array($problemSection->insights) && count($problemSection->insights))
            <div class="problems-insight wow animated fadeInUp" data-wow-duration="0.8s">
                @foreach($problemSection->insights as $insight)
                <div class="insight-item">
                    <span class="insight-number">{{ $insight['value'][$lang] ?? '' }}</span>
                    <span class="insight-label">{{ $insight['label'][$lang] ?? '' }}</span>
                </div>
                @endforeach
            </div>
            @endif
            <div class="row">
                @foreach($problemItems as $index => $item)
                <div class="col-lg-4 col-md-6 wow animated fadeInUp" data-wow-duration="0.8s" @if($index > 0) data-wow-delay="{{ number_format($index * 0.3, 1) }}s" @endif>
                    <div class="problem-card">
                        @if($itemText($item, 'impact_label'))
                            <span class="problem-impact">{{ $itemText($item, 'impact_label') }}</span>
                        @endif
                        <div class="problem-icon">
                            <img src="{{ $item->image ? asset(ltrim(upload_general_url($item->image), '/')) : $defaultCardIcon }}"
                                alt="{{ $itemText($item, 'title') }}">
                        </div>
                        <h5>{{ $itemText($item, 'title') }}</h5>
                        <p>{{ $itemText($item, 'description') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @if(($problemSection->cta['bridge'][$lang] ?? null) || ($problemSection->cta['text'][$lang] ?? null))
            <div class="problems-bridge wow animated fadeInUp" data-wow-duration="0.8s">
                @if($problemSection->cta['bridge'][$lang] ?? null)
                    <p>{{ $problemSection->cta['bridge'][$lang] }}</p>
                @endif
                @if(($problemSection->cta['text'][$lang] ?? null) && ($problemSection->cta['url'] ?? null))
                    <a class="btn btn-base" href="{{ $problemSection->cta['url'] }}">{{ $problemSection->cta['text'][$lang] }}</a>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif
    <!-- Problems Section End -->

    <!-- Features Section Start -->
    @include('landing.partials._features')
    <!-- Features Section End -->

    <!-- Dashboard Preview Section Start -->
    @include('landing.partials._solutions')
    <!-- Dashboard Preview Section End -->

    <!-- How It Works Section Start -->
    @include('landing.partials._how_it_works')
    <!-- How It Works Section End -->

    <!-- Why Choose Us Section Start -->
    @include('landing.partials._why_us')
    <!-- Why Choose Us Section End -->

    <!-- Statistics Section Start -->
    @include('landing.partials._statistics')
    <!-- Statistics Section End -->

    <!-- Pricing Section Start -->
    <div class="pricing-area pd-top-120 pd-bottom-90" id="pricing">
        <div class="container">
            <div class="section-title text-center wow animated fadeInUp" data-wow-duration="0.8s">
                <h6 class="sub-title">الأسعار</h6>
                <h2 class="title arabic-title">اختر <span>الخطة</span> التي <span>تناسبك</span></h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 wow animated fadeInUp" data-wow-duration="0.8s">
                    <div class="pricing-card">
                        <div class="pricing-icon"><i class="fas fa-gift"></i></div>
                        <div class="price">$0 <sub>/شهرياً</sub></div>
                        <div class="plan-name">مجاني</div>
                        <ul>
                            <li><i class="fa fa-check"></i> حتى 10 عملاء</li>
                            <li><i class="fa fa-check"></i> إدارة العملاء الأساسية</li>
                            <li><i class="fa fa-check"></i> إنشاء فواتير محدودة</li>
                            <li><i class="fa fa-check"></i> دعم فني عبر البريد</li>
                            <li class="disabled"><i class="fa fa-times"></i> ميزات متقدمة</li>
                            <li class="disabled"><i class="fa fa-times"></i> تقارير شاملة</li>
                        </ul>
                        <a class="btn btn-border-base w-100" href="register.html">ابدأ الآن</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.3s">
                    <div class="pricing-card featured">
                        <span class="pricing-badge">الأكثر شعبية</span>
                        <div class="pricing-icon"><i class="fas fa-star"></i></div>
                        <div class="price">$29 <sub>/شهرياً</sub></div>
                        <div class="plan-name">احترافي</div>
                        <ul>
                            <li><i class="fa fa-check"></i> حتى 100 عميل</li>
                            <li><i class="fa fa-check"></i> إدارة العملاء الكاملة</li>
                            <li><i class="fa fa-check"></i> فواتير غير محدودة</li>
                            <li><i class="fa fa-check"></i> تقارير متقدمة</li>
                            <li><i class="fa fa-check"></i> دعم فني أولوي</li>
                            <li class="disabled"><i class="fa fa-times"></i> API</li>
                        </ul>
                        <a class="btn btn-base w-100" href="register.html">ابدأ الآن</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.6s">
                    <div class="pricing-card">
                        <div class="pricing-icon"><i class="fas fa-building"></i></div>
                        <div class="price">$99 <sub>/شهرياً</sub></div>
                        <div class="plan-name">شركات</div>
                        <ul>
                            <li><i class="fa fa-check"></i> عملاء غير محدودين</li>
                            <li><i class="fa fa-check"></i> جميع ميزات الخطط الأخرى</li>
                            <li><i class="fa fa-check"></i> API كامل</li>
                            <li><i class="fa fa-check"></i> دعم فني 24/7</li>
                            <li><i class="fa fa-check"></i> تدريب مخصص</li>
                            <li><i class="fa fa-check"></i> Multi-Tenant</li>
                        </ul>
                        <a class="btn btn-border-base w-100" href="register.html">ابدأ الآن</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pricing Section End -->

    <!-- Testimonials Section Start -->
    @include('landing.partials._testimonials')
    <!-- Testimonials Section End -->

    <!-- FAQ Section Start -->
    @include('landing.partials._faq')
    <!-- FAQ Section End -->

    <!-- CTA Section Start -->
    @include('landing.partials._contact')
    <!-- CTA Section End -->

@endsection
