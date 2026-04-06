@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">ملف الموظف</h1>
            <p style="margin:8px 0 0; color:#6b7280;">عرض كامل لبيانات الموظف والمرفقات الخاصة به</p>
        </div>

        <div class="actions">
            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">تعديل</a>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">رجوع</a>
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

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">البيانات الأساسية</h2>
        </div>

        <div class="details-grid">
            <div class="detail-box">
                <strong>الاسم</strong>
                <div>{{ $employee->name ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>الرقم الوظيفي</strong>
                <div>{{ $employee->employee_number ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>المسمى الوظيفي</strong>
                <div>{{ $employee->job_title ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>الجوال</strong>
                <div>{{ $employee->phone ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>البريد الإلكتروني</strong>
                <div>{{ $employee->email ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>القسم</strong>
                <div>{{ $employee->department->name ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>العنوان</strong>
                <div>{{ $employee->address ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>تاريخ التوظيف</strong>
                <div>{{ $employee->hire_date ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>تاريخ انتهاء الإقامة</strong>
                <div>{{ $employee->residency_expiry_date ?? '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>الراتب</strong>
                <div>{{ isset($employee->salary) ? number_format($employee->salary, 2) : '-' }}</div>
            </div>

            <div class="detail-box">
                <strong>الحالة</strong>
                <div>
                    @if(($employee->status ?? '') === 'active')
                        <span class="badge badge-green">نشط</span>
                    @elseif(($employee->status ?? '') === 'inactive')
                        <span class="badge badge-red">غير نشط</span>
                    @else
                        <span class="badge badge-gray">{{ $employee->status ?? '-' }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="actions" style="margin-top:20px;">
            @if($employee->phone)
                <a href="tel:{{ $employee->phone }}" class="btn btn-success btn-sm">اتصال</a>

                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $employee->phone) }}"
                   target="_blank"
                   class="btn btn-success btn-sm">
                    واتساب
                </a>
            @endif

            @if($employee->email)
                <a href="mailto:{{ $employee->email }}" class="btn btn-primary btn-sm">
                    إرسال بريد
                </a>
            @endif
        </div>
    </div>

    <div class="page-card" style="margin-bottom:24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">رفع ملف جديد</h2>
            <p style="margin-top:8px; color:#6b7280;">إضافة ملف جديد إلى سجل الموظف</p>
        </div>

        <form action="{{ route('employees.documents.store', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label>نوع الملف</label>
                    <select name="document_type" required>
                        <option value="offer_letter">عرض وظيفي</option>
                        <option value="contract">عقد</option>
                        <option value="cv">سيرة ذاتية</option>
                        <option value="id">هوية</option>
                        <option value="certificate">شهادة</option>
                        <option value="employee_data">بيانات موظف</option>
                        <option value="other">ملف آخر</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>عنوان الملف</label>
                    <input type="text" name="title" value="{{ old('title') }}">
                </div>

                <div class="form-group form-group-full">
                    <label>اختر الملف</label>
                    <input type="file" name="file" required>
                </div>

                <div class="form-group form-group-full">
                    <label>ملاحظات</label>
                    <textarea name="notes">{{ old('notes') }}</textarea>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">رفع الملف</button>
            </div>
        </form>
    </div>

    <div class="page-card">
        <div class="page-header">
            <h2 style="margin:0; font-size:24px;">ملفات الموظف</h2>
            <p style="margin-top:8px; color:#6b7280;">جميع الملفات المرتبطة بهذا الموظف</p>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>النوع</th>
                        <th>العنوان</th>
                        <th>اسم الملف</th>
                        <th>ملاحظات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($employee->documents as $document)
                        <tr>
                            <td>{{ $document->document_type ?? '-' }}</td>
                            <td>{{ $document->title ?? '-' }}</td>
                            <td>{{ $document->original_name ?? '-' }}</td>
                            <td>{{ $document->notes ?? '-' }}</td>
                            <td>
                                <div class="actions-row">
                                    <a href="{{ route('employees.documents.open', [$employee, $document]) }}"
                                       target="_blank"
                                       class="btn btn-blue btn-sm">
                                        فتح الملف
                                    </a>

                                    <a href="{{ route('employees.documents.download', [$employee, $document]) }}"
                                       class="btn btn-success btn-sm">
                                        تحميل
                                    </a>

                                    <form action="{{ route('employees.documents.destroy', [$employee, $document]) }}"
                                          method="POST"
                                          onsubmit="return confirm('هل تريد حذف الملف؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-row">لا توجد ملفات لهذا الموظف</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection