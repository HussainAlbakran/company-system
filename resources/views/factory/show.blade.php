@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">تفاصيل أمر الإنتاج</h1>
            <p style="margin:8px 0 0; color:#6b7280;">
                متابعة تفاصيل الأمر، تسجيل الإنتاج، وتسجيل التوريد
            </p>
        </div>

        <div class="actions">
            <a href="{{ route('factory.index') }}" class="btn btn-secondary">رجوع</a>
            <a href="{{ route('factory.edit', $order->id) }}" class="btn btn-warning">تعديل الأمر</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
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

    {{-- بيانات الأمر --}}
    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">بيانات أمر الإنتاج</h2>
        </div>

        <div class="details-grid">
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
                <strong>نسبة الإنتاج</strong>
                <div>
                    <span class="badge badge-green">
                        {{ $order->production_percentage ?? 0 }}%
                    </span>
                </div>
            </div>

            <div class="detail-box">
                <strong>نسبة التوريد</strong>
                <div>
                    <span class="badge badge-orange">
                        {{ $order->supply_percentage ?? 0 }}%
                    </span>
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
    </div>

    {{-- النماذج --}}
    <div class="form-grid" style="margin-bottom:24px;">

        {{-- تسجيل إنتاج --}}
        <div class="page-card">
            <div class="page-header">
                <h2 style="margin:0; font-size:22px;">تسجيل إنتاج</h2>
                <p style="margin-top:8px; color:#6b7280;">إضافة كمية إنتاج جديدة لهذا الأمر</p>
            </div>

            <form action="{{ route('factory.entries.store', $order->id) }}" method="POST">
                @csrf

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
        </div>

        {{-- تسجيل توريد --}}
        <div class="page-card">
            <div class="page-header">
                <h2 style="margin:0; font-size:22px;">تسجيل توريد</h2>
                <p style="margin-top:8px; color:#6b7280;">إضافة كمية توريد جديدة لهذا الأمر</p>
            </div>

            <form action="{{ route('factory.supplies.store', $order->id) }}" method="POST">
                @csrf

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
        </div>

    </div>

    {{-- سجلات الإنتاج --}}
    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">سجلات الإنتاج</h2>
        </div>

        <div class="table-wrap">
            <table>
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
            </table>
        </div>
    </div>

    {{-- سجلات التوريد --}}
    <div class="page-card">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">سجلات التوريد</h2>
        </div>

        <div class="table-wrap">
            <table>
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
            </table>
        </div>
    </div>

</div>
@endsection