@php
    $topbarSettings = isset($settings) ? $settings : \App\Models\LandingSetting::first();
    $topbarLocale   = app()->getLocale();
    $topbarIsAr     = $topbarLocale === 'ar';
    $topbarSiteName = $topbarSettings
        ? ($topbarIsAr ? $topbarSettings->site_name_ar : $topbarSettings->site_name_en)
        : 'CRM Innovation';
@endphp
<!DOCTYPE html>
<html lang="{{ $topbarLocale }}" dir="{{ $topbarIsAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', $topbarSiteName)</title>
    <meta name="description" content="@yield('meta_description', '')">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('landing/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/magnific.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/owl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/responsive.css') }}">
    @if(app()->getLocale() === 'ar')
    <link rel="stylesheet" href="{{ asset('landing/css/rtl-style.css') }}">
    @endif

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ── Override template's main color with CRM brand ── */
        :root {
            --main-color:       #2563EB;   /* overrides template #246BFD */
            --crm-primary:      #2563EB;
            --crm-primary-dark: #1D4ED8;
            --crm-accent:       #7C3AED;
            --crm-success:      #10B981;
        }
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Plus Jakarta Sans'" }}, sans-serif;
        }

        /* ── Topbar ── */
        .crm-topbar {
            background: var(--main-color);
            color: #fff;
            font-size: 13px;
            padding: 8px 0;
        }
        .crm-topbar a { color: #fff; opacity: 0.88; text-decoration: none; }
        .crm-topbar a:hover { opacity: 1; }
        .crm-topbar .lang-switcher a {
            display: inline-flex;
            align-items: center;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.35);
            transition: background 0.2s;
        }
        .crm-topbar .lang-switcher a.active-lang {
            background: rgba(255,255,255,0.22);
            border-color: rgba(255,255,255,0.7);
        }
        .crm-topbar .lang-switcher a:hover { background: rgba(255,255,255,0.15); }

        /* ── Feature cards (features section) ── */
        .feature-card {
            padding: 28px;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            height: 100%;
            background: #fff;
            transition: box-shadow 0.25s, border-color 0.25s;
        }
        .feature-card:hover {
            box-shadow: 0 8px 32px rgba(37,99,235,0.10);
            border-color: var(--main-color);
        }
        .feature-card .feature-icon {
            width: 54px;
            height: 54px;
            background: rgba(37,99,235,0.08);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            flex-shrink: 0;
        }
        .feature-card .feature-icon i { color: var(--main-color); font-size: 22px; }
        .feature-card h5 { font-weight: 700; margin-bottom: 8px; color: #150E3D; }
        .feature-card p  { color: #64748B; margin: 0; font-size: 14.5px; line-height: 1.65; }

        /* ── Pricing badge ── */
        .pricing-badge {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--crm-accent);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 20px;
            white-space: nowrap;
        }
        .single-pricing-inner { position: relative; }

        /* ── Auth cards ── */
        .crm-auth-card {
            max-width: 480px;
            margin: 80px auto;
            background: #fff;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 32px rgba(37,99,235,0.10);
        }
        .crm-auth-card .form-control {
            border-radius: 8px;
            padding: 12px 16px;
            border: 1.5px solid #E2E8F0;
        }
        .crm-auth-card .form-control:focus {
            border-color: var(--main-color);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.10);
        }
        .crm-auth-card .btn-base {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
        }

        /* ── Testimonials ── */
        .single-testimonial-inner.style-1 {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            border: 1px solid #E2E8F0;
            margin: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }

        /* ── Team cards ── */
        .single-team-inner.style-1 {
            padding: 32px 24px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid #E2E8F0;
            transition: box-shadow 0.25s, transform 0.25s;
        }
        .single-team-inner.style-1:hover {
            box-shadow: 0 12px 40px rgba(37,99,235,0.12);
            transform: translateY(-4px);
        }
        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #F1F5F9;
            color: var(--crm-text);
            font-size: 13px;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }
        .social-link:hover {
            background: var(--main-color);
            color: #fff;
        }

        /* ── Blog ── */
        .single-blog-list { transition: transform 0.25s, box-shadow 0.25s; }
        .single-blog-list:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 48px rgba(0,0,0,0.10) !important;
        }

        /* ── Contact form ── */
        .contact-form-inner .form-control:focus {
            border-color: var(--main-color) !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.10);
            outline: none;
        }

        /* ── RTL layout fixes ── */
        [dir="rtl"] .ps-lg-5 { padding-left: 0 !important; padding-right: 3rem !important; }
        [dir="rtl"] .pe-xl-5 { padding-right: 0 !important; padding-left: 3rem !important; }
        [dir="rtl"] .ms-2, [dir="rtl"] .ms-3 { margin-left: 0 !important; margin-right: 0.5rem !important; }
        [dir="rtl"] .me-2, [dir="rtl"] .me-3 { margin-right: 0 !important; margin-left: 0.5rem !important; }
        [dir="rtl"] .fa-arrow-right::before { content: "\f060"; }
    </style>
    @yield('head')
