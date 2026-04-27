@extends('layouts.app')

@section('page_title', __('factory.index_page_title'))
@section('page_subtitle', __('factory.index_page_subtitle'))

@section('content')
<x-ui.card :title="__('factory.install_requests_card_title')" :subtitle="__('factory.install_requests_card_subtitle')">
    <div class="actions-row" style="margin-bottom:12px; flex-wrap:wrap; gap:8px; align-items:center;">
        <a href="{{ route('factory.installation-requests.index') }}" class="btn btn-primary">{{ __('factory.view_install_requests') }}</a>
        @if(($pendingInstallationFactoryRequestsCount ?? 0) > 0)
            <span class="badge badge-orange" title="{{ __('factory.pending_receipt_badge') }}">{{ $pendingInstallationFactoryRequestsCount }}</span>
        @endif
    </div>
</x-ui.card>

<x-ui.card :title="__('factory.orders_card_title')" :subtitle="__('factory.orders_card_subtitle')">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('production-orders.create') }}" class="btn btn-primary">+ {{ __('factory.add_production_order') }}</a>
    </div>
    <x-ui.table>
            <thead>
                <tr>
                    <th>{{ __('factory.th_number') }}</th>
                    <th>{{ __('factory.th_project') }}</th>
                    <th>{{ __('factory.th_order') }}</th>
                    <th>{{ __('factory.th_product') }}</th>
                    <th>{{ __('factory.th_required') }}</th>
                    <th>{{ __('factory.th_produced') }}</th>
                    <th>{{ __('factory.th_remaining') }}</th>
                    <th>{{ __('factory.th_progress') }}</th>
                    <th>{{ __('factory.th_status') }}</th>
                    <th>{{ __('factory.th_details') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>
                        @if($order->project)
                            <strong>{{ $order->project->project_code }}</strong><br>
                            <span style="color:#6b7280;">{{ $order->project->name }}</span>
                        @else
                            <span class="badge badge-red">{{ __('factory.not_linked_project') }}</span>
                        @endif
                    </td>
                    <td>{{ $order->order_number }}</td>
                    <td><strong>{{ $order->product_name }}</strong></td>
                    <td>{{ number_format((float) $order->planned_quantity, 2) }}</td>
                    <td>{{ number_format((float) $order->produced_quantity, 2) }}</td>
                    <td>{{ number_format((float) $order->remaining_quantity, 2) }}</td>
                    <td style="min-width:160px;">
                        <x-ui.progress :value="$order->production_percentage" />
                    </td>
                    <td>
                        @if($order->status == 'completed')
                            <span class="badge badge-green">{{ __('factory.status_completed') }}</span>
                        @elseif($order->status == 'in_progress')
                            <span class="badge badge-blue">{{ __('factory.status_in_progress') }}</span>
                        @elseif($order->status == 'pending')
                            <span class="badge badge-gray">{{ __('factory.status_pending') }}</span>
                        @else
                            <span class="badge badge-gray">{{ $order->status }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('production-orders.show', $order->id) }}" class="btn btn-primary btn-sm">{{ __('factory.view') }}</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="empty-row">{{ __('factory.orders_empty') }}</td></tr>
                @endforelse
            </tbody>
    </x-ui.table>
    @if(method_exists($orders, 'links'))<div style="margin-top:16px;">{{ $orders->links() }}</div>@endif
</x-ui.card>
@endsection