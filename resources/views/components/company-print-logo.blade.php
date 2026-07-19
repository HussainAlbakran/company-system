@php
    $logoUrl = \App\Support\CompanyBranding::logoUrl();
@endphp

@if($logoUrl)
    <img
        src="{{ $logoUrl }}"
        alt="{{ __('auth.company_logo_alt') }}"
        class="company-print-logo {{ $attributes->get('class') }}"
        {{ $attributes->except('class') }}
    >
@endif
