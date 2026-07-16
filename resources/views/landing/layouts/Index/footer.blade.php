<footer class="footer-area bg-black bg-cover" id="footer">
@php
    $lang = app()->getLocale() === 'ar' ? 'ar' : 'en';
    $newsletterSection = $newsletterSection ?? \App\Models\LandingNewsletterSection::where('is_active', true)->first();
@endphp
    @if($newsletterSection?->is_active)
    @php
        $nlBg = $newsletterSection->bg_image
            ? asset(ltrim(upload_general_url($newsletterSection->bg_image), '/'))
            : asset('landing/img/bg/6.png');
        $nlTitle = $newsletterSection->title[$lang] ?? __('crm.newsletter_title');
        $nlPlaceholder = $newsletterSection->placeholder[$lang] ?? __('crm.newsletter_placeholder');
        $nlBtn = $newsletterSection->button_text[$lang] ?? __('crm.newsletter_btn');
    @endphp
    <div class="footer-subscribe">
        <div class="container">
            <div class="footer-subscribe-inner bg-cover wow animated fadeInUp" data-wow-duration="0.8s" style="background-image: url('{{ $nlBg }}');">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2 class="mb-lg-0 mb-3">{{ $nlTitle }}</h2>
                    </div>
                    <div class="col-lg-6 align-self-center text-lg-end">
                        <div class="subscribe-form-wrap">
                            <form id="landing-newsletter-form" action="{{ route('newsletter.subscribe') }}" method="POST"
                                class="subscribe-form-inner landing-ajax-form" data-success="{{ __('crm.newsletter_success') }}">
                                @csrf
                                <input type="email" name="email" placeholder="{{ $nlPlaceholder }}" maxlength="150">
                                <button type="submit" class="btn btn-black border-radius-0 landing-submit-btn">{{ $nlBtn }}</button>
                            </form>
                            <div class="landing-field-error newsletter-field-error" data-for="email"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="widget widget_about wow animated fadeInUp" data-wow-duration="0.8s">
                    <div class="thumb">
                        <img src="{{ $landingLogoFooter }}" alt="{{ $landingSiteName }}">
                    </div>
                    <div class="details">
                        <p>{{ __('crm.footer_about_desc') }}</p>
                        @if($landingPhone)
                        <p class="mt-3">
                            <i class="fa fa-phone-alt"></i>
                            <a href="tel:{{ $landingPhoneTel }}" class="footer-contact-link">{{ $landingPhone }}</a>
                        </p>
                        @endif
                        @if($layoutSettings?->email)
                        <p class="mt-2">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:{{ $layoutSettings->email }}" class="footer-contact-link">{{ $layoutSettings->email }}</a>
                        </p>
                        @endif
                        <ul class="social-media">
                            @if($layoutSettings?->facebook)<li><a href="{{ $layoutSettings->facebook }}" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a></li>@endif
                            @if($layoutSettings?->twitter)<li><a href="{{ $layoutSettings->twitter }}" target="_blank" rel="noopener"><i class="fab fa-twitter"></i></a></li>@endif
                            @if($layoutSettings?->instagram)<li><a href="{{ $layoutSettings->instagram }}" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a></li>@endif
                            @if($layoutSettings?->linkedin)<li><a href="{{ $layoutSettings->linkedin }}" target="_blank" rel="noopener"><i class="fab fa-linkedin-in"></i></a></li>@endif
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="widget widget_nav_menu wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.3s">
                    <h4 class="widget-title">{{ __('crm.footer_quick_links') }}</h4>
                    <ul>
                        <li><a href="{{ landing_section_url('hero') }}"><i class="fas fa-arrow-left"></i> {{ __('crm.nav_home') }}</a></li>
                        <li><a href="{{ landing_section_url('features') }}"><i class="fas fa-arrow-left"></i> {{ __('crm.nav_features') }}</a></li>
                        <li><a href="{{ landing_section_url('pricing') }}"><i class="fas fa-arrow-left"></i> {{ __('crm.nav_pricing') }}</a></li>
                        <li><a href="{{ landing_section_url('faq') }}"><i class="fas fa-arrow-left"></i> {{ __('crm.nav_faq') }}</a></li>
                        <li><a href="{{ landing_section_url('testimonials') }}"><i class="fas fa-arrow-left"></i> {{ __('crm.nav_testimonials') }}</a></li>
                        <li><a href="{{ landing_section_url('contact') }}"><i class="fas fa-arrow-left"></i> {{ __('crm.nav_contact') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="widget widget_nav_menu wow animated fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.6s">
                    <h4 class="widget-title">{{ __('crm.footer_account') }}</h4>
                    <ul>
                        <li><a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> {{ __('crm.footer_login_link') }}</a></li>
                        <li><a href="{{ route('register') }}"><i class="fas fa-arrow-left"></i> {{ __('crm.footer_register_link') }}</a></li>
                        <li><a href="{{ route('landing.terms') }}"><i class="fas fa-arrow-left"></i> {{ __('crm.footer_terms_link') }}</a></li>
                        <li><a href="{{ route('landing.privacy') }}"><i class="fas fa-arrow-left"></i> {{ __('crm.footer_privacy_link') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-6 align-self-center">
                    <p>{{ __('crm.footer_copyright', ['year' => date('Y')]) }}</p>
                </div>
                <div class="col-md-6 text-lg-end">
                    <a href="{{ route('landing.terms') }}">{{ __('crm.footer_terms_link') }}</a>
                    <a href="{{ route('landing.privacy') }}">{{ __('crm.footer_privacy_link') }}</a>
                    <a href="{{ landing_section_url('contact') }}">{{ __('crm.footer_contact_link') }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
