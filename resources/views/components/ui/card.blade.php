@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'page-card']) }}>
    @if($title || $subtitle)
        <div class="page-header">
            <div>
                @if($title)
                    <h3 style="margin:0; font-size:14px; font-weight:800; color:#000000;">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="page-subtitle" style="margin:3px 0 0; color:#000000; line-height:1.55;">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    @endif

    {{ $slot }}
</div>
