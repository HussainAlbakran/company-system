@extends('layouts.app')

@section('page_title', __('factory.show_page_title'))
@section('page_subtitle', __('factory.show_page_subtitle'))

@section('content')
<x-ui.card :title="__('factory.card_order_title')" :subtitle="__('factory.card_order_subtitle')">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('factory.index') }}" class="btn btn-secondary">{{ __('factory.back') }}</a>
        <a href="{{ route('production-orders.edit', $order->id) }}" class="btn btn-warning">{{ __('factory.edit_order') }}</a>
    </div>
    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="alert-danger"><ul style="margin:0; padding-right:18px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
</x-ui.card>

<x-ui.card :title="__('factory.card_order_title')">
    <div class="details-grid">
        <div class="detail-box"><strong>{{ __('factory.project_code') }}</strong><div>{{ optional($project)->project_code ?? '-' }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.project_name') }}</strong><div>{{ optional($project)->name ?? '-' }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.client') }}</strong><div>{{ optional($project)->client_name ?? '-' }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.main_contractor') }}</strong><div>{{ optional($project)->main_contractor ?? '-' }}</div></div>
        @if($project && $project->required_concrete_quantity !== null)
            <div class="detail-box"><strong>{{ __('factory.required_concrete_design') }}</strong><div>{{ number_format((float) $project->required_concrete_quantity, 2) }}</div></div>
        @endif
        <div class="detail-box"><strong>{{ __('factory.product_name') }}</strong><div>{{ $order->product_name ?? '-' }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.order_number') }}</strong><div>{{ $order->order_number ?? '-' }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.required_from_design') }}</strong><div>{{ $order->planned_quantity ?? 0 }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.produced_qty') }}</strong><div>{{ $order->produced_quantity ?? 0 }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.supply_qty') }}</strong><div>{{ $order->supplied_quantity ?? 0 }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.remaining_qty') }}</strong><div>{{ $order->remaining_quantity ?? 0 }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.production_progress') }}</strong><div><x-ui.progress :value="$order->production_percentage" /></div></div>
        <div class="detail-box"><strong>{{ __('factory.supply_progress') }}</strong><div><x-ui.progress :value="$order->supply_percentage" color="#f59e0b" /></div></div>
        <div class="detail-box"><strong>{{ __('factory.expected_completion_days') }}</strong><div>{{ $order->expected_production_days ?? '-' }}</div></div>
        <div class="detail-box">
            <strong>{{ __('factory.days_remaining_to_end') }}</strong>
            <div>
                @if(!is_null($order->remaining_days_to_end))
                    @if($order->remaining_days_to_end < 0)
                        <span class="badge badge-red">{{ __('factory.late_by_days', ['days' => abs($order->remaining_days_to_end)]) }}</span>
                    @elseif($order->remaining_days_to_end <= 7)
                        <span class="badge badge-orange">{{ $order->remaining_days_to_end }} {{ __('factory.days_unit') }}</span>
                    @else
                        <span class="badge badge-green">{{ $order->remaining_days_to_end }} {{ __('factory.days_unit') }}</span>
                    @endif
                @else
                    -
                @endif
            </div>
        </div>
        <div class="detail-box">
            <strong>{{ __('factory.th_status') }}</strong>
            <div>
                @if(($order->status ?? '') === 'completed')
                    <span class="badge badge-green">{{ __('factory.status_completed') }}</span>
                @elseif(($order->status ?? '') === 'in_progress')
                    <span class="badge badge-blue">{{ __('factory.status_in_progress') }}</span>
                @elseif(($order->status ?? '') === 'pending')
                    <span class="badge badge-gray">{{ __('factory.status_pending') }}</span>
                @else
                    <span class="badge badge-gray">{{ $order->status ?? '-' }}</span>
                @endif
            </div>
        </div>
    </div>
</x-ui.card>

