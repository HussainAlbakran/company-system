@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">قسم المصنع</h1>
            <p style="margin:8px 0 0; color:#6b7280;">
                متابعة أوامر الإنتاج والتوريد بشكل كامل
            </p>
        </div>

        <a href="{{ route('factory.create') }}" class="btn btn-primary">
            ➕ إضافة أمر إنتاج
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
                    <th>#</th>
                    <th>رقم الأمر</th>
                    <th>المنتج</th>
                    <th>المطلوب</th>
                    <th>تم الإنتاج</th>
                    <th>تم التوريد</th>
                    <th>نسبة الإنتاج</th>
                    <th>نسبة التوريد</th>
                    <th>الأيام المتبقية</th>
                    <th>الحالة</th>
                    <th>التفاصيل</th>
                </tr>
            </thead>

            <tbody>

                @forelse($orders as $order)

                <tr>
                    <td>{{ $order->id }}</td>

                    <td>{{ $order->order_number }}</td>

                    <td>
                        <strong>{{ $order->product_name }}</strong>
                    </td>

                    <td>{{ $order->planned_quantity }}</td>

                    <td>{{ $order->produced_quantity }}</td>

                    <td>
                        <span class="badge badge-blue">
                            {{ $order->supplied_quantity }}
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-green">
                            {{ $order->production_percentage }}%
                        </span>
                    </td>

                    <td>
                        <span class="badge badge-orange">
                            {{ $order->supply_percentage }}%
                        </span>
                    </td>

                    <td>
                        @if(!is_null($order->remaining_days_to_end))
                            @if($order->remaining_days_to_end < 0)
                                <span class="badge badge-red">
                                    متأخر {{ abs($order->remaining_days_to_end) }} يوم
                                </span>
                            @elseif($order->remaining_days_to_end <= 7)
                                <span class="badge badge-orange">
                                    {{ $order->remaining_days_to_end }} يوم
                                </span>
                            @else
                                <span class="badge badge-green">
                                    {{ $order->remaining_days_to_end }} يوم
                                </span>
                            @endif
                        @else
                            -
                        @endif
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
                        <a href="{{ route('factory.show', $order->id) }}" class="btn btn-primary btn-sm">
                            عرض
                        </a>
                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="11" class="empty-row">
                        لا توجد أوامر إنتاج حتى الآن
                    </td>
                </tr>

                @endforelse

            </tbody>
        </table>

    </div>

    @if(method_exists($orders, 'links'))
        <div style="margin-top:16px;">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection