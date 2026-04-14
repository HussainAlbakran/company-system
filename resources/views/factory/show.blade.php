@extends('layouts.app')

@section('page_title', 'Factory Order Details')
@section('page_subtitle', 'Production execution and progress')

@section('content')
<x-ui.card title="Production Order Details" subtitle="متابعة تفاصيل الأمر، المقاسات، تسجيل الإنتاج، وتسجيل التوريد">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('factory.index') }}" class="btn btn-secondary">رجوع</a>
        <a href="{{ route('production-orders.edit', $order->id) }}" class="btn btn-warning">تعديل الأمر</a>
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

<x-ui.card title="بيانات المشروع وأمر الإنتاج" subtitle="Enterprise snapshot">
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
                <div>{{ optional($project)->main_contractor ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>اسم المنتج</strong>
                <div>{{ $order->product_name ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>رقم الأمر</strong>
                <div>{{ $order->order_number ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>الكمية المطلوبة</strong>
                <div>{{ $order->planned_quantity ?? 0 }}</div>
            </div>

            <div class="detail-box">
                <strong>الكمية المنتجة</strong>
                <div>{{ $order->produced_quantity ?? 0 }}</div>
            </div>

            <div class="detail-box">
                <strong>كمية التوريد</strong>
                <div>{{ $order->supplied_quantity ?? 0 }}</div>
            </div>

            <div class="detail-box">
                <strong>الكمية المتبقية</strong>
                <div>{{ $order->remaining_quantity ?? 0 }}</div>
            </div>

            <div class="detail-box">
                <strong>Production Progress</strong>
                <div>
                    <x-ui.progress :value="$order->production_percentage" />
                </div>
            </div>

            <div class="detail-box">
                <strong>Supply Progress</strong>
                <div>
                    <x-ui.progress :value="$order->supply_percentage" color="#f59e0b" />
                </div>
            </div>

            <div class="detail-box">
                <strong>الأيام المتوقعة للإكمال</strong>
                <div>{{ $order->expected_production_days ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>الأيام المتبقية حتى النهاية</strong>
                <div>
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
                </div>
            </div>

            <div class="detail-box">
                <strong>الحالة</strong>
                <div>
                    @if(($order->status ?? '') === 'completed')
                        <span class="badge badge-green">مكتمل</span>
                    @elseif(($order->status ?? '') === 'in_progress')
                        <span class="badge badge-blue">قيد التنفيذ</span>
                    @elseif(($order->status ?? '') === 'pending')
                        <span class="badge badge-gray">قيد الانتظار</span>
                    @else
                        <span class="badge badge-gray">{{ $order->status ?? '-' }}</span>
                    @endif
                </div>
            </div>
    </div>
</x-ui.card>

<x-ui.card title="بيانات المعماري" subtitle="المقاسات والمخططات القادمة من المعماري لهذا المشروع">
    <div class="details-grid">
            <div class="detail-box">
                <strong>نوع الرسم</strong>
                <div>{{ optional($architectTask)->drawing_type ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>حالة الرسم</strong>
                <div>{{ optional($architectTask)->drawing_status ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>حالة التخطيط</strong>
                <div>{{ optional($architectTask)->planning_status ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>ملف الرسم</strong>
                <div>
                    @if($architectTask && $architectTask->drawing_file)
                        <a href="{{ asset('storage/' . $architectTask->drawing_file) }}" target="_blank" class="btn btn-primary btn-sm">
                            فتح
                        </a>
                    @else
                        -
                    @endif
                </div>
            </div>

            <div class="detail-box">
                <strong>ملف التخطيط</strong>
                <div>
                    @if($architectTask && $architectTask->planning_file)
                        <a href="{{ asset('storage/' . $architectTask->planning_file) }}" target="_blank" class="btn btn-primary btn-sm">
                            فتح
                        </a>
                    @else
                        -
                    @endif
                </div>
            </div>

            <div class="detail-box detail-box-full">
                <strong>ملاحظات المعماري</strong>
                <div>{{ optional($architectTask)->notes ?? '-' }}</div>
            </div>
    </div>
</x-ui.card>

<x-ui.card title="مقاسات المشروع">
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
                            <td colspan="11" class="empty-row">لا توجد مقاسات لهذا المشروع</td>
                        </tr>
                    @endforelse
                </tbody>
    </x-ui.table>
</x-ui.card>

<div class="form-grid" style="margin-bottom:24px;">

    <x-ui.card title="تسجيل إنتاج" subtitle="إضافة كمية إنتاج جديدة لهذا الأمر">
        <form action="{{ route('production-entries.store') }}" method="POST">
                @csrf

                <input type="hidden" name="production_order_id" value="{{ $order->id }}">
                <input type="hidden" name="project_id" value="{{ $order->project_id }}">

                <div class="form-grid">
                    <div class="form-group">
                        <label>تاريخ الإنتاج</label>
                        <input type="date" name="entry_date" value="{{ old('entry_date') }}" required>
                    </div>

                    <div class="form-group">
                        <label>الكمية</label>
                        <input type="number" step="0.01" name="quantity" value="{{ old('quantity') }}" required>
                    </div>

                    <div class="form-group">
                        <label>وقت البداية</label>
                        <input type="time" name="start_time" value="{{ old('start_time') }}">
                    </div>

                    <div class="form-group">
                        <label>وقت النهاية</label>
                        <input type="time" name="end_time" value="{{ old('end_time') }}">
                    </div>

                    <div class="form-group">
                        <label>رقم الموظف</label>
                        <input type="number" name="employee_id" value="{{ old('employee_id') }}">
                    </div>

                    <div class="form-group form-group-full">
                        <label>ملاحظات</label>
                        <textarea name="notes">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">حفظ الإنتاج</button>
                </div>
        </form>
    </x-ui.card>

    <x-ui.card title="تسجيل توريد" subtitle="إضافة كمية توريد جديدة لهذا الأمر">
        <form action="{{ route('production-supplies.store') }}" method="POST">
                @csrf

                <input type="hidden" name="production_order_id" value="{{ $order->id }}">

                <div class="form-grid">
                    <div class="form-group">
                        <label>تاريخ التوريد</label>
                        <input type="date" name="supply_date" value="{{ old('supply_date') }}" required>
                    </div>

                    <div class="form-group">
                        <label>كمية التوريد</label>
                        <input type="number" step="0.01" name="quantity" value="{{ old('quantity') }}" required>
                    </div>

                    <div class="form-group">
                        <label>اسم المستلم</label>
                        <input type="text" name="receiver_name" value="{{ old('receiver_name') }}">
                    </div>

                    <div class="form-group form-group-full">
                        <label>ملاحظات</label>
                        <textarea name="notes">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">حفظ التوريد</button>
                </div>
        </form>
    </x-ui.card>
</div>

<x-ui.card title="سجلات الإنتاج">
    <x-ui.table>
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>الكمية</th>
                        <th>وقت البداية</th>
                        <th>وقت النهاية</th>
                        <th>ساعات العمل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->entries as $entry)
                        <tr>
                            <td>{{ $entry->entry_date ?? '-' }}</td>
                            <td>{{ $entry->quantity ?? 0 }}</td>
                            <td>{{ $entry->start_time ?? '-' }}</td>
                            <td>{{ $entry->end_time ?? '-' }}</td>
                            <td>{{ $entry->working_hours ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-row">لا توجد سجلات إنتاج</td>
                        </tr>
                    @endforelse
                </tbody>
    </x-ui.table>
</x-ui.card>

<x-ui.card title="سجلات التوريد">
    <x-ui.table>
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>كمية التوريد</th>
                        <th>المستلم</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->supplies as $supply)
                        <tr>
                            <td>{{ $supply->supply_date ?? '-' }}</td>
                            <td>{{ $supply->quantity ?? 0 }}</td>
                            <td>{{ $supply->receiver_name ?? '-' }}</td>
                            <td>{{ $supply->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-row">لا توجد سجلات توريد</td>
                        </tr>
                    @endforelse
                </tbody>
    </x-ui.table>
</x-ui.card>
@endsection