@extends('landing.layouts.Auth.master')

@section('content')
@php
    $defaultPlan = $defaultPlan ?? ($plans->firstWhere('is_featured', true) ?? $plans->first());
@endphp
<section class="auth-area">
    <div class="container">
       <div class="auth-wrap wow animated fadeInUp" data-wow-duration="0.8s">
          <div class="auth-brand">
             <a href="{{ route('landing') }}" class="auth-brand-logo">
                <img src="{{ $landingLogo }}" alt="{{ $landingSiteName }}">
             </a>
             <span class="auth-trial-badge"><i class="fa fa-gift me-2"></i>{{ __('crm.register_trial_badge') }}</span>
             <h3>{{ __('crm.register_brand_title') }}</h3>
             <p>{{ __('crm.register_brand_desc') }}</p>
             <ul class="auth-benefits">
                <li><i class="fas fa-check"></i> {{ __('crm.register_benefit_fast') }}</li>
                <li><i class="fas fa-check"></i> {{ __('crm.register_benefit_arabic') }}</li>
                <li><i class="fas fa-check"></i> {{ __('crm.register_benefit_cancel') }}</li>
             </ul>
          </div>
          <div class="auth-form-side">
             <h2>{{ __('crm.register_title') }}</h2>
             <p class="auth-subtitle">{{ __('crm.register_form_subtitle') }}</p>
             <form id="landing-register-form"
                action="{{ url('/api/crm/register') }}"
                method="post"
                class="auth-ajax-form"
                data-auth-type="register"
                data-redirect="{{ url('/crm/dashboard') }}"
                data-loading="{{ __('crm.register_creating') }}"
                data-network-error="{{ __('crm.register_network_error') }}">
                <div class="single-input-inner style-border">
                   <input type="text" name="name" placeholder="{{ __('crm.register_name_label') }}" required maxlength="150">
                </div>
                <div class="auth-field-error" data-for="name"></div>
                <div class="single-input-inner style-border">
                   <input type="text" name="company_name" placeholder="{{ __('crm.register_company_label') }}" required maxlength="150">
                </div>
                <div class="auth-field-error" data-for="company_name"></div>
                <div class="single-input-inner style-border">
                   <input type="email" name="email" placeholder="{{ __('crm.register_email_label') }}" required autocomplete="email" maxlength="150">
                </div>
                <div class="auth-field-error" data-for="email"></div>
                <div class="single-input-inner style-border">
                   <input type="tel" name="phone" placeholder="{{ __('crm.register_phone_label') }}" maxlength="30">
                </div>
                <div class="auth-field-error" data-for="phone"></div>
                <label class="auth-field-label">{{ __('crm.register_plan_label') }}</label>
                <div class="auth-plan-options">
                   @foreach($plans as $plan)
                   <label class="auth-plan-option">
                      <input type="radio" name="plan_id" value="{{ $plan->id }}" @checked($defaultPlan && $defaultPlan->id === $plan->id) required>
                      <span class="auth-plan-card">
                         <strong>{{ $plan->name }}</strong>
                         <small>${{ number_format($plan->monthly_price, 0) }}/{{ __('crm.register_monthly') }}</small>
                      </span>
                   </label>
                   @endforeach
                </div>
                <div class="auth-field-error" data-for="plan_id"></div>
                <label class="auth-field-label">{{ __('crm.register_billing_label') }}</label>
                <div class="auth-billing-options">
                   <label class="auth-billing-option">
                      <input type="radio" name="billing_cycle" value="monthly" checked>
                      <span>{{ __('crm.register_monthly') }}</span>
                   </label>
                   <label class="auth-billing-option">
                      <input type="radio" name="billing_cycle" value="yearly">
                      <span>{{ __('crm.register_yearly') }}</span>
                   </label>
                </div>
                <div class="auth-field-error" data-for="billing_cycle"></div>
                <div class="single-input-inner style-border password-field">
                   <button type="button" class="password-toggle" aria-label="{{ __('crm.login_show_password') }}">
                      <i class="far fa-eye"></i>
                   </button>
                   <input type="password" name="password" placeholder="{{ __('crm.register_password_label') }}" required autocomplete="new-password" minlength="8">
                </div>
                <div class="auth-field-error" data-for="password"></div>
                <div class="single-input-inner style-border password-field">
                   <button type="button" class="password-toggle" aria-label="{{ __('crm.register_show_password_confirm') }}">
                      <i class="far fa-eye"></i>
                   </button>
                   <input type="password" name="password_confirmation" placeholder="{{ __('crm.register_confirm_label') }}" required autocomplete="new-password" minlength="8">
                </div>
                <div class="auth-field-error" data-for="password_confirmation"></div>
                <label class="auth-terms">
                   <input type="checkbox" name="terms" required>
                   <span>{{ __('crm.register_terms_prefix') }} <a href="{{ route('landing.terms') }}">{{ __('crm.footer_terms_link') }}</a> {{ __('crm.register_terms_and') }} <a href="{{ route('landing.privacy') }}">{{ __('crm.footer_privacy_link') }}</a></span>
                </label>
                <button type="submit" class="btn btn-base w-100 border-radius-5 auth-submit-btn">{{ __('crm.register_btn') }}</button>
             </form>
             <p class="auth-switch">{{ __('crm.register_have_account') }} <a href="{{ route('login') }}">{{ __('crm.register_sign_in_link') }}</a></p>
          </div>
       </div>
    </div>
 </section>
@endsection
