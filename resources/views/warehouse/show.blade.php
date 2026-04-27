@extends('layouts.app')

@php
    $slug = str_replace('-', '_', $sectionKey);
    $sectionTransKey = 'warehouse.section_'.$slug;
    $sectionLabel = __($sectionTransKey);
    if ($sectionLabel === $sectionTransKey) {
        $sectionLabel = __('warehouse.section_unknown');
    }
@endphp

@section('page_title', $sectionLabel)
@section('page_subtitle', __('warehouse.page_title'))

@section('content')

<div class="page-card" dir="rtl" style="text-align:right;">

    <div style="margin-bottom:15px; display:flex; gap:10px;">
        <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
            {{ __('warehouse.back') }}
        </a>

        <a href="{{ route('warehouse.section.input', $sectionKey) }}" class="btn btn-primary">
            {{ __('warehouse.add') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('warehouse.th_name') }}</th>
                    <th>{{ __('warehouse.th_quantity') }}</th>
                    <th>{{ __('warehouse.th_unit') }}</th>
                    <th>{{ __('warehouse.th_notes') }}</th>
                    <th>{{ __('warehouse.th_action') }}</th>
                </tr>
            </thead>

            <tbody>

                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ $item->notes }}</td>

                        <td style="display:flex; gap:6px;">

                            <a href="{{ route('warehouse.edit', $item->id) }}" class="btn btn-sm btn-primary">
                                {{ __('warehouse.edit') }}
                            </a>

                            <form action="{{ route('warehouse.destroy', $item->id) }}" method="POST" onsubmit="return confirm(@json(__('warehouse.confirm_delete')))">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-danger">
                                    {{ __('warehouse.delete') }}
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">
                            {{ __('warehouse.empty_section') }}
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</div>

@endsection
