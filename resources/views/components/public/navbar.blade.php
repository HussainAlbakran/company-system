@props([
    'logoUrl' => null,
])

<header
    class="sticky top-0 z-50 border-b border-white/10 bg-[#050a14]/85 backdrop-blur-xl"
    x-data="{ mobileOpen: false }"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="group flex min-w-0 flex-1 items-center gap-3 no-underline">
            <span class="relative flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-white/10 bg-gradient-to-br from-blue-600/25 to-slate-900 ring-1 ring-white/5">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ __('شعار شركة التقدم للخرسانة الجاهزة') }}" class="h-full w-full object-contain p-1.5" />
                @else
                    <span class="text-xs font-extrabold tracking-tight text-blue-200">APC</span>
                @endif
            </span>
            <span class="min-w-0 text-start">
                <span class="block truncate text-sm font-extrabold text-white sm:text-base">{{ __('شركة التقدم للخرسانة الجاهزة') }}</span>
                <span class="mt-0.5 block truncate font-['Figtree',sans-serif] text-[10px] font-semibold uppercase tracking-[0.2em] text-blue-200/90 sm:text-[11px]">ADVANCE PRECAST COMPANY</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 md:flex" aria-label="{{ __('التنقل الرئيسي') }}">
            <a class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5 hover:text-white" href="{{ url('/') }}#top">{{ __('الرئيسية') }}</a>
            <a class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5 hover:text-white" href="{{ url('/') }}#services">{{ __('خدماتنا') }}</a>
            <a class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5 hover:text-white" href="{{ url('/') }}#projects">{{ __('مشاريعنا') }}</a>
            <a class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5 hover:text-white" href="{{ url('/') }}#about">{{ __('من نحن') }}</a>
            <a class="rounded-lg px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5 hover:text-white" href="{{ url('/') }}#contact">{{ __('تواصل معنا') }}</a>
        </nav>

        <div class="flex shrink-0 items-center gap-2">
            <a href="{{ route('locale.switch', 'ar') }}" class="inline-flex items-center justify-center rounded-lg border border-white/20 bg-white/5 px-2.5 py-1.5 text-xs font-bold text-white">العربية</a>
            <a href="{{ route('locale.switch', 'en') }}" class="inline-flex items-center justify-center rounded-lg border border-white/20 bg-white/5 px-2.5 py-1.5 text-xs font-bold text-white">English</a>
            <a
                href="{{ route('login') }}"
                class="inline-flex items-center justify-center rounded-lg border border-blue-500/40 bg-blue-600/15 px-3 py-2 text-xs font-bold text-blue-100 shadow-sm shadow-blue-900/20 transition hover:border-blue-400/60 hover:bg-blue-600/25 sm:text-sm"
            >
                {{ __('تسجيل الدخول') }}
            </a>
            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-white md:hidden"
                @click="mobileOpen = !mobileOpen"
                :aria-expanded="mobileOpen"
                aria-controls="public-mobile-nav"
            >
                <span class="sr-only">{{ __('فتح القائمة') }}</span>
                <svg x-show="!mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                <svg x-show="mobileOpen" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </div>

    <div
        id="public-mobile-nav"
        x-show="mobileOpen"
        x-cloak
        x-transition
        class="border-t border-white/10 bg-[#050a14]/95 md:hidden"
    >
        <div class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-3">
            <a class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-100 hover:bg-white/5" href="{{ url('/') }}#top" @click="mobileOpen = false">{{ __('الرئيسية') }}</a>
            <a class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-100 hover:bg-white/5" href="{{ url('/') }}#services" @click="mobileOpen = false">{{ __('خدماتنا') }}</a>
            <a class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-100 hover:bg-white/5" href="{{ url('/') }}#projects" @click="mobileOpen = false">{{ __('مشاريعنا') }}</a>
            <a class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-100 hover:bg-white/5" href="{{ url('/') }}#about" @click="mobileOpen = false">{{ __('من نحن') }}</a>
            <a class="rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-100 hover:bg-white/5" href="{{ url('/') }}#contact" @click="mobileOpen = false">{{ __('تواصل معنا') }}</a>
            <div class="mt-1 flex items-center gap-2">
                <a href="{{ route('locale.switch', 'ar') }}" class="rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-center text-xs font-bold text-white">العربية</a>
                <a href="{{ route('locale.switch', 'en') }}" class="rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-center text-xs font-bold text-white">English</a>
            </div>
            <a class="mt-1 rounded-lg border border-blue-500/40 bg-blue-600/15 px-3 py-2.5 text-center text-sm font-bold text-blue-100" href="{{ route('login') }}">{{ __('تسجيل الدخول') }}</a>
        </div>
    </div>
</header>
