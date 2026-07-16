
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
          <nav class="header-auth-nav" aria-label="{{ __('crm.auth_quick_links_aria') }}">
             <a href="{{ route('landing') }}" class="header-auth-link"><i class="fas fa-home"></i><span>{{ __('crm.nav_home') }}</span></a>
             @if (Route::is('register'))
             <a href="{{ route('login') }}" class="header-auth-link"><i class="fas fa-user"></i><span>{{ __('crm.nav_sign_in') }}</span></a>
             @endif
             @if (Route::is('login'))
                <a href="{{ route('register') }}" class="header-auth-link"><i class="fas fa-user-plus"></i><span>{{ __('crm.footer_register_link') }}</span></a>
             @endif
             <a href="{{ landing_section_url('contact') }}" class="header-auth-link"><i class="fas fa-envelope"></i><span>{{ __('crm.nav_contact') }}</span></a>
             <a href="{{ route('lang.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" class="lang-switch-link" hreflang="{{ app()->getLocale() == 'ar' ? 'en' : 'ar' }}" lang="{{ app()->getLocale() == 'ar' ? 'en' : 'ar' }}" title="{{ __('crm.auth_switch_lang_title') }}">
                <i class="fas fa-globe"></i>
                <span>{{ app()->getLocale() == 'ar' ? __('crm.lang_en') : __('crm.lang_ar') }}</span>
             </a>
          </nav>
       </div>
       @if($landingPhone)
       <div class="nav-right-part nav-right-part-desktop align-self-center">
          <a class="navbar-phone" href="tel:{{ $landingPhoneTel }}">
             <span class="icon"><img src="{{ asset('landing/img/icon/1.png') }}" alt=""></span>
             <span>{{ __('crm.topbar_contact') }}</span>
             <h5>{{ $landingPhone }}</h5>
          </a>
       </div>
       @endif
    </div>
 </nav>
