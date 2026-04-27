@extends('layouts.app')

@section('page_title', __('warehouse.page_title'))
@section('page_subtitle', __('warehouse.page_subtitle'))

@section('content')

@php
    $warehouseSections = [
        'diesel',
        'oils',
        'wood',
        'concrete-materials',
        'concrete-chemicals',
        'operational-materials',
        'rebar',
        'strands',
        'extra-materials',
    ];
@endphp

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">{{ __('warehouse.page_title') }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">
                {{ __('warehouse.page_subtitle') }}
            </p>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('warehouse.th_section') }}</th>
                    <th>{{ __('warehouse.th_input') }}</th>
                    <th>{{ __('warehouse.th_view') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach($warehouseSections as $section)
                    @php
                        $slug = str_replace('-', '_', $section);
                        $key = 'warehouse.section_'.$slug;
                        $label = __($key);
                        if ($label === $key) {
                            $label = __('warehouse.section_unknown');
                        }
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td>
                            <a href="{{ route('warehouse.section.input', $section) }}" class="btn btn-primary btn-sm">
                                {{ __('warehouse.input') }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('warehouse.section.show', $section) }}" class="btn btn-secondary btn-sm">
                                {{ __('warehouse.view') }}
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
