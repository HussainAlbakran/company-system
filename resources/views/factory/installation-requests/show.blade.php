@extends('layouts.app')

@php
    $p = $requestModel->project;
@endphp

@section('page_title', __('factory.install_req_show_title'))
@section('page_subtitle', __('factory.install_req_show_subtitle', ['name' => ($p?->name ?? '-')]))

@section('content')
<x-ui.card :title="__('factory.card_install_detail_title')" :subtitle="__('factory.install_req_show_subtitle', ['name' => ($p?->name ?? '-')])">
    <div class="actions-row" style="margin-bottom:12px; flex-wrap:wrap; gap:8px;">
        <a href="{{ route('factory.installation-requests.index') }}" class="btn btn-secondary btn-sm">{{ __('factory.back_to_list') }}</a>
        <a href="{{ route('factory.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('factory.to_factory') }}</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <div class="details-grid" style="margin-bottom:20px;">
        <div class="detail-box">
            <strong>{{ __('factory.project_name') }}</strong>
            <div><strong>{{ $p?->name ?? '-' }}</strong></div>
        </div>
        <div class="detail-box">
            <strong>{{ __('factory.project_code') }}</strong>
            <div>{{ $p?->project_code ?? '-' }}</div>
        </div>
        <div class="detail-box">
            <strong>{{ __('factory.th_requester') }}</strong>
            <div>{{ $requestModel->creator?->name ?? '-' }}</div>
        </div>
        <div class="detail-box">
            <strong>{{ __('factory.th_status') }}</strong>
            <div>
                <span class="badge badge-blue">{{ __('factory.installation_status.'.$requestModel->status) }}</span>
            </div>
        </div>
        <div class="detail-box">
            <strong>{{ __('factory.submitted_at') }}</strong>
            <div>{{ $requestModel->submitted_at?->format('Y-m-d H:i') ?? '-' }}</div>
        </div>
        <div class="detail-box detail-box-full">
            <strong>{{ __('factory.request_notes') }}</strong>
            <div>{{ $requestModel->notes ?? '-' }}</div>
        </div>
    </div>

    <h3 style="margin:16px 0 8px; font-size:18px;">{{ __('factory.line_items') }}</h3>
    <x-ui.table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('factory.th_item_name') }}</th>
                <th>{{ __('factory.th_description') }}</th>
                <th>{{ __('factory.field_quantity') }}</th>
                <th>{{ __('architect.th_unit') }}</th>
                <th>{{ __('factory.th_reason') }}</th>
                <th>{{ __('factory.field_notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requestModel->items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td><strong>{{ $item->item_name }}</strong></td>
                    <td>{{ $item->description ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ $item->reason ?? '-' }}</td>
                    <td>{{ $item->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-row">{{ __('factory.line_items_empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @php
        $st = $requestModel->status;
        $nextStatusOptions = [];
        if ($st === \App\Models\InstallationFactoryRequest::STATUS_SUBMITTED) {
            $nextStatusOptions = [
                'received' => __('factory.installation_status.received'),
                'processing' => __('factory.installation_status.processing'),
                'completed' => __('factory.installation_status.completed'),
            ];
        } elseif ($st === \App\Models\InstallationFactoryRequest::STATUS_RECEIVED) {
            $nextStatusOptions = [
                'processing' => __('factory.installation_status.processing'),
                'completed' => __('factory.installation_status.completed'),
            ];
        } elseif ($st === \App\Models\InstallationFactoryRequest::STATUS_PROCESSING) {
            $nextStatusOptions = ['completed' => __('factory.installation_status.completed')];
        }
    @endphp
    @if(count($nextStatusOptions) > 0)
        <div class="page-card" style="margin-top:20px;">
            <div class="page-header">
                <div>
                    <h3 style="margin:0; font-size:14px; font-weight:800; color:#f8fbff;">{{ __('factory.update_status_title') }}</h3>
                    <p class="page-subtitle" style="margin:3px 0 0;">{{ __('factory.update_status_sub', ['name' => ($p?->name ?? '-')]) }}</p>
                </div>
            </div>
            <form action="{{ route('factory.installation-requests.update-status', $requestModel) }}" method="POST" class="actions-row" style="flex-wrap:wrap; gap:10px; align-items:flex-end;">
                @csrf
                @method('PATCH')
                <div class="form-group" style="margin:0;">
                    <label>{{ __('factory.new_status') }}</label>
                    <select name="status" class="form-control" required>
                        @foreach($nextStatusOptions as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('factory.save_status') }}</button>
            </form>
            <p style="color:#94a3b8; font-size:13px; margin-top:8px;">{{ __('factory.current_status_label') }} {{ __('factory.installation_status.'.$requestModel->status) }}</p>
        </div>
    @endif
</x-ui.card>
@endsection
