@extends('layouts.app')

@section('page_title', __('assets.page_title'))
@section('page_subtitle', __('assets.page_subtitle'))

@section('content')
<style>
    .filter-card-link { text-decoration: none; color: inherit; display: block; }
    .filter-card-link.active .detail-box {
        border-color: rgba(59,130,246,.5);
        box-shadow: inset 0 0 0 1px rgba(59,130,246,.2);
    }
</style>

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">{{ __('assets.page_title') }}</h1>
            <p style="color:#6b7280;">
                {{ __('assets.page_subtitle') }}
            </p>
        </div>
        <div class="actions-row">
            <a href="{{ route('assets.with-employees') }}" class="btn btn-secondary btn-sm">الأصول التي مع الموظفين</a>
            <a href="{{ route('assets.registration-expiring-soon') }}" class="btn btn-warning btn-sm">{{ __('assets.registration_expiring_link') }}</a>
        </div>
    </div>

    <div class="form-grid" style="margin-bottom:20px;">

        <a href="{{ route('assets.index') }}" class="filter-card-link {{ request('status') ? '' : 'active' }}">
            <div class="detail-box">
                <strong>{{ __('assets.stat_total') }}</strong>
                <div class="badge badge-blue">
                    {{ $totalAssetsCount }}
                </div>
            </div>
        </a>

        <a href="{{ route('assets.index', ['status' => 'available']) }}" class="filter-card-link {{ request('status') === 'available' ? 'active' : '' }}">
            <div class="detail-box">
                <strong>{{ __('assets.stat_available') }}</strong>
                <div class="badge badge-green">
                    {{ $availableAssetsCount }}
                </div>
            </div>
        </a>

        <a href="{{ route('assets.index', ['status' => 'assigned']) }}" class="filter-card-link {{ request('status') === 'assigned' ? 'active' : '' }}">
            <div class="detail-box">
                <strong>{{ __('assets.stat_assigned') }}</strong>
                <div class="badge badge-orange">
                    {{ $assignedAssetsCount }}
                </div>
            </div>
        </a>

        <a href="{{ route('assets.index', ['status' => 'maintenance']) }}" class="filter-card-link {{ request('status') === 'maintenance' ? 'active' : '' }}">
            <div class="detail-box">
                <strong>{{ __('assets.stat_maintenance') }}</strong>
                <div class="badge badge-gray">
                    {{ $maintenanceAssetsCount }}
                </div>
            </div>
        </a>

    </div>

    <div class="page-card" style="margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:10px;">
            <input type="text" name="search" placeholder="{{ __('assets.search_placeholder') }}"
                   class="form-control" value="{{ request('search') }}">

            <button class="btn btn-primary">{{ __('common.search') }}</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('assets.th_number') }}</th>
                    <th>{{ __('assets.th_name') }}</th>
                    <th>{{ __('assets.th_quantity') }}</th>
                    <th>{{ __('assets.th_serial') }}</th>
                    <th>{{ __('assets.th_status') }}</th>
                    <th>{{ __('assets.th_purchase_date') }}</th>
                    <th>{{ __('assets.th_view') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($assets as $asset)
                    <tr>
                        <td>{{ $asset->id }}</td>

                        <td>
                            <strong>{{ $asset->name }}</strong>
                        </td>

                        <td>{{ $asset->quantity }}</td>

                        <td>
                            <a href="{{ route('assets.show', $asset->id) }}" class="employee-link">
                                {{ $asset->serial_number }}
                            </a>
                        </td>

                        <td>
                            @if($asset->status == 'available')
                                <span class="badge badge-green">{{ __('assets.status_available') }}</span>
                            @elseif($asset->status == 'assigned')
                                <span class="badge badge-orange">{{ __('assets.status_assigned_with_employee') }}</span>
                            @else
                                <span class="badge badge-gray">{{ __('assets.status_maintenance') }}</span>
                            @endif
                        </td>

                        <td>{{ $asset->purchase_date ?? '-' }}</td>

                        <td>
                            <a href="{{ route('assets.show', $asset->id) }}"
                               class="btn btn-primary btn-sm">
                                {{ __('common.view') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">
                            {{ __('assets.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $assets->links() }}
    </div>

</div>

@endsection
