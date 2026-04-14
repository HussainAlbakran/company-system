@extends('layouts.app')

@section('content')

<div class="page-card" dir="rtl" style="text-align:right;">

    <div style="margin-bottom:15px; display:flex; gap:10px;">
        <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
            رجوع
        </a>

        <a href="{{ route('warehouse.section.input', $sectionKey) }}" class="btn btn-primary">
            إضافة
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
                    <th>الاسم</th>
                    <th>الكمية</th>
                    <th>الوحدة</th>
                    <th>ملاحظات</th>
                    <th>الإجراء</th>
                </tr>
            </thead>

            <tbody>

                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ $item->notes }}</td>

                        <td style="display:flex; gap:6px;">

                            {{-- تعديل --}}
                            <a href="{{ route('warehouse.edit', $item->id) }}" class="btn btn-sm btn-primary">
                                تعديل
                            </a>

                            {{-- حذف --}}
                            <form action="{{ route('warehouse.destroy', $item->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-danger">
                                    حذف
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">
                            لا توجد بيانات لهذا القسم
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</div>

@endsection