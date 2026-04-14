@props([
    'value' => 0,
    'color' => '#3b82f6',
])

@php
    $progress = max(0, min(100, (float) $value));
@endphp

<div style="width:100%;">
    <div style="height:6px; border-radius:999px; background:rgba(143,163,193,.24); overflow:hidden; border:1px solid rgba(143,163,193,.22);">
        <div style="height:100%; width: {{ $progress }}%; background: {{ $color }}; border-radius:999px; box-shadow:0 0 8px {{ $color }}66;"></div>
    </div>
    <div style="margin-top:3px; font-size:10px; color:#91a3c0;">{{ number_format($progress, 0) }}%</div>
</div>
