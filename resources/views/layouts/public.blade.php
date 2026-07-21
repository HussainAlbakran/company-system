<!DOCTYPE html>

@php
    $publicRtl = in_array(
        app()->getLocale(),
        config('locales.rtl', ['ar', 'ur']),
        true
    );

    $siteName = 'شركة التقدم للخرسانة الجاهزة';
    $siteNameEnglish = 'ADVANCE PRECAST COMPANY';

    $defaultTitle = $siteName . ' | ' . $siteNameEnglish;

    $defaultDescription = 'شركة التقدم للخرسانة الجاهزة متخصصة في الخرسانة الجاهزة والمنتجات الخرسانية مسبقة الصب، وتقدم حلولًا موثوقة للمشاريع الإنشائية في المملكة العربية السعودية.';

    $currentUrl = url()->current();

    /*
    |--------------------------------------------------------------------------
    | مسار شعار الشركة
    |--------------------------------------------------------------------------
    | يجب أن يكون ملف الشعار موجودًا داخل:
    | public/logo.png
    |
    | ويصبح رابطه:
    | https://altaqaddumapc.com/logo.png
    */
    $logoUrl = asset('logo.png');

    $schemaData = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        '@id' => url('/') . '#organization',
        'name' => $siteName,
        'alternateName' => [
            $siteNameEnglish,
            'Advance Precast Company',
            'Altaqaddum APC',
            'APC',
        ],
        'url' => url('/'),
        'logo' => $logoUrl,
        'image' => $logoUrl,
        'description' => $defaultDescription,
        'email' => 'mailto:altaqaddum.system@gmail.com',
        'telephone' => '+966590548089',
        'areaServed' => [
            '@type' => 'Country',
            'name' => 'Saudi Arabia',
        ],
    ];

    $websiteSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => url('/') . '#website',
        'url' => url('/'),
        'name' => $siteName,
        'alternateName' => $siteNameEnglish,
        'publisher' => [
            '@id' => url('/') . '#organization',
        ],
        'inLanguage' => app()->getLocale(),
    ];
@endphp

<html
    lang="{{ app()->getLocale() }}"
    dir="{{ $publicRtl ? 'rtl' : 'ltr' }}"
    class="scroll-smooth"
>
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    {{-- عنوان الصفحة --}}
    <title>
        @yield('title', $defaultTitle)
    </title>

    {{-- وصف الصفحة لمحركات البحث --}}
    <meta
        name="description"
        content="@yield('meta_description', $defaultDescription)"
    >

    {{-- كلمات مرتبطة بنشاط الشركة --}}
    <meta
        name="keywords"
        content="شركة التقدم للخرسانة الجاهزة, التقدم للخرسانة الجاهزة, شركة خرسانة جاهزة, خرسانة جاهزة, خرسانة مسبقة الصب, منتجات خرسانية, مصنع خرسانة, بريكاست, مشاريع إنشائية, Advance Precast Company, Altaqaddum APC, APC, السعودية"
    >

    <meta
        name="author"
        content="{{ $siteName }}"
    >

    <meta
        name="robots"
        content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"
    >

    <meta
        name="googlebot"
        content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1"
    >

    {{-- الرابط الرسمي للصفحة --}}
    <link
        rel="canonical"
        href="@yield('canonical', $currentUrl)"
    >

    {{-- لغة الموقع --}}
    <link
        rel="alternate"
        hreflang="ar"
        href="{{ url('/') }}"
    >

    <link
        rel="alternate"
        hreflang="x-default"
        href="{{ url('/') }}"
    >

    {{-- Open Graph: واتساب وفيسبوك ولينكدإن --}}
    <meta
        property="og:type"
        content="website"
    >

    <meta
        property="og:site_name"
        content="{{ $siteName }}"
    >

    <meta
        property="og:title"
        content="@yield('og_title', $defaultTitle)"
    >

    <meta
        property="og:description"
        content="@yield('og_description', $defaultDescription)"
    >

    <meta
        property="og:url"
        content="@yield('canonical', $currentUrl)"
    >

    <meta
        property="og:image"
        content="@yield('og_image', $logoUrl)"
    >

    <meta
        property="og:image:secure_url"
        content="@yield('og_image', $logoUrl)"
    >

    <meta
        property="og:image:alt"
        content="شعار شركة التقدم للخرسانة الجاهزة"
    >

    <meta
        property="og:locale"
        content="{{ app()->getLocale() === 'ar' ? 'ar_SA' : 'en_US' }}"
    >

    {{-- Twitter / X --}}
    <meta
        name="twitter:card"
        content="summary_large_image"
    >

    <meta
        name="twitter:title"
        content="@yield('twitter_title', $defaultTitle)"
    >

    <meta
        name="twitter:description"
        content="@yield('twitter_description', $defaultDescription)"
    >

    <meta
        name="twitter:image"
        content="@yield('twitter_image', $logoUrl)"
    >

    <meta
        name="twitter:image:alt"
        content="شعار شركة التقدم للخرسانة الجاهزة"
    >

    {{-- هوية المتصفح --}}
    <meta
        name="theme-color"
        content="#050a14"
    >

    <link
        rel="icon"
        type="image/png"
        href="{{ $logoUrl }}"
    >

    <link
        rel="apple-touch-icon"
        href="{{ $logoUrl }}"
    >

    {{-- بيانات الشركة المنظمة لمحركات البحث --}}
    <script type="application/ld+json">
        {!! json_encode(
            $schemaData,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_PRETTY_PRINT
        ) !!}
    </script>

    {{-- بيانات الموقع المنظمة --}}
    <script type="application/ld+json">
        {!! json_encode(
            $websiteSchema,
            JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES |
            JSON_PRETTY_PRINT
        ) !!}
    </script>

    {{-- الخطوط --}}
    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
    >

    <link
        href="https://fonts.bunny.net/css?family=cairo:400,500,600,700,800&family=figtree:500,600,700&display=swap"
        rel="stylesheet"
    >

    {{-- ملفات CSS وJavaScript --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- إضافات خاصة بكل صفحة --}}
    @stack('head')
</head>

<body
    class="min-h-screen bg-[#050a14]
    {{ $publicRtl ? 'font-arabic' : 'font-sans' }}
    text-slate-100 antialiased
    selection:bg-blue-500/40
    selection:text-white"
>
    @yield('content')

    @stack('scripts')
</body>
</html>