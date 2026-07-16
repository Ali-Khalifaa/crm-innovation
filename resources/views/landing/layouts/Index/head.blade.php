
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="alternate" hreflang="ar" href="{{ route('landing') }}">
    <link rel="alternate" hreflang="en" href="{{ route('lang.switch', 'en') }}">
    @if($pageKeywords)
    <meta name="keywords" content="{{ $pageKeywords }}">
    @endif
    <meta name="author" content="{{ $author }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="language" content="{{ $layoutLocale === 'ar' ? 'Arabic' : 'English' }}">
    <meta name="theme-color" content="{{ $themeColor }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ $ogLocale }}">
    <meta property="og:site_name" content="{{ $landingSiteName }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="{{ $ogTitle }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $twitterTitle }}">
    <meta name="twitter:description" content="{{ $twitterDescription }}">
    <meta name="twitter:image" content="{{ $twitterImage }}">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ $landingFavicon }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $landingFavicon }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $landingFavicon }}">
    <link rel="shortcut icon" href="{{ $landingFavicon }}" type="image/png">

    <script type="application/ld+json">
      {
         "@context": "https://schema.org",
         "@type": "SoftwareApplication",
         "name": "{{ $landingSiteName }}",
         "applicationCategory": "BusinessApplication",
         "operatingSystem": "Web",
         "inLanguage": "{{ $layoutLocale }}",
         "description": "{{ $pageDescription }}",
         "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD",
            "description": "14-day free trial"
         },
         "provider": {
            "@type": "Organization",
            "name": "{{ $landingSiteName }}",
            "logo": "{{ $landingLogo }}"
         }
      }
    </script>

    <link rel="stylesheet" href="{{ asset('landing/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/nice-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/magnific.min.css') }}/">
    <link rel="stylesheet" href="{{ asset('landing/css/owl.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/slick.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/responsive.css') }}">
    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('landing/css/crm-saas-ar.css') }}">
        <link rel="stylesheet" href="{{ asset('landing/css/rtl-style.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('landing/css/crm-saas-en.css') }}">
    @endif
</head>