<x-ui.card :title="__('factory.architect_data_title')" :subtitle="__('factory.architect_data_subtitle')">
    <div class="details-grid">
        <div class="detail-box"><strong>{{ __('factory.drawing_type') }}</strong><div>{{ optional($architectTask)->drawing_type ?? '-' }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.drawing_status') }}</strong><div>{{ optional($architectTask)->drawing_status ?? '-' }}</div></div>
        <div class="detail-box"><strong>{{ __('factory.planning_status') }}</strong><div>{{ optional($architectTask)->planning_status ?? '-' }}</div></div>
        <div class="detail-box">
            <strong>{{ __('factory.drawing_file') }}</strong>
            <div>@if($architectTask && $architectTask->drawing_file)<a href="{{ asset('storage/' . $architectTask->drawing_file) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('factory.open') }}</a>@else-@endif</div>
        </div>
        <div class="detail-box">
            <strong>{{ __('factory.planning_file') }}</strong>
            <div>@if($architectTask && $architectTask->planning_file)<a href="{{ asset('storage/' . $architectTask->planning_file) }}" target="_blank" class="btn btn-primary btn-sm">{{ __('factory.open') }}</a>@else-@endif</div>
        </div>
        <div class="detail-box detail-box-full"><strong>{{ __('factory.field_notes') }}</strong><div>{{ optional($architectTask)->notes ?? '-' }}</div></div>
    </div>
</x-ui.card>

<x-ui.card :title="__('factory.measurements_title')">
    <x-ui.table>
        <thead>
            <tr>
                <th>#</th><th>{{ __('architect.th_type') }}</th><th>{{ __('factory.th_element') }}</th><th>{{ __('architect.th_length') }}</th>
                <th>{{ __('architect.th_width') }}</th><th>{{ __('architect.th_height') }}</th><th>{{ __('architect.th_count') }}</th>
                <th>{{ __('architect.th_unit') }}</th><th>{{ __('architect.th_area') }}</th><th>{{ __('architect.th_volume') }}</th><th>{{ __('factory.field_notes') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($measurements as $measurement)
                <tr>
                    <td>{{ $measurement->id }}</td><td>{{ $measurement->type ?? '-' }}</td><td>{{ $measurement->name }}</td><td>{{ $measurement->length }}</td>
                    <td>{{ $measurement->width }}</td><td>{{ $measurement->height }}</td><td>{{ $measurement->quantity }}</td><td>{{ $measurement->unit ?? 'm' }}</td>
                    <td>{{ $measurement->area }}</td><td>{{ $measurement->volume }}</td><td>{{ $measurement->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="11" class="empty-row">{{ __('factory.measurements_empty') }}</td></tr>
            @endforelse
        </tbody>
    </x-ui.table>
</x-ui.card>

<div class="form-grid" style="margin-bottom:24px;">
    <x-ui.card :title="__('factory.record_production_title')" :subtitle="__('factory.record_production_sub')">
        <form action="{{ route('production-entries.store') }}" method="POST">
            @csrf
            <input type="hidden" name="production_order_id" value="{{ $order->id }}">
            <input type="hidden" name="project_id" value="{{ $order->project_id }}">
            <div class="form-grid">
                <div class="form-group"><label>{{ __('factory.field_entry_date') }}</label><input type="date" name="entry_date" value="{{ old('entry_date') }}" required></div>
                <div class="form-group"><label>{{ __('factory.field_quantity') }}</label><input type="number" step="0.01" name="quantity" value="{{ old('quantity') }}" required></div>
                <div class="form-group"><label>{{ __('factory.field_start_time') }}</label><input type="time" name="start_time" value="{{ old('start_time') }}"></div>
                <div class="form-group"><label>{{ __('factory.field_end_time') }}</label><input type="time" name="end_time" value="{{ old('end_time') }}"></div>
                <div class="form-group"><label>{{ __('factory.field_employee_id') }}</label><input type="number" name="employee_id" value="{{ old('employee_id') }}"></div>
                <div class="form-group form-group-full"><label>{{ __('factory.field_notes') }}</label><textarea name="notes">{{ old('notes') }}</textarea></div>
            </div>
            <div class="form-actions"><button type="submit" class="btn btn-primary">{{ __('factory.save_production') }}</button></div>
        </form>
    </x-ui.card>

    <x-ui.card :title="__('factory.record_supply_title')" :subtitle="__('factory.record_supply_sub')">
        <form action="{{ route('production-supplies.store') }}" method="POST">
            @csrf
            <input type="hidden" name="production_order_id" value="{{ $order->id }}">
            <div class="form-grid">
                <div class="form-group"><label>{{ __('factory.field_supply_date') }}</label><input type="date" name="supply_date" value="{{ old('supply_date') }}" required></div>
                <div class="form-group"><label>{{ __('factory.field_supply_quantity') }}</label><input type="number" step="0.01" name="quantity" value="{{ old('quantity') }}" required></div>
                <div class="form-group"><label>{{ __('factory.field_receiver_name') }}</label><input type="text" name="receiver_name" value="{{ old('receiver_name') }}"></div>
                <div class="form-group form-group-full"><label>{{ __('factory.field_notes') }}</label><textarea name="notes">{{ old('notes') }}</textarea></div>
            </div>
            <div class="form-actions"><button type="submit" class="btn btn-success">{{ __('factory.save_supply') }}</button></div>
        </form>
    </x-ui.card>
</div>

<x-ui.card :title="__('factory.production_log_title')">
    <x-ui.table>
        <thead><tr><th>{{ __('factory.th_date') }}</th><th>{{ __('factory.field_quantity') }}</th><th>{{ __('factory.field_start_time') }}</th><th>{{ __('factory.field_end_time') }}</th><th>{{ __('factory.th_working_hours') }}</th></tr></thead>
        <tbody>
            @forelse($order->entries as $entry)
                <tr><td>{{ $entry->entry_date ?? '-' }}</td><td>{{ $entry->quantity ?? 0 }}</td><td>{{ $entry->start_time ?? '-' }}</td><td>{{ $entry->end_time ?? '-' }}</td><td>{{ $entry->working_hours ?? '-' }}</td></tr>
            @empty
                <tr><td colspan="5" class="empty-row">{{ __('factory.production_log_empty') }}</td></tr>
            @endforelse
        </tbody>
    </x-ui.table>
</x-ui.card>

<x-ui.card :title="__('factory.supply_log_title')">
    <x-ui.table>
        <thead><tr><th>{{ __('factory.th_date') }}</th><th>{{ __('factory.th_supply_qty') }}</th><th>{{ __('factory.th_receiver') }}</th><th>{{ __('factory.field_notes') }}</th></tr></thead>
        <tbody>
            @forelse($order->supplies as $supply)
                <tr><td>{{ $supply->supply_date ?? '-' }}</td><td>{{ $supply->quantity ?? 0 }}</td><td>{{ $supply->receiver_name ?? '-' }}</td><td>{{ $supply->notes ?? '-' }}</td></tr>
            @empty
                <tr><td colspan="4" class="empty-row">{{ __('factory.supply_log_empty') }}</td></tr>
            @endforelse
        </tbody>
    </x-ui.table>
</x-ui.card>
@endsection