</head>
<body class="@yield('body-class', 'home-14')">

    <!-- preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-inner">
            <div class="spinner"><div class="dot1"></div><div class="dot2"></div></div>
        </div>
    </div>

    <div class="body-overlay" id="body-overlay"></div>

    <!-- Topbar -->
    <div class="crm-topbar d-none d-lg-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    @if($topbarSettings && $topbarSettings->phone)
                        <a href="tel:{{ $topbarSettings->phone }}" class="me-3">
                            <i class="fas fa-phone me-1"></i> {{ $topbarSettings->phone }}
                        </a>
                    @endif
                    @if($topbarSettings && $topbarSettings->email)
                        <a href="mailto:{{ $topbarSettings->email }}">
                            <i class="fas fa-envelope me-1"></i> {{ $topbarSettings->email }}
                        </a>
                    @endif
                </div>
                <div class="col-lg-6">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <div class="lang-switcher d-flex gap-1">
                            <a href="{{ route('lang.switch', 'en') }}"
                               class="{{ $topbarLocale === 'en' ? 'active-lang' : '' }}">EN</a>
                            <a href="{{ route('lang.switch', 'ar') }}"
                               class="{{ $topbarLocale === 'ar' ? 'active-lang' : '' }}">ع</a>
                        </div>
                        <div class="d-flex gap-2 ms-3">
                            @if($topbarSettings && $topbarSettings->facebook)
                                <a href="{{ $topbarSettings->facebook }}" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            @endif
                            @if($topbarSettings && $topbarSettings->twitter)
                                <a href="{{ $topbarSettings->twitter }}" target="_blank"><i class="fab fa-twitter"></i></a>
                            @endif
                            @if($topbarSettings && $topbarSettings->linkedin)
                                <a href="{{ $topbarSettings->linkedin }}" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                            @endif
                            @if($topbarSettings && $topbarSettings->instagram)
                                <a href="{{ $topbarSettings->instagram }}" target="_blank"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if($topbarSettings && $topbarSettings->youtube)
                                <a href="{{ $topbarSettings->youtube }}" target="_blank"><i class="fab fa-youtube"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('landing.partials._navbar')

    @yield('content')

    @include('landing.partials._footer')

    <!-- Back to top -->
    <div class="back-to-top"><span class="back-top"><i class="fa fa-angle-up"></i></span></div>

    <!-- Scripts -->
    <script src="{{ asset('landing/js/jquery.min.js') }}"></script>
    <script src="{{ asset('landing/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('landing/js/fontawesome.min.js') }}"></script>
    <script src="{{ asset('landing/js/magnific.min.js') }}"></script>
    <script src="{{ asset('landing/js/owl.min.js') }}"></script>
    <script src="{{ asset('landing/js/wow.min.js') }}"></script>
    <script src="{{ asset('landing/js/counter-up.min.js') }}"></script>
    <script src="{{ asset('landing/js/waypoint.min.js') }}"></script>
    <script src="{{ asset('landing/js/main.js') }}"></script>
    @yield('scripts')
</body>
</html>
