@props([
    'label' => '',
    'email' => '',
])

<div {{ $attributes->merge(['class' => 'flex items-start gap-3 rounded-xl border border-white/10 bg-white/[0.03] px-4 py-3 text-start']) }}>
    <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-500/15 text-blue-300 ring-1 ring-blue-400/20" aria-hidden="true">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
    </span>
    <div class="min-w-0 flex-1">
        <div class="text-sm font-bold text-white">{{ $label }}</div>
        <a href="mailto:{{ $email }}" class="mt-1 block truncate text-sm text-blue-200 underline-offset-4 transition hover:text-white hover:underline">{{ $email }}</a>
    </div>
</div>
