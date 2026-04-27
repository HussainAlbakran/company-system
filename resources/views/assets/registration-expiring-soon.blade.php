@extends('layouts.app')

@section('page_title', __('assets.registration_page_title'))
@section('page_subtitle', __('assets.registration_page_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('assets.registration_page_title') }}</h1>
            <p style="color:#6b7280;">{{ __('assets.registration_page_subtitle') }}</p>
        </div>
        <div class="actions-row">
            <a href="{{ route('assets.index') }}" class="btn btn-secondary">{{ __('assets.back_to_assets') }}</a>
        </div>
    </div>

    <div class="page-card" style="margin-bottom:20px;">
        <h2 style="margin:0 0 8px; font-size:18px;">{{ __('assets.registration_section_title') }}</h2>
        <p style="color:#6b7280; margin:0 0 12px;">{{ __('assets.registration_section_sub') }}</p>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('assets.th_serial') }}</th>
                        <th>{{ __('assets.th_quantity') }}</th>
                        <th>{{ __('assets.field_plate_number') }}</th>
                        <th>{{ __('assets.field_vehicle_type') }}</th>
                        <th>{{ __('assets.field_color') }}</th>
                        <th>{{ __('assets.field_registration_expiry') }}</th>
                        <th>{{ __('assets.th_days_remaining') }}</th>
                        <th>{{ __('assets.th_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrationAssets as $asset)
                        <tr>
                            <td>
                                <a href="{{ route('assets.show', $asset->id) }}" class="employee-link">{{ $asset->serial_number }}</a>
                            </td>
                            <td>{{ $asset->quantity }}</td>
                            <td>{{ $asset->plate_number ?? '-' }}</td>
                            <td>{{ $asset->vehicle_type ?? '-' }}</td>
                            <td>{{ $asset->color ?? '-' }}</td>
                            <td>{{ $asset->registration_expiry_date ?? '-' }}</td>
                            <td>
                                @if($asset->days_remaining <= 7)
                                    <span class="badge badge-red">{{ $asset->days_remaining }}</span>
                                @elseif($asset->days_remaining <= 15)
                                    <span class="badge badge-orange">{{ $asset->days_remaining }}</span>
                                @else
                                    <span class="badge badge-blue">{{ $asset->days_remaining }}</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->status === 'available')
                                    <span class="badge badge-green">{{ __('assets.status_available') }}</span>
                                @elseif($asset->status === 'assigned')
                                    <span class="badge badge-orange">{{ __('assets.status_assigned_with_employee') }}</span>
                                @else
                                    <span class="badge badge-gray">{{ __('assets.status_maintenance') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-row">{{ __('assets.registration_empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px;">
            {{ $registrationAssets->links() }}
        </div>
    </div>

    <div class="page-card">
        <h2 style="margin:0 0 8px; font-size:18px;">{{ __('assets.inspection_section_title') }}</h2>
        <p style="color:#6b7280; margin:0 0 12px;">{{ __('assets.inspection_section_sub') }}</p>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('assets.th_serial') }}</th>
                        <th>{{ __('assets.th_quantity') }}</th>
                        <th>{{ __('assets.field_plate_number') }}</th>
                        <th>{{ __('assets.field_vehicle_type') }}</th>
                        <th>{{ __('assets.field_color') }}</th>
                        <th>{{ __('assets.field_inspection_expiry') }}</th>
                        <th>{{ __('assets.th_days_remaining') }}</th>
                        <th>{{ __('assets.th_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspectionAssets as $asset)
                        <tr>
                            <td>
                                <a href="{{ route('assets.show', $asset->id) }}" class="employee-link">{{ $asset->serial_number }}</a>
                            </td>
                            <td>{{ $asset->quantity }}</td>
                            <td>{{ $asset->plate_number ?? '-' }}</td>
                            <td>{{ $asset->vehicle_type ?? '-' }}</td>
                            <td>{{ $asset->color ?? '-' }}</td>
                            <td>{{ $asset->inspection_expiry_date ?? '-' }}</td>
                            <td>
                                @if($asset->days_remaining <= 7)
                                    <span class="badge badge-red">{{ $asset->days_remaining }}</span>
                                @elseif($asset->days_remaining <= 15)
                                    <span class="badge badge-orange">{{ $asset->days_remaining }}</span>
                                @else
                                    <span class="badge badge-blue">{{ $asset->days_remaining }}</span>
                                @endif
                            </td>
                            <td>
                                @if($asset->status === 'available')
                                    <span class="badge badge-green">{{ __('assets.status_available') }}</span>
                                @elseif($asset->status === 'assigned')
                                    <span class="badge badge-orange">{{ __('assets.status_assigned_with_employee') }}</span>
                                @else
                                    <span class="badge badge-gray">{{ __('assets.status_maintenance') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-row">{{ __('assets.inspection_empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:16px;">
            {{ $inspectionAssets->links() }}
        </div>
    </div>
</div>
@endsection
