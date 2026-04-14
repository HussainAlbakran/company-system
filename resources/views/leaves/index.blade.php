@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header">
        <h2>إدارة الإجازات</h2>
        <p>عرض طلبات الإجازات واعتمادها أو رفضها</p>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الموظف</th>
                    <th>تاريخ البداية</th>
                    <th>تاريخ النهاية</th>
                    <th>عدد الأيام</th>
                    <th>الرصيد الحالي</th>
                    <th>الحالة</th>
                    <th>السبب</th>
                    <th>الإجراء</th>
                </tr>
            </thead>

            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td>{{ $leave->id }}</td>

                        <td>
                            {{ $leave->employee->name ?? '-' }}
                        </td>

                        <td>{{ $leave->start_date }}</td>

                        <td>{{ $leave->end_date }}</td>

                        <td>{{ $leave->days }}</td>

                        <td>
                            {{ $leave->employee->leave_balance ?? 0 }}
                        </td>

                        <td>
                            @if($leave->status === 'approved')
                                <span class="badge badge-green">معتمدة</span>
                            @elseif($leave->status === 'rejected')
                                <span class="badge badge-red">مرفوضة</span>
                            @else
                                <span class="badge badge-orange">قيد الانتظار</span>
                            @endif
                        </td>

                        <td>{{ $leave->reason ?? '-' }}</td>

                        <td>
                            @if($leave->status === 'pending')
                                <div class="actions-row">
                                    <form action="{{ route('leaves.approve', $leave->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            قبول
                                        </button>
                                    </form>

                                    <form action="{{ route('leaves.reject', $leave->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            رفض
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="badge badge-gray">تمت المعالجة</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-row">
                            لا توجد طلبات إجازة حاليًا
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection