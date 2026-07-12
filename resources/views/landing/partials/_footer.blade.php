@php
    $footerSettings = isset($settings) ? $settings : \App\Models\LandingSetting::first();
    $footerIsAr     = app()->getLocale() === 'ar';
    $footerSiteName = $footerSettings
        ? ($footerIsAr ? $footerSettings->site_name_ar : $footerSettings->site_name_en)
        : 'CRM Innovation';
@endphp

<footer class="footer-area bg-black bg-cover">
    <div class="footer-subscribe">
        <div class="container">
            <div class="footer-subscribe-inner bg-cover wow animated fadeInUp" data-wow-duration="0.8s"
                style="background-image: url('{{ asset('landing/img/bg/6.png') }}');">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2 class="mb-lg-0 mb-3">{{ __('crm.footer_trial_title') }}</h2>
                    </div>
                    <div class="col-lg-6 text-lg-end">
                        <a href="{{ route('register') }}" class="btn btn-base border-radius-0">
                            {{ __('crm.footer_trial_btn') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="widget widget_about wow animated fadeInUp" data-wow-duration="0.8s">
                    <div class="thumb mb-3">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ $footerSiteName }}" style="max-height:50px;">
                    </div>
                    <div class="details">
                        <p>{{ __('crm.footer_about_desc') }}</p>
                        @if($footerSettings && $footerSettings->email)
                        <p class="mt-2">
                            <i class="fa fa-envelope me-2"></i>
                            <a href="mailto:{{ $footerSettings->email }}" class="text-white-50">
                                {{ $footerSettings->email }}
                            </a>
                        </p>
                        @endif
                        @if($footerSettings && $footerSettings->phone)
                        <p class="mt-1">
                            <i class="fa fa-phone me-2"></i>
                            <a href="tel:{{ $footerSettings->phone }}" class="text-white-50">
                                {{ $footerSettings->phone }}
                            </a>
                        </p>
                        @endif
                        @if($footerSettings && $footerSettings->address_en)
                        <p class="mt-1">
                            <i class="fa fa-map-marker-alt me-2"></i>
                            <span class="text-white-50">
                                {{ $footerIsAr ? $footerSettings->address_ar : $footerSettings->address_en }}
                            </span>
                        </p>
                        @endif
                    </div>
                    <ul class="social-media mt-3">
                        @if($footerSettings && $footerSettings->facebook)
                        <li><a href="{{ $footerSettings->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                        @endif
                        @if($footerSettings && $footerSettings->twitter)
                        <li><a href="{{ $footerSettings->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                        @endif
                        @if($footerSettings && $footerSettings->linkedin)
                        <li><a href="{{ $footerSettings->linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                        @endif
                        @if($footerSettings && $footerSettings->instagram)
                        <li><a href="{{ $footerSettings->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a></li>
                        @endif
                        @if($footerSettings && $footerSettings->youtube)
                        <li><a href="{{ $footerSettings->youtube }}" target="_blank"><i class="fab fa-youtube"></i></a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 offset-lg-1">
                <div class="widget widget_nav_menu wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.3s">
                    <h4 class="widget-title">{{ __('crm.footer_product') }}</h4>
                    <ul>
                        <li>
                            <a href="{{ route('landing') }}#features">
                                <i class="fas fa-arrow-right"></i> {{ __('crm.footer_features_link') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pricing') }}">
                                <i class="fas fa-arrow-right"></i> {{ __('crm.footer_pricing_link') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}">
                                <i class="fas fa-arrow-right"></i> {{ __('crm.footer_free_trial') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('login') }}">
                                <i class="fas fa-arrow-right"></i> {{ __('crm.footer_login_link') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="widget widget_nav_menu wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.6s">
                    <h4 class="widget-title">{{ __('crm.footer_company') }}</h4>
                    <ul>
                        <li>
                            <a href="#">
                                <i class="fas fa-arrow-right"></i> {{ __('crm.footer_about_link') }}
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-arrow-right"></i> {{ __('crm.footer_blog_link') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('landing') }}#contact">
                                <i class="fas fa-arrow-right"></i> {{ __('crm.footer_contact_link') }}
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fas fa-arrow-right"></i> {{ __('crm.footer_privacy_link') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="widget widget_about wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.9s">
                    <h4 class="widget-title">{{ __('crm.footer_crm_features') }}</h4>
                    <ul>
                        <li><i class="fas fa-check text-success me-2"></i> {{ __('crm.footer_feat_1') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i> {{ __('crm.footer_feat_2') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i> {{ __('crm.footer_feat_3') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i> {{ __('crm.footer_feat_4') }}</li>
                        <li><i class="fas fa-check text-success me-2"></i> {{ __('crm.footer_feat_5') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-6 align-self-center">
                    <p>{{ str_replace(':year', date('Y'), __('crm.footer_copyright')) }}</p>
                </div>
                <div class="col-md-6 text-lg-end">
                    <a href="#">{{ __('crm.footer_terms_link') }}</a>
                    <a href="#">{{ __('crm.footer_privacy_link') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
