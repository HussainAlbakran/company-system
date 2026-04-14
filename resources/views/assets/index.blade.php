@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">الأصول</h1>
            <p style="color:#6b7280;">
                جميع الأصول التابعة للشركة
            </p>
        </div>
    </div>

    {{-- الإحصائيات --}}
    <div class="form-grid" style="margin-bottom:20px;">

        <div class="detail-box">
            <strong>إجمالي الأصول</strong>
            <div class="badge badge-blue">
                {{ $totalAssetsCount }}
            </div>
        </div>

        <div class="detail-box">
            <strong>متاحة</strong>
            <div class="badge badge-green">
                {{ $availableAssetsCount }}
            </div>
        </div>

        <div class="detail-box">
            <strong>مع موظفين</strong>
            <div class="badge badge-orange">
                {{ $assignedAssetsCount }}
            </div>
        </div>

        <div class="detail-box">
            <strong>في الصيانة</strong>
            <div class="badge badge-gray">
                {{ $maintenanceAssetsCount }}
            </div>
        </div>

    </div>

    {{-- البحث --}}
    <div class="page-card" style="margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:10px;">
            <input type="text" name="search" placeholder="ابحث باسم الأصل أو الرقم التسلسلي"
                   class="form-control" value="{{ request('search') }}">

            <button class="btn btn-primary">بحث</button>
        </form>
    </div>

    {{-- الجدول --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم الأصل</th>
                    <th>الكمية</th>
                    <th>الرقم التسلسلي</th>
                    <th>الحالة</th>
                    <th>تاريخ الشراء</th>
                    <th>عرض</th>
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

                        <td>{{ $asset->serial_number }}</td>

                        <td>
                            @if($asset->status == 'available')
                                <span class="badge badge-green">متاح</span>
                            @elseif($asset->status == 'assigned')
                                <span class="badge badge-orange">مع موظف</span>
                            @else
                                <span class="badge badge-gray">صيانة</span>
                            @endif
                        </td>

                        <td>{{ $asset->purchase_date ?? '-' }}</td>

                        <td>
                            <a href="{{ route('assets.show', $asset->id) }}"
                               class="btn btn-primary btn-sm">
                                عرض
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-row">
                            لا توجد أصول حالياً
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