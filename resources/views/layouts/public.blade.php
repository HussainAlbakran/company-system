<!DOCTYPE html>
@php($publicRtl = in_array(app()->getLocale(), config('locales.rtl', ['ar', 'ur']), true))
<html lang="{{ app()->getLocale() }}" dir="{{ $publicRtl ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ __('شركة التقدم للخرسانة الجاهزة — ADVANCE PRECAST COMPANY — خرسانة جاهزة ومنتجات خرسانية مسبقة الصب.') }}">
    <title>@yield('title', __('شركة التقدم للخرسانة الجاهزة | ADVANCE PRECAST COMPANY'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cairo:400,500,600;700;800&family=figtree:500,600;700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen bg-[#050a14] {{ $publicRtl ? 'font-arabic' : 'font-sans' }} text-slate-100 antialiased selection:bg-blue-500/40 selection:text-white">
    @yield('content')
    @stack('scripts')
</body>
</html>
