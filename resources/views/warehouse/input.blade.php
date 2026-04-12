@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">إدخال القسم: {{ $section }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">
                جدول إدخال خاص بالقسم — املأ الأعمدة حسب البند والكمية والوحدة والملاحظات
            </p>
        </div>

        <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
            رجوع
        </a>
    </div>

    <div class="table-wrap">
        <table aria-label="جدول إدخال مواد قسم {{ $section }}">
            <thead>
                <tr>
                    <th scope="col">البند</th>
                    <th scope="col">الكمية</th>
                    <th scope="col">الوحدة</th>
                    <th scope="col">ملاحظات</th>
                </tr>
            </thead>

            <tbody>
                @for($i = 0; $i < 20; $i++)
                    <tr>
                        <td contenteditable="true" tabindex="0" style="min-height:2.25rem;"></td>
                        <td contenteditable="true" tabindex="0" style="min-height:2.25rem;"></td>
                        <td contenteditable="true" tabindex="0" style="min-height:2.25rem;"></td>
                        <td contenteditable="true" tabindex="0" style="min-height:2.25rem;"></td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

</div>

@endsection
