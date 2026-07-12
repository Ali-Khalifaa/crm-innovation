<nav class="navbar navbar-area navbar-area-2 navbar-expand-lg bg-white">
    <div class="container nav-container custom-container">
        <div class="responsive-mobile-menu">
            <button class="menu toggle-btn d-block d-lg-none" data-target="#crm_main_menu"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="icon-left"></span>
                <span class="icon-right"></span>
            </button>
        </div>
        <div class="logo">
            <a href="{{ route('landing') }}">
                <img src="{{ asset('images/logo.png') }}" alt="CRM Innovation" style="max-height:45px;">
            </a>
        </div>
        <div class="collapse navbar-collapse" id="crm_main_menu">
            <ul class="navbar-nav menu-open text-lg-center ps-lg-5">
                <li><a href="{{ route('landing') }}">{{ __('crm.nav_home') }}</a></li>
                <li><a href="{{ route('landing') }}#features">{{ __('crm.nav_features') }}</a></li>
                <li><a href="{{ route('pricing') }}">{{ __('crm.nav_pricing') }}</a></li>
                <li><a href="{{ route('landing') }}#contact">{{ __('crm.nav_contact') }}</a></li>
            </ul>
        </div>
        <div class="nav-right-part nav-right-part-desktop d-lg-inline-flex align-items-center gap-3">
            {{-- Mobile lang switch --}}
            <div class="d-lg-none d-flex gap-1 me-2">
                <a href="{{ route('lang.switch', 'en') }}"
                   class="btn btn-sm {{ app()->getLocale() === 'en' ? 'btn-base' : 'btn-border-base' }}"
                   style="padding:4px 10px;font-size:12px;">EN</a>
                <a href="{{ route('lang.switch', 'ar') }}"
                   class="btn btn-sm {{ app()->getLocale() === 'ar' ? 'btn-base' : 'btn-border-base' }}"
                   style="padding:4px 10px;font-size:12px;">ع</a>
            </div>
            <a href="{{ route('login') }}" class="btn btn-border-base">{{ __('crm.nav_sign_in') }}</a>
            <a href="{{ route('register') }}" class="btn btn-base">
                {{ __('crm.nav_start_trial') }}
                <i class="fa fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</nav>
