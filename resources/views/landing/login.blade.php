@extends('landing.layout')

@section('title', app()->getLocale() === 'ar'
    ? 'تسجيل الدخول — CRM Innovation'
    : 'Sign In — CRM Innovation')
@section('body-class', '')

@section('content')

<div style="background:#F8FAFC;min-height:calc(100vh - 80px);padding:80px 0;">
    <div class="container">
        <div class="crm-auth-card wow animated fadeInUp" data-wow-duration="0.6s">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="CRM Innovation" style="max-height:50px;margin-bottom:16px;">
                <h3 style="font-weight:700;color:#1E293B;">{{ __('crm.login_title') }}</h3>
                <p class="text-muted">{{ __('crm.login_subtitle') }}</p>
            </div>

            <div id="alert-box" class="alert d-none mb-3" role="alert"></div>

            <form id="login-form">
                <div class="mb-3">
                    <label class="form-label fw-semibold">{{ __('crm.login_email_label') }}</label>
                    <input type="email" class="form-control" id="email"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'you@company.com' : 'you@company.com' }}"
                        required
                        value="{{ config('app.env') === 'local' ? 'demo@crm-innovation.com' : '' }}">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('crm.login_password_label') }}</label>
                    <input type="password" class="form-control" id="password"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Your password' }}"
                        required
                        value="{{ config('app.env') === 'local' ? 'Demo@123456' : '' }}">
                </div>
                <button type="submit" class="btn btn-base" id="submit-btn">
                    <span id="btn-text">{{ __('crm.login_btn') }}</span>
                    <span id="btn-spinner" class="d-none spinner-border spinner-border-sm ms-2"></span>
                </button>
            </form>

            <hr class="my-4">
            <p class="text-center text-muted mb-0" style="font-size:14px;">
                {{ __('crm.login_no_account') }}
                <a href="{{ route('register') }}" style="color:var(--crm-primary);font-weight:600;">
                    {{ __('crm.login_start_trial_link') }}
                </a>
            </p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const _loginBtnText   = @json(__('crm.login_btn'));
const _loginSigningIn = @json(__('crm.login_signing_in'));
const _errNetwork     = @json(app()->getLocale() === 'ar' ? 'خطأ في الشبكة. حاول مرة أخرى.' : 'Network error. Please try again.');

document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn      = document.getElementById('submit-btn');
    const btnText  = document.getElementById('btn-text');
    const spinner  = document.getElementById('btn-spinner');
    const alertBox = document.getElementById('alert-box');

    btn.disabled = true;
    btnText.textContent = _loginSigningIn;
    spinner.classList.remove('d-none');
    alertBox.classList.add('d-none');

    try {
        const res = await fetch('/api/crm/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                email:    document.getElementById('email').value,
                password: document.getElementById('password').value,
            }),
        });

        const text  = await res.text();
        const clean = text.replace(/^﻿+/, '');
        let data;
        try { data = JSON.parse(clean); }
        catch (e) { throw new Error('Server error (HTTP ' + res.status + ')'); }

        if (res.ok && data.success) {
            try { localStorage.setItem('crm_token',  data.data.access_token); }      catch (e) {}
            try { localStorage.setItem('crm_user',   JSON.stringify(data.data.user)); }   catch (e) {}
            try { localStorage.setItem('crm_tenant', JSON.stringify(data.data.tenant)); } catch (e) {}
            window.location.href = '/crm/dashboard';
        } else {
            alertBox.className = 'alert alert-danger mb-3';
            alertBox.textContent = data.message || @json(app()->getLocale() === 'ar' ? 'بريد إلكتروني أو كلمة مرور غير صحيحة.' : 'Invalid email or password.');
            alertBox.classList.remove('d-none');
        }
    } catch (err) {
        alertBox.className = 'alert alert-danger mb-3';
        alertBox.textContent = err.message || _errNetwork;
        alertBox.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btnText.textContent = _loginBtnText;
        spinner.classList.add('d-none');
    }
});
</script>
@endsection
