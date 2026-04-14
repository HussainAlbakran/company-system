@extends('layouts.app')

@section('content')

<div class="page-card">

    {{-- Header --}}
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">تفاصيل الأصل</h1>
            <p style="color:#6b7280;">عرض كامل لبيانات الأصل</p>
        </div>

        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
            رجوع
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- بيانات الأصل --}}
    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">معلومات الأصل</h2>
        </div>

        <div class="details-grid">

            <div class="detail-box">
                <strong>اسم الأصل</strong>
                <div>{{ $asset->name }}</div>
            </div>

            <div class="detail-box">
                <strong>الرقم التسلسلي</strong>
                <div>{{ $asset->serial_number }}</div>
            </div>

            <div class="detail-box">
                <strong>الكمية</strong>
                <div>{{ $asset->quantity }}</div>
            </div>

            <div class="detail-box">
                <strong>الحالة</strong>
                <div>
                    @if($asset->status == 'available')
                        <span class="badge badge-green">متاح</span>
                    @elseif($asset->status == 'assigned')
                        <span class="badge badge-blue">مُسلم</span>
                    @elseif($asset->status == 'maintenance')
                        <span class="badge badge-orange">صيانة</span>
                    @endif
                </div>
            </div>

            <div class="detail-box">
                <strong>تاريخ الشراء</strong>
                <div>{{ $asset->purchase_date ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>مرتبط بمشتريات</strong>
                <div>
                    @if($asset->purchase)
                        <span class="badge badge-blue">
                            {{ $asset->purchase->title }}
                        </span>
                    @else
                        <span class="badge badge-gray">غير مرتبط</span>
                    @endif
                </div>
            </div>

            <div class="detail-box detail-box-full">
                <strong>ملاحظات</strong>
                <div>{{ $asset->notes ?? '-' }}</div>
            </div>

        </div>
    </div>

    {{-- 🔥 سجل التسليم (العهدة) --}}
    <div class="page-card">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">سجل العهدة</h2>
            <p style="color:#6b7280;">الموظفين الذين استلموا هذا الأصل</p>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الموظف</th>
                        <th>تاريخ البداية</th>
                        <th>تاريخ النهاية</th>
                        <th>الحالة</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($asset->assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->id }}</td>

                            <td>
                                {{ optional($assignment->employee)->name ?? '-' }}
                            </td>

                            <td>{{ $assignment->start_date }}</td>

                            <td>{{ $assignment->end_date ?? '-' }}</td>

                            <td>
                                @if($assignment->status == 'active')
                                    <span class="badge badge-green">نشط</span>
                                @else
                                    <span class="badge badge-gray">منتهي</span>
                                @endif
                            </td>

                            <td>{{ $assignment->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-row">
                                لا يوجد سجل عهدة لهذا الأصل
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

</div>

@endsection