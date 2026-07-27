@extends('layouts.public')

@section('title', __('شركة التقدم للخرسانة الجاهزة | ADVANCE PRECAST COMPANY'))

@section('content')
    <div id="top" class="relative overflow-x-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%50%_at_50%-20%,rgba(59,130,246,0.22),transparent)]" aria-hidden="true"></div>

        <x-public.navbar :logo-url="$logoUrl" />

        {{-- Hero --}}
        <section
            class="relative flex min-h-[calc(100vh-4.5rem)] items-center justify-center bg-slate-950 bg-cover bg-center bg-no-repeat px-4 py-16 sm:px-6 lg:px-8"
            style="background-image: url('{{ asset('images/public/hero.png') }}')"
        >
            <div class="absolute inset-0 bg-gradient-to-b from-[#020617]/70 via-[#0a1628]/60 to-[#050a14]/70"></div>

            <div class="relative z-10 mx-auto max-w-2xl text-center flex flex-col items-center space-y-6">
                <p class="font-['Figtree',sans-serif] text-xs font-bold uppercase tracking-[0.35em] text-blue-300/90 sm:text-sm">
                    ADVANCE PRECAST COMPANY
                </p>

                <h1 class="mt-4 text-3xl font-extrabold leading-tight text-white sm:text-4xl md:text-5xl lg:text-6xl">
                    {{ __('شركة التقدم للخرسانة الجاهزة') }}
                </h1>

                <p class="mx-auto mt-5 max-w-2xl text-pretty text-base leading-relaxed text-slate-300 sm:text-lg">
                    {{ __('نبني الثقة كما نبني الخرسانة: جودة عالية، وتنفيذ دقيق، وتوريد موثوق للمشاريع الإنشائية الكبرى.') }}
                </p>

                <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row sm:gap-4">
                    <a
                        href="{{ url('/') }}#services"
                        class="inline-flex w-full min-w-[200px] items-center justify-center rounded-xl bg-gradient-to-l from-blue-600 to-blue-500 px-8 py-3.5 text-base font-bold text-white shadow-lg shadow-blue-900/40 transition hover:from-blue-500 hover:to-blue-400 sm:w-auto"
                    >
                        {{ __('خدماتنا') }}
                    </a>

                    <a
                        href="{{ url('/') }}#contact"
                        class="inline-flex w-full min-w-[200px] items-center justify-center rounded-xl border border-white/20 bg-white/5 px-8 py-3.5 text-base font-bold text-white backdrop-blur transition hover:border-white/30 hover:bg-white/10 sm:w-auto"
                    >
                        {{ __('تواصل معنا') }}
                    </a>
                </div>

                <div class="mt-12 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400 sm:text-sm">
                    <span class="inline-flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        {{ __('خرسانة جاهزة معتمدة') }}
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                        {{ __('منتجات مسبقة الصب') }}
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                        {{ __('دعم هندسي متخصص') }}
                    </span>
                </div>
            </div>
        </section>

        {{-- Services --}}
        <section id="services" class="relative border-t border-white/5 bg-[#060d18] py-20 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl">{{ __('خدماتنا') }}</h2>
                    <p class="mt-3 text-slate-400">{{ __('حلول متكاملة من المصنع إلى موقع العمل، بمعايير جودة تناسب المشاريع الحساسة.') }}</p>
                </div>

                <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <article class="group rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-transparent p-6 text-start shadow-xl shadow-black/20 transition hover:border-blue-500/30">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/15 text-blue-300 ring-1 ring-blue-400/25">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008H18V11.25Zm0 3h.008v.008H18V14.25Zm0 3h.008v.008H18V17.25Z" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-white">المنتجات الخرسانية مسبقة الصب</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-400">عناصر جاهزة بدقة تشكيل عالية لتقليل مدة التنفيذ ورفع جودة التشطيب الإنشائي.</p>
                    </article>

                    <article class="group rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-transparent p-6 text-start shadow-xl shadow-black/20 transition hover:border-blue-500/30 sm:col-span-2 lg:col-span-1">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/15 text-blue-300 ring-1 ring-blue-400/25">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3v.75m0-3h.75m0 0H9m.75 0H9m-.75 0v.75m0-3h.75m0 0H9m.75 0H9" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-white">الحلول الإنشائية</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-400">تنسيق بين التصميم والتوريد والتركيب لتقديم حل متكامل يلائم طبيعة المشروع وجدوله الزمني.</p>
                    </article>

                    <article class="group rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-transparent p-6 text-start shadow-xl shadow-black/20 transition hover:border-blue-500/30 sm:col-span-2 lg:col-span-1">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/15 text-blue-300 ring-1 ring-blue-400/25">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.125 1.125 0 0 0 1.125.75h9.75a1.125 1.125 0 0 0 1.125-1.125v-9.75a1.125 1.125 0 0 0-.375-.825l-6.75-6.75a1.125 1.125 0 0 0-.825-.375H9.75A1.125 1.125 0 0 0 8.25 3v15.75Z" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-white">التوريد للمشاريع</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-400">توريد منظم للمواقع مع التزام بالكميات والمواعيد وبما يتوافق مع متطلبات المقاولين والمطورين.</p>
                    </article>

                    <article class="group rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-transparent p-6 text-start shadow-xl shadow-black/20 transition hover:border-blue-500/30 sm:col-span-2 lg:col-span-1">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/15 text-blue-300 ring-1 ring-blue-400/25">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-5.653a2.548 2.548 0 0 1-.16-.278.872.872 0 0 1 .16-.278l2.79-3.386a2.548 2.548 0 0 1 3.233-.162l4.655 5.653a2.548 2.548 0 0 1 .162 3.233l-2.79 3.386a2.548 2.548 0 0 1-.278.16 2.548 2.548 0 0 1-.278-.16Z" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-white">الدعم الفني والهندسي</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-400">متابعة فنية لمراحل التنفيذ واستشارات تساعد على اتخاذ القرار الصحيح في الأنظمة الإنشائية.</p>
                    </article>
                </div>
            </div>
        </section>

        {{-- About --}}
        <section id="about" class="relative border-t border-white/5 bg-[#060d18] py-20 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
                    <div class="min-w-0 text-start">
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl">{{ __('من نحن') }}</h2>
                        <p class="mt-2 font-['Figtree',sans-serif] text-sm font-semibold uppercase tracking-widest text-blue-300/90">ADVANCE PRECAST COMPANY</p>
                        <div class="mt-8 space-y-5 text-start text-base leading-relaxed text-slate-300">
                            <p>
                                <strong class="text-white">شركة التقدم للخرسانة الجاهزة</strong>
                                شركة متخصصة في الخرسانة الجاهزة والمنتجات الخرسانية مسبقة الصب، نعمل مع فرق تنفيذية منضبطة لخدمة المشاريع الإنشائية بمعايير واضحة وثابتة.
                            </p>
                            <p>
                                <strong class="text-white">رؤيتنا:</strong>
                                تقديم منتجات إنشائية موثوقة تدعم أهداف المطورين والمقاولين من حيث الجودة والتسليم والالتزام بالمواصفات.
                            </p>
                            <p>
                                <strong class="text-white">الجودة والالتزام:</strong>
                                نراجع مراحل الإنتاج والتوريد بعناية، ونعتمد شفافية في التواصل مع العملاء لضمان تطابق المخرجات مع الاتفاق.
                            </p>
                            <p>
                                <strong class="text-white">الاعتماد والتنفيذ:</strong>
                                نبني علاقات طويلة الأمد مبنية على الوفاء بالمواعيد والقدرة على مواكبة متطلبات المواقع الكبرى.
                            </p>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="absolute -inset-4 rounded-3xl bg-gradient-to-br from-blue-600/20 to-transparent blur-2xl" aria-hidden="true"></div>
                        <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-slate-900/60 p-8 text-start shadow-xl">
                            <ul class="space-y-4 text-slate-200">
                                <li class="flex gap-3">
                                    <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-400"></span>
                                    <span>تجربة في دعم مشاريع متعددة المراحل.</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-400"></span>
                                    <span>تركيز على الامتثال للمواصفات والسلامة.</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-400"></span>
                                    <span>تنسيق بين الإنتاج والتوريد والدعم الفني.</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-400"></span>
                                    <span>نهج عملي يقلل المفاجآت في الموقع.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Contact --}}
        <section id="contact" class="relative border-t border-white/5 py-20 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl">{{ __('تواصل معنا') }}</h2>
                    <p class="mt-3 text-slate-400">{{ __('فريق المبيعات جاهز لاستقبال استفساراتكم عبر الإيميلات التالية.') }}</p>
                </div>

                <div class="mx-auto mt-12 grid max-w-2xl gap-3">
                    <x-public.contact-email-row label=" المبيعات" email="emad@atpc-sa.com" />
                    <x-public.contact-email-row label="المبيعات 1" email="Akram@atpc-sa.com" />
                    <x-public.contact-email-row label="المبيعات 2" email="m-qqzzaz@atpc-sa.com" />
                    <x-public.contact-email-row label="المبيعات 3" email="mishary@atpc-sa.com" />
                </div>

                <div class="mx-auto mt-12 grid max-w-2xl gap-4 rounded-2xl border border-dashed border-white/15 bg-white/[0.02] p-6 text-center text-sm text-slate-400">
                    <p class="font-semibold text-slate-300">تواصل شركة التقدم للخرسانة الجاهزة - الموقع</p>
                    <div class="flex flex-wrap items-center justify-center gap-6">
                        <span class="inline-flex items-center gap-2 opacity-80">
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                                  0114443535 -الهاتف-
                        <a href="https://maps.app.goo.gl/yBawF6jBwZ9zBeJL9?g_st=ic" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 opacity-80 hover:opacity-100">
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            الموقع
                        </a>
                    </div>
                    <div class="flex justify-center gap-4 pt-2 opacity-60" aria-label="وسائل التواصل الاجتماعي (قريباً)">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10" title="LinkedIn">in</span>
                        <span class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10" title="X">X</span>
                        <span class="flex h-10 w-10 items-center justify-center rounded-full border border-white/10" title="YouTube">▶️</span>
                    </div>
                </div>

                <div class="mt-12 flex justify-center">
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-l from-blue-600 to-blue-500 px-10 py-3.5 text-base font-bold text-white shadow-lg shadow-blue-900/40 transition hover:from-blue-500 hover:to-blue-400"
                    >
                        {{ __('تسجيل الدخول') }}
                    </a>
                </div>
            </div>
        </section>

        <footer class="border-t border-white/10 bg-[#030712] py-10">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 text-center text-sm text-slate-500 sm:flex-row sm:text-start sm:px-6 lg:px-8">
                <p class="max-w-full font-['Figtree',sans-serif] leading-relaxed">
                    <span dir="ltr" class="inline-block">©️ {{ date('Y') }} ADVANCE PRECAST COMPANY</span>
                    <span class="mx-1 text-slate-600" aria-hidden="true">—</span>
                    <span>شركة التقدم للخرسانة الجاهزة</span>
                </p>
                <a href="{{ route('login') }}" class="shrink-0 font-semibold text-blue-300/90 hover:text-white">{{ __('تسجيل الدخول') }}</a>
            </div>
        </footer>
    </div>
@endsection