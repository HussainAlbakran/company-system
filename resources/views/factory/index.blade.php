@extends('layouts.app')

@section('page_title', 'Factory')
@section('page_subtitle', 'Production orders and live progress')

@section('content')
<x-ui.card title="Factory Orders" subtitle="Required, produced, remaining quantities">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('production-orders.create') }}" class="btn btn-primary">+ Add Production Order</a>
    </div>
    <x-ui.table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Project</th>
                    <th>Order</th>
                    <th>Product</th>
                    <th>Required</th>
                    <th>Produced</th>
                    <th>Remaining</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Details</th>
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
                            <span class="badge badge-red">غير مربوط بمشروع</span>
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
                            <span class="badge badge-green">مكتمل</span>
                        @elseif($order->status == 'in_progress')
                            <span class="badge badge-blue">قيد التنفيذ</span>
                        @elseif($order->status == 'pending')
                            <span class="badge badge-gray">قيد الانتظار</span>
                        @else
                            <span class="badge badge-gray">{{ $order->status }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('production-orders.show', $order->id) }}" class="btn btn-primary btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="10" class="empty-row">No production orders yet</td></tr>
                @endforelse
            </tbody>
    </x-ui.table>
    @if(method_exists($orders, 'links'))<div style="margin-top:16px;">{{ $orders->links() }}</div>@endif
</x-ui.card>
@endsection