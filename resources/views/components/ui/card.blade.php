@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'page-card']) }}>
    @if($title || $subtitle)
        <div class="page-header">
            <div>
                @if($title)
                    <h3 style="margin:0; font-size:14px; font-weight:800; color:#f8fbff;">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="page-subtitle" style="margin:3px 0 0;">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    @endif

    {{ $slot }}
</div>
