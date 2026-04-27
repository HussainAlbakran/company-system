<!DOCTYPE html>
@php($authRtl = in_array(app()->getLocale(), config('locales.rtl', ['ar', 'ur']), true))
<html lang="{{ app()->getLocale() }}" dir="{{ $authRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.login') }} {{ __('auth.login_title_suffix') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 {{ $authRtl ? 'font-arabic' : 'font-sans' }}">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6 lg:px-8">
        <div class="absolute inset-0 bg-slate-950"></div>
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-35"
             style="background-image: url('{{ asset('images/public/hero.png') }}')"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-[#020617]/80 via-[#0a1628]/70 to-[#050a14]/80"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_80%50%_at_50%-20%,rgba(59,130,246,0.22),transparent)]"></div>

        <div class="absolute top-4 z-20 flex flex-wrap gap-2">
            @foreach(config('locales.supported', ['ar', 'en', 'ur']) as $loc)
                <a href="{{ route('locale.switch', $loc) }}" class="rounded-md border border-white/20 bg-white/10 px-3 py-1 text-xs font-bold text-white">{{ config('locales.labels.'.$loc, $loc) }}</a>
            @endforeach
        </div>

        <div class="relative z-10 w-full max-w-sm">
            <div class="rounded-2xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl sm:p-7">
                <div class="mb-8 flex justify-center">
                    <img src="{{ asset('images/public/logo.png') }}" class="h-10 w-auto object-contain" alt="{{ __('auth.company_logo_alt') }}" />
                </div>

                <h1 class="mb-5 text-center text-lg font-bold text-white">{{ __('auth.login') }}</h1>

                @if (session('status'))
                    <div class="mb-4 rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm text-slate-300">{{ __('auth.email') }}</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white placeholder-slate-500 focus:border-blue-500 focus:outline-none focus:ring-0"
                            placeholder="{{ __('auth.email_example_placeholder') }}"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm text-slate-300">{{ __('auth.password') }}</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white placeholder-slate-500 focus:border-blue-500 focus:outline-none focus:ring-0"
                            placeholder="{{ __('auth.password_dots_placeholder') }}"
                        >
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label for="remember" class="inline-flex items-center gap-2 text-slate-400">
                            <input id="remember" type="checkbox" name="remember" class="rounded border-white/20 bg-white/5 text-blue-500 focus:ring-0">
                            <span>{{ __('auth.remember_me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-blue-400 hover:text-blue-300">
                                {{ __('auth.forgot_password') }}
                            </a>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-gradient-to-l from-blue-600 to-blue-500 py-2 font-bold text-white transition hover:from-blue-500 hover:to-blue-400"
                    >
                        {{ __('auth.login') }}
                    </button>
                </form>

                <div class="mt-5 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-slate-300 hover:text-white">
                        {{ __('auth.back_home') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
