
<footer class="footer-area bg-black bg-cover">
    <div class="container">
       <div class="footer-bottom py-4">
          <div class="row">
             <div class="col-md-6 align-self-center">
                <p class="mb-0">{{ __('crm.footer_copyright', ['year' => date('Y')]) }}</p>
             </div>
             <div class="col-md-6 text-lg-end">
                <nav class="footer-auth-nav" aria-label="{{ __('crm.auth_quick_links_aria') }}">
                   <a href="{{ route('landing') }}"><i class="fas fa-home"></i><span>{{ __('crm.nav_home') }}</span></a>
                   @if (Route::is('register'))
                   <a href="{{ route('login') }}"><i class="fas fa-user"></i><span>{{ __('crm.nav_sign_in') }}</span></a>
                   @endif
                   @if (Route::is('login'))
                   <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i><span>{{ __('crm.footer_register_link') }}</span></a>
                   @endif
                   <a href="{{ route('landing') }}#contact"><i class="fas fa-envelope"></i><span>{{ __('crm.nav_contact') }}</span></a>
                </nav>
             </div>
          </div>
       </div>
    </div>
 </footer>
