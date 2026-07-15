<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

@include('landing.layouts.Index.head')

<body>
<!-- preloader area start -->
<div class="preloader" id="preloader">
    <div class="preloader-inner">
        <div class="spinner">
            <div class="dot1"></div>
            <div class="dot2"></div>
        </div>
    </div>
</div>
<!-- preloader area end -->

<div class="body-overlay" id="body-overlay"></div>

<!-- navbar start -->
@include('landing.layouts.Index.nav')
<!-- navbar end -->

@yield('content')


<!-- Footer Area Start -->
@include('landing.layouts.Index.footer')
<!-- Footer Area End -->

<!-- back to top area start -->
<div class="back-to-top">
    <span class="back-top"><i class="fa fa-angle-up"></i></span>
</div>
<!-- back to top area end -->

@if(session('contact_success'))
<div class="landing-toast success" id="landing-toast" role="alert">{{ __('crm.contact_success') }}</div>
@elseif(session('newsletter_success'))
<div class="landing-toast success" id="landing-toast" role="alert">{{ __('crm.newsletter_success') }}</div>
@elseif(session('newsletter_error'))
<div class="landing-toast error" id="landing-toast" role="alert">{{ session('newsletter_error') }}</div>
@endif

@include('landing.layouts.Index.script')

</body>
</html>
