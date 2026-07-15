<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    @include('landing.layouts.Auth.head')
   <body>
      <div class="body-overlay" id="body-overlay"></div>

      @include('landing.layouts.Auth.nav')

      @yield('content')

      @include('landing.layouts.Auth.footer')

      <div class="back-to-top">
         <span class="back-top"><i class="fa fa-angle-up"></i></span>
      </div>

      @include('landing.layouts.Auth.script')

   </body>
</html>
