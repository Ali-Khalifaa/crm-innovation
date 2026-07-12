@extends('landing.layout')

@section('title', app()->getLocale() === 'ar'
    ? 'إنشاء حساب — CRM Innovation'
    : 'Register — CRM Innovation')
@section('body-class', '')

@section('content')

<div style="background:#F8FAFC;min-height:calc(100vh - 80px);padding:60px 0;">
    <div class="container">
        <div class="crm-auth-card wow animated fadeInUp" data-wow-duration="0.6s">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="CRM Innovation" style="max-height:50px;margin-bottom:16px;">
                <h3 style="font-weight:700;color:#1E293B;">{{ __('crm.register_title') }}</h3>
                <p class="text-muted">{{ __('crm.register_subtitle') }}</p>
            </div>

            <div id="alert-box" class="alert d-none mb-3" role="alert"></div>

            <form id="register-form">
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        {{ __('crm.register_company_label') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="company_name"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'اسم شركتك' : 'Your company name' }}"
                        required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        {{ __('crm.register_email_label') }} <span class="text-danger">*</span>
                    </label>
                    <input type="email" class="form-control" id="email"
                        placeholder="you@company.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        {{ __('crm.register_password_label') }} <span class="text-danger">*</span>
                    </label>
                    <input type="password" class="form-control" id="password"
                        placeholder="{{ __('crm.register_password_hint') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        {{ __('crm.register_confirm_label') }} <span class="text-danger">*</span>
                    </label>
                    <input type="password" class="form-control" id="password_confirmation"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'أعد كتابة كلمة المرور' : 'Repeat password' }}"
                        required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        {{ __('crm.register_plan_label') }} <span class="text-danger">*</span>
                    </label>
                    <select class="form-control" id="plan_id" required>
                        @foreach($plans as $plan)
                        <option value="{{ $plan->id }}"
                            {{ request('plan') == $plan->id
                                ? 'selected'
                                : ($plan->is_featured && !request('plan') ? 'selected' : '') }}>
                            {{ $plan->name }} — ${{ number_format($plan->monthly_price, 0) }}{{ __('crm.pricing_per_month') }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">{{ __('crm.register_billing_label') }}</label>
                    <div class="d-flex gap-3">
                        <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                            <input type="radio" name="billing_cycle" value="monthly" checked>
                            {{ __('crm.register_monthly') }}
                        </label>
                        <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                            <input type="radio" name="billing_cycle" value="yearly">
                            {{ __('crm.register_yearly') }}
                            <span class="badge bg-success ms-1">{{ __('crm.register_save_20') }}</span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-base" id="submit-btn">
                    <span id="btn-text">{{ __('crm.register_btn') }}</span>
                    <span id="btn-spinner" class="d-none spinner-border spinner-border-sm ms-2"></span>
                </button>
            </form>

            <hr class="my-4">
            <p class="text-center text-muted mb-0" style="font-size:14px;">
                {{ __('crm.register_have_account') }}
                <a href="{{ route('login') }}" style="color:var(--crm-primary);font-weight:600;">
                    {{ __('crm.register_sign_in_link') }}
                </a>
            </p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const _registerBtnText  = @json(__('crm.register_btn'));
const _registerCreating = @json(__('crm.register_creating'));
const _errNetwork       = @json(app()->getLocale() === 'ar' ? 'خطأ في الشبكة. تحقق من اتصالك.' : 'Network error. Check your connection.');

document.getElementById('register-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn      = document.getElementById('submit-btn');
    const btnText  = document.getElementById('btn-text');
    const spinner  = document.getElementById('btn-spinner');
    const alertBox = document.getElementById('alert-box');

    btn.disabled = true;
    btnText.textContent = _registerCreating;
    spinner.classList.remove('d-none');
    alertBox.classList.add('d-none');

    const payload = {
        company_name:          document.getElementById('company_name').value,
        email:                 document.getElementById('email').value,
        password:              document.getElementById('password').value,
        password_confirmation: document.getElementById('password_confirmation').value,
        plan_id:               parseInt(document.getElementById('plan_id').value),
        billing_cycle:         document.querySelector('input[name="billing_cycle"]:checked').value,
    };

    try {
        const res = await fetch('/api/crm/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });

        const text  = await res.text();
        const clean = text.replace(/^﻿+/, '');
        let data;
        try { data = JSON.parse(clean); }
        catch (e) { throw new Error('Server error (HTTP ' + res.status + ')'); }

        if (res.ok && data.success) {
            try { localStorage.setItem('crm_token',  data.data.token); }               catch (e) {}
            try { localStorage.setItem('crm_user',   JSON.stringify(data.data.user)); }   catch (e) {}
            try { localStorage.setItem('crm_tenant', JSON.stringify(data.data.tenant)); } catch (e) {}
            window.location.href = '/crm/dashboard';
        } else {
            const msg    = data.message || @json(app()->getLocale() === 'ar' ? 'فشل التسجيل. حاول مرة أخرى.' : 'Registration failed. Please try again.');
            const errors = data.errors ? Object.values(data.errors).flat().join(' ') : '';
            alertBox.className   = 'alert alert-danger mb-3';
            alertBox.textContent = errors || msg;
            alertBox.classList.remove('d-none');
        }
    } catch (err) {
        alertBox.className   = 'alert alert-danger mb-3';
        alertBox.textContent = err.message || _errNetwork;
        alertBox.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btnText.textContent = _registerBtnText;
        spinner.classList.add('d-none');
    }
});
</script>
@endsection
