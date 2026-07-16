
<nav class="navbar navbar-area navbar-expand-lg">
    <div class="container nav-container navbar-bg">
        <div class="responsive-mobile-menu">
            <button class="menu toggle-btn d-block d-lg-none" data-target="#itech_main_menu"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="icon-left"></span>
                <span class="icon-right"></span>
            </button>
        </div>
        <div class="logo">
            <a href="{{ route('landing') }}"><img src="{{ $landingLogo }}" alt="{{ $landingSiteName }}"></a>
        </div>
        <div class="collapse navbar-collapse" id="itech_main_menu">
            <ul class="navbar-nav menu-open">
                <li><a href="{{ landing_section_url('hero') }}">{{ __('crm.nav_home') }}</a></li>
                <li><a href="{{ landing_section_url('features') }}">{{ __('crm.nav_features') }}</a></li>
                <li><a href="{{ landing_section_url('solutions') }}">{{ __('crm.nav_solutions') }}</a></li>
                <li><a href="{{ landing_section_url('pricing') }}">{{ __('crm.nav_pricing') }}</a></li>
                <li><a href="{{ landing_section_url('faq') }}">{{ __('crm.nav_faq') }}</a></li>
                <li><a href="{{ landing_section_url('contact') }}">{{ __('crm.nav_contact') }}</a></li>
                <li class="d-lg-none lang-switch-item">
                    <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="lang-switch-link" hreflang="en" lang="en" title="Switch language">
                        <i class="fas fa-globe"></i>
                        <span>{{ app()->getLocale() == 'ar' ? 'English' : 'العربية' }}</span>
                    </a>
                </li>
                <li class="d-lg-none nav-auth-item">
                    <a href="{{ route('login') }}" class="nav-auth-login"><i class="fas fa-user"></i> {{ __('crm.nav_sign_in') }}</a>
                </li>
            </ul>
        </div>
        <div class="nav-right-part nav-right-part-desktop align-self-center">
            <div class="nav-header-actions d-none d-lg-inline-flex">
                <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="lang-switch-link" hreflang="en" lang="en" title="Switch language">
                    <i class="fas fa-globe"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'English' : 'العربية' }}</span>
                </a>
                <a href="{{ route('login') }}" class="nav-auth-login"><i class="fas fa-user"></i> {{ __('crm.nav_sign_in') }}</a>
            </div>
            @if($landingPhone)
            <a class="navbar-phone" href="tel:{{ $landingPhoneTel }}">
                  <span class="icon">
                  <img src="{{ asset('landing/img/icon/1.png') }}" alt="img">
                  </span>
                <span>{{ __('crm.topbar_contact') }}</span>
                <h5>{{ $landingPhone }}</h5>
            </a>
            @endif
        </div>
    </div>
</nav>
