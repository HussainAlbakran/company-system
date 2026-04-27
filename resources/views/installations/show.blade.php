@extends('layouts.app')

@section('page_title', __('factory.installations_show_title'))
@section('page_subtitle', __('factory.installations_show_subtitle'))

@section('content')
<x-ui.card :title="__('factory.card_install_project_title')" :subtitle="__('factory.card_install_project_sub')">
    <div class="actions-row" style="margin-bottom:12px; flex-wrap:wrap; gap:8px;">
        <a href="{{ route('installations.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>
        @if(auth()->user()->canManageInstallations())
            <a href="{{ route('installations.factory-requests.create', $project) }}" class="btn btn-primary">{{ __('factory.request_from_factory') }}</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-right:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</x-ui.card>

<x-ui.card :title="__('factory.form_project_data_title')">
    <div class="details-grid">
            <div class="detail-box">
                <strong>{{ __('factory.project_code') }}</strong>
                <div>{{ optional($project)->project_code ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.project_name') }}</strong>
                <div>{{ optional($project)->name ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.client') }}</strong>
                <div>{{ optional($project)->client_name ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.main_contractor') }}</strong>
                <div>{{ $project->main_contractor ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.th_planned') }}</strong>
                <div>{{ number_format($project->planned_quantity ?? 0, 2) }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.th_produced_short') }}</strong>
                <div>{{ number_format($project->produced_quantity ?? 0, 2) }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.th_remaining_short') }}</strong>
                <div>{{ number_format($project->remaining_quantity ?? 0, 2) }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.th_completion') }}</strong>
                <div><x-ui.progress :value="$project->progress_percentage ?? 0" /></div>
            </div>
    </div>
</x-ui.card>

<x-ui.card :title="__('factory.factory_requests_for_project')" :subtitle="__('factory.factory_requests_sub', ['name' => $project->name])">
    <x-ui.table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('factory.project_name') }}</th>
                <th>{{ __('factory.th_requester') }}</th>
                <th>{{ __('factory.th_status') }}</th>
                <th>{{ __('factory.th_created') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($installationFactoryRequests ?? [] as $ifr)
                <tr>
                    <td>{{ $ifr->id }}</td>
                    <td><strong>{{ $project->name }}</strong></td>
                    <td>{{ $ifr->creator?->name ?? '-' }}</td>
                    <td>
                        <span class="badge badge-blue">{{ __('factory.installation_status.'.$ifr->status) }}</span>
                    </td>
                    <td>{{ $ifr->created_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        @if($ifr->status === \App\Models\InstallationFactoryRequest::STATUS_DRAFT && (auth()->user()->isAdminLike() || (int) $ifr->created_by === (int) auth()->id()))
                            <a href="{{ route('installations.factory-requests.edit', [$project, $ifr]) }}" class="btn btn-secondary btn-sm">{{ __('factory.edit_draft') }}</a>
                        @else
                            <span class="badge badge-gray">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row">{{ __('factory.factory_requests_empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>
</x-ui.card>

<x-ui.card :title="__('factory.architect_readonly_title')" :subtitle="__('factory.architect_readonly_sub')">
    <div class="details-grid">
            <div class="detail-box">
                <strong>{{ __('factory.drawing_type') }}</strong>
                <div>{{ optional($architectTask)->drawing_type ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.drawing_status') }}</strong>
                <div>
                    <span class="badge badge-blue">
                        {{ optional($architectTask)->drawing_status ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.planning_status') }}</strong>
                <div>
                    <span class="badge badge-blue">
                        {{ optional($architectTask)->planning_status ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.drawing_file') }}</strong>
                <div>
                    @if($architectTask && $architectTask->drawing_file)
                        <a href="{{ asset('storage/' . $architectTask->drawing_file) }}"
                           target="_blank"
                           class="btn btn-primary btn-sm">
                            {{ __('factory.open') }}
                        </a>
                    @else
                        <span class="badge badge-gray">{{ __('factory.file_not_uploaded') }}</span>
                    @endif
                </div>
            </div>

            <div class="detail-box">
                <strong>{{ __('factory.planning_file') }}</strong>
                <div>
                    @if($architectTask && $architectTask->planning_file)
                        <a href="{{ asset('storage/' . $architectTask->planning_file) }}"
                           target="_blank"
                           class="btn btn-primary btn-sm">
                            {{ __('factory.open') }}
                        </a>
                    @else
                        <span class="badge badge-gray">{{ __('factory.file_not_uploaded') }}</span>
                    @endif
                </div>
            </div>

            <div class="detail-box detail-box-full">
                <strong>{{ __('architect.architect_notes') }}</strong>
                <div>{{ optional($architectTask)->notes ?? '-' }}</div>
            </div>
    </div>
</x-ui.card>

<x-ui.card :title="__('factory.measurements_card_title')" :subtitle="__('factory.measurements_card_sub')">
    <x-ui.table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('architect.th_type') }}</th>
                        <th>{{ __('factory.th_element') }}</th>
                        <th>{{ __('architect.th_length') }}</th>
                        <th>{{ __('architect.th_width') }}</th>
                        <th>{{ __('architect.th_height') }}</th>
                        <th>{{ __('architect.th_count') }}</th>
                        <th>{{ __('architect.th_unit') }}</th>
                        <th>{{ __('architect.th_area') }}</th>
                        <th>{{ __('architect.th_volume') }}</th>
                        <th>{{ __('factory.field_notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($measurements as $measurement)
                        <tr>
                            <td>{{ $measurement->id }}</td>
                            <td>{{ $measurement->type ?? '-' }}</td>
                            <td>{{ $measurement->name }}</td>
                            <td>{{ $measurement->length }}</td>
                            <td>{{ $measurement->width }}</td>
                            <td>{{ $measurement->height }}</td>
                            <td>{{ $measurement->quantity }}</td>
                            <td>{{ $measurement->unit ?? 'm' }}</td>
                            <td>{{ $measurement->area }}</td>
                            <td>{{ $measurement->volume }}</td>
                            <td>{{ $measurement->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="empty-row">
                                {{ __('factory.measurements_empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
    </x-ui.table>
</x-ui.card>

<x-ui.card :title="__('factory.production_orders_title')" :subtitle="__('factory.production_orders_sub')">
    <x-ui.table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('factory.order_number') }}</th>
                        <th>{{ __('factory.product_name') }}</th>
                        <th>{{ __('factory.th_planned') }}</th>
                        <th>{{ __('factory.th_produced_short') }}</th>
                        <th>{{ __('factory.th_remaining_short') }}</th>
                        <th>{{ __('factory.th_status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productionOrders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->order_number }}</td>
                            <td>{{ $order->product_name }}</td>
                            <td>{{ number_format((float) $order->planned_quantity, 2) }}</td>
                            <td>{{ number_format((float) $order->produced_quantity, 2) }}</td>
                            <td>{{ number_format((float) $order->remaining_quantity, 2) }}</td>
                            <td>
                                <span class="badge badge-blue">{{ $order->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-row">{{ __('factory.production_orders_empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
    </x-ui.table>
</x-ui.card>

<x-ui.card :title="__('factory.complete_project_title')">
    @if(($project->progress_percentage ?? 0) >= 100)
        <form action="{{ route('installations.complete', $project->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">{{ __('factory.complete_project_btn') }}</button>
        </form>
    @else
        <span class="badge badge-gray">{{ __('factory.awaiting_production') }}</span>
    @endif
</x-ui.card>

@endsection