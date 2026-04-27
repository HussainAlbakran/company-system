@extends('layouts.public')

@section('title', __('شركة التقدم للخرسانة الجاهزة | ADVANCE PRECAST COMPANY'))

@php
    $statusLabels = [
        'ongoing' => 'قيد التنفيذ',
        'completed' => 'مكتمل',
        'paused' => 'متوقف مؤقتاً',
        'cancelled' => 'ملغى',
    ];
    $stageLabels = [
        'sales' => 'مبيعات',
        'architect' => 'هندسة',
        'purchasing' => 'مشتريات',
        'production_installation' => 'إنتاج وتركيب',
        'completed' => 'مكتمل',
    ];
@endphp

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
                <div class="mx-auto mb-6 flex justify-center">
                    @if ($logoUrl)
                        <div class="flex h-[4.5rem] w-[4.5rem] sm:h-[5.25rem] sm:w-[5.25rem] items-center justify-center overflow-hidden rounded-xl border border-white/10 bg-white/5 p-2 shadow-lg">
                            <img src="{{ $logoUrl }}" class="h-full w-full object-contain scale-95" />
                        </div>
                    @else
                        <div class="flex h-[4.5rem] w-[4.5rem] sm:h-[5.25rem] sm:w-[5.25rem] items-center justify-center rounded-[1.75rem] border border-white/15 bg-gradient-to-br from-blue-600/30 to-slate-900/90 shadow-2xl">
                            <span class="text-2xl font-black text-blue-100">APC</span>
                        </div>
                    @endif
                </div>

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
                        href="{{ url('/') }}#projects"
                        class="inline-flex w-full min-w-[200px] items-center justify-center rounded-xl bg-gradient-to-l from-blue-600 to-blue-500 px-8 py-3.5 text-base font-bold text-white shadow-lg shadow-blue-900/40 transition hover:from-blue-500 hover:to-blue-400 sm:w-auto"
                    >
                        {{ __('عرض المشاريع') }}
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
                    <article class="group rounded-2xl border border-white/10 bg-gradient-to-b from-white/[0.07] to-transparent p-6 text-start shadow-xl shadow-black/20 transition hover:border-blue-500/30 hover:shadow-blue-900/10">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500/15 text-blue-300 ring-1 ring-blue-400/25">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" /></svg>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-white">الخرسانة الجاهزة</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-400">توريد خرسانة جاهزة بخلطات مدروسة وزمن تسليم يضمن استمرارية الصب ومتانة المنشأ.</p>
                    </article>

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

        {{-- Projects --}}
        <section id="projects" class="relative border-t border-white/5 py-20 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-stretch justify-between gap-4 text-center sm:flex-row sm:items-center sm:text-start">
                    <div class="min-w-0">
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl">{{ __('مشاريعنا') }}</h2>
                    <p class="mt-2 max-w-xl text-slate-400">{{ __('لمحة من أعمالنا وجاهزية التنفيذ عبر مراحل المشروع.') }}</p>
                    </div>
                    @if ($usingSampleProjects)
                        <span class="inline-flex max-w-full items-center justify-center rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-2 text-center text-xs font-bold leading-snug text-amber-200 sm:max-w-sm sm:shrink-0 sm:py-1.5">{{ __('بيانات توضيحية — تُستبدل تلقائياً عند توفر مشاريع في النظام') }}</span>
                    @endif
                </div>

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($projects as $project)
                        @php
                            $progress = (int) round(min(100, max(0, (float) ($project->progress_percentage ?? 0))));
                            $statusKey = $project->status ?? 'ongoing';
                            $statusAr = $statusLabels[$statusKey] ?? $statusKey;
                            $stageKey = $project->current_stage ?? null;
                            $stageAr = $stageKey ? ($stageLabels[$stageKey] ?? $stageKey) : null;
                        @endphp
                        <article class="flex min-w-0 flex-col rounded-2xl border border-white/10 bg-gradient-to-b from-slate-900/80 to-slate-950/90 p-6 shadow-lg shadow-black/30">
                            <h3 class="break-words text-lg font-bold text-white">{{ $project->name }}</h3>
                            <div class="mt-4 flex items-center justify-between gap-2 text-sm">
                                <span class="text-slate-400">نسبة الإنجاز</span>
                                <span class="shrink-0 font-bold text-blue-200">{{ $progress }}٪</span>
                            </div>
                            <div
                                class="relative mt-2 h-2.5 overflow-hidden rounded-full bg-white/10"
                                role="progressbar"
                                aria-valuenow="{{ $progress }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                                aria-label="نسبة إنجاز المشروع {{ $progress }}٪"
                            >
                                <div class="absolute end-0 top-0 h-full rounded-full bg-gradient-to-l from-blue-500 to-blue-400 transition-all" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-white/10 pt-4">
                                <span class="inline-flex rounded-lg bg-white/5 px-2.5 py-1 text-xs font-semibold text-slate-200 ring-1 ring-white/10">الحالة: {{ $statusAr }}</span>
                                @if ($stageAr)
                                    <span class="inline-flex rounded-lg bg-blue-500/10 px-2.5 py-1 text-xs font-semibold text-blue-200 ring-1 ring-blue-400/20">المرحلة: {{ $stageAr }}</span>
                                @endif
                            </div>
                        </article>
                    @endforeach
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
                    <p class="mt-3 text-slate-400">{{ __('فريق المبيعات جاهز لاستقبال استفساراتكم عبر القنوات التالية.') }}</p>
                </div>

                <div class="mx-auto mt-12 grid max-w-2xl gap-3">
                    <x-public.contact-email-row label="مدير المبيعات" email="sales.manager@advanceprecast.com" />
                    <x-public.contact-email-row label="المبيعات 1" email="sales1@advanceprecast.com" />
                    <x-public.contact-email-row label="المبيعات 2" email="sales2@advanceprecast.com" />
                    <x-public.contact-email-row label="المبيعات 3" email="sales3@advanceprecast.com" />
                </div>

                <div class="mx-auto mt-12 grid max-w-2xl gap-4 rounded-2xl border border-dashed border-white/15 bg-white/[0.02] p-6 text-center text-sm text-slate-400">
                    <p class="font-semibold text-slate-300">بيانات إضافية (يمكن تحديثها لاحقاً)</p>
                    <div class="flex flex-wrap items-center justify-center gap-6">
                        <span class="inline-flex items-center gap-2 opacity-80">
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                            الهاتف: — قريباً —
                        </span>
                        <span class="inline-flex items-center gap-2 opacity-80">
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            الموقع: — قريباً —
                        </span>
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