@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">المستودع</h1>
            <p style="margin:8px 0 0; color:#6b7280;">
                الأقسام الرئيسية للمواد داخل المستودع
            </p>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>القسم</th>
                    <th>إدخال</th>
                    <th>عرض</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>ديزل</td>
                    <td>
                        <a href="{{ route('warehouse.section.input', 'diesel') }}" class="btn btn-primary btn-sm">
                            إدخال
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('warehouse.section.show', 'diesel') }}" class="btn btn-secondary btn-sm">
                            عرض
                        </a>
                    </td>
                </tr>

                <tr>
                    <td>زيوت</td>
                    <td>
                        <a href="{{ route('warehouse.section.input', 'oils') }}" class="btn btn-primary btn-sm">
                            إدخال
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('warehouse.section.show', 'oils') }}" class="btn btn-secondary btn-sm">
                            عرض
                        </a>
                    </td>
                </tr>

                <tr>
                    <td>أخشاب</td>
                    <td>
                        <a href="{{ route('warehouse.section.input', 'wood') }}" class="btn btn-primary btn-sm">
                            إدخال
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('warehouse.section.show', 'wood') }}" class="btn btn-secondary btn-sm">
                            عرض
                        </a>
                    </td>
                </tr>

                <tr>
                    <td>مواد خرسانة</td>
                    <td>
                        <a href="{{ route('warehouse.section.input', 'concrete-materials') }}" class="btn btn-primary btn-sm">
                            إدخال
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('warehouse.section.show', 'concrete-materials') }}" class="btn btn-secondary btn-sm">
                            عرض
                        </a>
                    </td>
                </tr>

                <tr>
                    <td>كيمكال خرسانة</td>
                    <td>
                        <a href="{{ route('warehouse.section.input', 'concrete-chemicals') }}" class="btn btn-primary btn-sm">
                            إدخال
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('warehouse.section.show', 'concrete-chemicals') }}" class="btn btn-secondary btn-sm">
                            عرض
                        </a>
                    </td>
                </tr>

                <tr>
                    <td>مواد تشغيلية متنوعة</td>
                    <td>
                        <a href="{{ route('warehouse.section.input', 'operational-materials') }}" class="btn btn-primary btn-sm">
                            إدخال
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('warehouse.section.show', 'operational-materials') }}" class="btn btn-secondary btn-sm">
                            عرض
                        </a>
                    </td>
                </tr>

                <tr>
                    <td>حديد تسليح</td>
                    <td>
                        <a href="{{ route('warehouse.section.input', 'rebar') }}" class="btn btn-primary btn-sm">
                            إدخال
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('warehouse.section.show', 'rebar') }}" class="btn btn-secondary btn-sm">
                            عرض
                        </a>
                    </td>
                </tr>

                <tr>
                    <td>استرندات</td>
                    <td>
                        <a href="{{ route('warehouse.section.input', 'strands') }}" class="btn btn-primary btn-sm">
                            إدخال
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('warehouse.section.show', 'strands') }}" class="btn btn-secondary btn-sm">
                            عرض
                        </a>
                    </td>
                </tr>

                {{-- 🔥 الجديد --}}
                <tr>
                    <td>مواد إضافية</td>
                    <td>
                        <a href="{{ route('warehouse.section.input', 'extra-materials') }}" class="btn btn-primary btn-sm">
                            إدخال
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('warehouse.section.show', 'extra-materials') }}" class="btn btn-secondary btn-sm">
                            عرض
                        </a>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

</div>

@endsection