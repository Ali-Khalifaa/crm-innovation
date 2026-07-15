@extends('landing.layouts.Auth.master')

@section('content')
<section class="auth-area">
    <div class="container">
       <div class="auth-wrap wow animated fadeInUp" data-wow-duration="0.8s">
          <div class="auth-brand">
             <a href="{{ route('landing') }}" class="auth-brand-logo">
                <img src="{{ $landingLogo }}" alt="{{ $landingSiteName }}">
             </a>
             <h3>{{ __('crm.login_title') }}</h3>
             <p>{{ __('crm.login_brand_desc') }}</p>
             <ul class="auth-benefits">
                <li><i class="fas fa-check"></i> {{ __('crm.login_benefit_dashboard') }}</li>
                <li><i class="fas fa-check"></i> {{ __('crm.login_benefit_reports') }}</li>
                <li><i class="fas fa-check"></i> {{ __('crm.login_benefit_support') }}</li>
             </ul>
          </div>
          <div class="auth-form-side">
             <h2>{{ __('crm.login_btn') }}</h2>
             <p class="auth-subtitle">{{ __('crm.login_subtitle') }}</p>
             <form id="landing-login-form"
                action="{{ url('/api/crm/login') }}"
                method="post"
                class="auth-ajax-form"
                data-auth-type="login"
                data-redirect="{{ url('/crm/dashboard') }}"
                data-loading="{{ __('crm.login_signing_in') }}"
                data-network-error="{{ __('crm.login_network_error') }}">
                <div class="single-input-inner style-border">
                   <input type="email" name="email" placeholder="{{ __('crm.login_email_label') }}" required autocomplete="email">
                </div>
                <div class="auth-field-error" data-for="email"></div>
                <div class="single-input-inner style-border password-field">
                   <button type="button" class="password-toggle" aria-label="{{ __('crm.login_show_password') }}">
                      <i class="far fa-eye"></i>
                   </button>
                   <input type="password" name="password" placeholder="{{ __('crm.login_password_label') }}" required autocomplete="current-password">
                </div>
                <div class="auth-field-error" data-for="password"></div>
                <div class="auth-meta">
                   <label>
                      <input type="checkbox" name="remember">
                      {{ __('crm.login_remember_me') }}
                   </label>
                   <a href="#">{{ __('crm.login_forgot_password') }}</a>
                </div>
                <button type="submit" class="btn btn-base w-100 border-radius-5 auth-submit-btn">{{ __('crm.login_btn') }}</button>
             </form>
             <p class="auth-switch">{{ __('crm.login_no_account') }} <a href="{{ route('register') }}">{{ __('crm.login_start_trial_link') }}</a></p>
          </div>
       </div>
    </div>
 </section>
@endsection
