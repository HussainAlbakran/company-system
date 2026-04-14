@props([
    'compact' => true,
])

<div {{ $attributes->merge(['class' => 'table-wrap']) }}>
    <table @if($compact) style="font-size:11.5px;" @endif>
        {{ $slot }}
    </table>
</div>
