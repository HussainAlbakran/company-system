@extends('layouts.app')

@section('page_title', 'Installation Details')
@section('page_subtitle', 'Read-only from production stream')

@section('content')
<x-ui.card title="التركيبات - تفاصيل المشروع" subtitle="كل ما يحتاجه قسم التركيبات لهذا المشروع في صفحة واحدة">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('installations.index') }}" class="btn btn-secondary">رجوع</a>
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

<x-ui.card title="بيانات المشروع">
    <div class="details-grid">
            <div class="detail-box">
                <strong>رقم المشروع</strong>
                <div>{{ optional($project)->project_code ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>اسم المشروع</strong>
                <div>{{ optional($project)->name ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>العميل</strong>
                <div>{{ optional($project)->client_name ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>المقاول الرئيسي</strong>
                <div>{{ $project->main_contractor ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>المطلوب</strong>
                <div>{{ number_format($project->planned_quantity ?? 0, 2) }}</div>
            </div>

            <div class="detail-box">
                <strong>تم إنتاجه</strong>
                <div>{{ number_format($project->produced_quantity ?? 0, 2) }}</div>
            </div>

            <div class="detail-box">
                <strong>المتبقي</strong>
                <div>{{ number_format($project->remaining_quantity ?? 0, 2) }}</div>
            </div>

            <div class="detail-box">
                <strong>نسبة الإنجاز</strong>
                <div><x-ui.progress :value="$project->progress_percentage ?? 0" /></div>
            </div>
    </div>
</x-ui.card>

<x-ui.card title="بيانات المعماري" subtitle="هذه البيانات للعرض فقط، ولا يمكن تعديلها من قسم التركيبات">
    <div class="details-grid">
            <div class="detail-box">
                <strong>نوع الرسم</strong>
                <div>{{ optional($architectTask)->drawing_type ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>حالة الرسم</strong>
                <div>
                    <span class="badge badge-blue">
                        {{ optional($architectTask)->drawing_status ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="detail-box">
                <strong>حالة التخطيط</strong>
                <div>
                    <span class="badge badge-blue">
                        {{ optional($architectTask)->planning_status ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="detail-box">
                <strong>ملف الرسم</strong>
                <div>
                    @if($architectTask && $architectTask->drawing_file)
                        <a href="{{ asset('storage/' . $architectTask->drawing_file) }}"
                           target="_blank"
                           class="btn btn-primary btn-sm">
                            فتح
                        </a>
                    @else
                        <span class="badge badge-gray">غير مرفوع</span>
                    @endif
                </div>
            </div>

            <div class="detail-box">
                <strong>ملف التخطيط</strong>
                <div>
                    @if($architectTask && $architectTask->planning_file)
                        <a href="{{ asset('storage/' . $architectTask->planning_file) }}"
                           target="_blank"
                           class="btn btn-primary btn-sm">
                            فتح
                        </a>
                    @else
                        <span class="badge badge-gray">غير مرفوع</span>
                    @endif
                </div>
            </div>

            <div class="detail-box detail-box-full">
                <strong>ملاحظات المعماري</strong>
                <div>{{ optional($architectTask)->notes ?? '-' }}</div>
            </div>
    </div>
</x-ui.card>

<x-ui.card title="المقاسات الخاصة بالمشروع" subtitle="المقاسات المعتمدة من قسم المعماري لهذا المشروع فقط">
    <x-ui.table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>النوع</th>
                        <th>العنصر</th>
                        <th>الطول</th>
                        <th>العرض</th>
                        <th>الارتفاع</th>
                        <th>العدد</th>
                        <th>الوحدة</th>
                        <th>المساحة</th>
                        <th>الحجم</th>
                        <th>ملاحظات</th>
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
                                لا توجد مقاسات لهذا المشروع
                            </td>
                        </tr>
                    @endforelse
                </tbody>
    </x-ui.table>
</x-ui.card>

<x-ui.card title="أوامر الإنتاج" subtitle="الكميات المعروضة هنا مقروءة فقط من أوامر الإنتاج المرتبطة بالمشروع">
    <x-ui.table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>رقم الأمر</th>
                        <th>المنتج</th>
                        <th>المطلوب</th>
                        <th>تم إنتاجه</th>
                        <th>المتبقي</th>
                        <th>الحالة</th>
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
                            <td colspan="7" class="empty-row">لا توجد أوامر إنتاج لهذا المشروع</td>
                        </tr>
                    @endforelse
                </tbody>
    </x-ui.table>
</x-ui.card>

<x-ui.card title="إنهاء المشروع">
    @if(($project->progress_percentage ?? 0) >= 100)
        <form action="{{ route('installations.complete', $project->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">إنهاء المشروع</button>
        </form>
    @else
        <span class="badge badge-gray">بانتظار اكتمال الإنتاج</span>
    @endif
</x-ui.card>

@endsection