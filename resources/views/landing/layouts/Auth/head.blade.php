<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @if (Route::is('register'))
        <title>{{ __('crm.auth_meta_title_register') }}</title>
        <meta name="description" content="{{ __('crm.auth_meta_desc_register') }}">
    @else
        <title>{{ __('crm.auth_meta_title_login') }}</title>
        <meta name="description" content="{{ __('crm.auth_meta_desc_login') }}">
    @endif
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ $landingFavicon }}">
    <link rel="shortcut icon" href="{{ $landingFavicon }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('landing/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/responsive.css') }}">
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('landing/css/rtl-style.css') }}">
        <link rel="stylesheet" href="{{ asset('landing/css/auth-ar.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('landing/css/auth-en.css') }}">
    @endif
 </head>
