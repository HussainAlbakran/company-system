@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 1100px; margin: 20px auto;">

    @if(session('success'))
        <div style="background: #d1e7dd; color: #0f5132; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #f8d7da; color: #842029; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">ملف الموظف</h2>
        <a href="{{ route('employees.index') }}" style="background: #6c757d; color: white; padding: 10px 14px; border-radius: 6px; text-decoration: none;">
            رجوع إلى الموظفين
        </a>
    </div>

    <div style="background: #ffffff; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
        <h3 style="margin-top: 0; margin-bottom: 15px;">بيانات الموظف</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <strong>الاسم:</strong>
                <div>{{ $employee->name }}</div>
            </div>

            <div>
                <strong>البريد الإلكتروني:</strong>
                <div>{{ $employee->email }}</div>
            </div>

            <div>
                <strong>رقم الجوال:</strong>
                <div>{{ $employee->phone }}</div>
            </div>

            <div>
                <strong>المسمى الوظيفي:</strong>
                <div>{{ $employee->job_title }}</div>
            </div>

            <div>
                <strong>القسم:</strong>
                <div>{{ $employee->department->name ?? '-' }}</div>
            </div>

            <div>
                <strong>الراتب:</strong>
                <div>{{ $employee->salary }}</div>
            </div>

            <div>
                <strong>تاريخ التوظيف:</strong>
                <div>{{ $employee->hire_date }}</div>
            </div>
        </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
        <h3 style="margin-top: 0; margin-bottom: 15px;">رفع ملف PDF للموظف</h3>

        <form action="{{ route('employees.documents.store', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 6px;">عنوان الملف</label>
                    <input type="text" name="title" required
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px;">نوع الملف</label>
                    <input type="text" name="document_type" placeholder="مثال: عقد / هوية / شهادة"
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px;">اختر ملف PDF</label>
                    <input type="file" name="document" accept="application/pdf" required
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>
            </div>

            <div style="margin-top: 15px;">
                <button type="submit"
                        style="background: #0d6efd; color: #fff; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer;">
                    رفع الملف
                </button>
            </div>
        </form>
    </div>

    <div style="background: #ffffff; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
        <h3 style="margin-top: 0; margin-bottom: 15px;">ملفات الموظف</h3>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="border: 1px solid #ddd; padding: 10px;">العنوان</th>
                    <th style="border: 1px solid #ddd; padding: 10px;">النوع</th>
                    <th style="border: 1px solid #ddd; padding: 10px;">اسم الملف</th>
                    <th style="border: 1px solid #ddd; padding: 10px;">عرض</th>
                    <th style="border: 1px solid #ddd; padding: 10px;">حذف</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employee->documents as $document)
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 10px;">{{ $document->title }}</td>
                        <td style="border: 1px solid #ddd; padding: 10px;">{{ $document->document_type ?? '-' }}</td>
                        <td style="border: 1px solid #ddd; padding: 10px;">{{ $document->file_name }}</td>
                        <td style="border: 1px solid #ddd; padding: 10px;">
                            <a href="{{ asset('storage/' . $document->file_path) }}"
                               target="_blank"
                               style="background: #198754; color: white; padding: 6px 10px; border-radius: 6px; text-decoration: none;">
                                فتح PDF
                            </a>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 10px;">
                            <form action="{{ route('employees.documents.destroy', [$employee->id, $document->id]) }}"
                                  method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف الملف؟')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        style="background: #dc3545; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer;">
                                    حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="border: 1px solid #ddd; padding: 15px; text-align: center;">
                            لا توجد ملفات مرفوعة لهذا الموظف
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($employee->department && ($employee->department->name === 'الهندسة' || $employee->department->name === 'Engineering'))
        <div style="background: #fff3cd; color: #664d03; border: 1px solid #ffecb5; border-radius: 10px; padding: 20px;">
            <h3 style="margin-top: 0;">قسم الهندسة</h3>
            <p style="margin-bottom: 15px;">
                هذا الموظف يتبع قسم الهندسة. يمكنك عرض المشاريع الهندسية من الزر التالي:
            </p>

            <a href="{{ route('engineering-projects.index') }}"
               style="background: #fd7e14; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none;">
                عرض مشاريع الهندسة
            </a>
        </div>
    @endif

</div>
@endsection