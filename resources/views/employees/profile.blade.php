@extends('layouts.app')

@section('page_title', __('employees.profile_title'))
@section('page_subtitle', '')

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
        <h2 style="margin: 0;">{{ __('employees.profile_title') }}</h2>
        <a href="{{ route('employees.index') }}" style="background: #6c757d; color: white; padding: 10px 14px; border-radius: 6px; text-decoration: none;">
            {{ __('employees.back_to_employees') }}
        </a>
    </div>

    <div style="background: #ffffff; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
        <h3 style="margin-top: 0; margin-bottom: 15px;">{{ __('employees.employee_data') }}</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div>
                <strong>{{ __('employees.label_name') }}:</strong>
                <div>{{ $employee->name }}</div>
            </div>

            <div>
                <strong>{{ __('employees.label_email') }}:</strong>
                <div>{{ $employee->email }}</div>
            </div>

            <div>
                <strong>{{ __('employees.label_phone') }}:</strong>
                <div>{{ $employee->phone }}</div>
            </div>

            <div>
                <strong>{{ __('employees.label_job_title') }}:</strong>
                <div>{{ $employee->job_title }}</div>
            </div>

            <div>
                <strong>{{ __('employees.label_department') }}:</strong>
                <div>{{ $employee->department->name ?? '-' }}</div>
            </div>

            <div>
                <strong>{{ __('employees.salary') }}:</strong>
                <div>{{ $employee->salary }}</div>
            </div>

            <div>
                <strong>{{ __('employees.hire_date') }}:</strong>
                <div>{{ $employee->hire_date }}</div>
            </div>
        </div>
    </div>

    <div style="background: #ffffff; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
        <h3 style="margin-top: 0; margin-bottom: 15px;">{{ __('employees.upload_pdf_section') }}</h3>

        <form action="{{ route('employees.documents.store', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 6px;">{{ __('employees.doc_title') }}</label>
                    <input type="text" name="title" required
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px;">{{ __('employees.doc_type') }}</label>
                    <input type="text" name="document_type" placeholder="{{ __('employees.doc_type_placeholder') }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 6px;">{{ __('employees.choose_pdf') }}</label>
                    <input type="file" name="document" accept="application/pdf" required
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                </div>
            </div>

            <div style="margin-top: 15px;">
                <button type="submit"
                        style="background: #0d6efd; color: #fff; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer;">
                    {{ __('employees.upload') }}
                </button>
            </div>
        </form>
    </div>

    <div style="background: #ffffff; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin-bottom: 25px;">
        <h3 style="margin-top: 0; margin-bottom: 15px;">{{ __('employees.employee_files') }}</h3>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="border: 1px solid #ddd; padding: 10px;">{{ __('employees.tbl_title') }}</th>
                    <th style="border: 1px solid #ddd; padding: 10px;">{{ __('employees.tbl_type') }}</th>
                    <th style="border: 1px solid #ddd; padding: 10px;">{{ __('employees.tbl_file_name') }}</th>
                    <th style="border: 1px solid #ddd; padding: 10px;">{{ __('employees.tbl_view') }}</th>
                    <th style="border: 1px solid #ddd; padding: 10px;">{{ __('employees.tbl_delete') }}</th>
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
                                {{ __('employees.open_pdf') }}
                            </a>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 10px;">
                            <form action="{{ route('employees.documents.destroy', [$employee->id, $document->id]) }}"
                                  method="POST"
                                  onsubmit="return confirm(@json(__('employees.confirm_delete_file')))">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        style="background: #dc3545; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer;">
                                    {{ __('employees.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="border: 1px solid #ddd; padding: 15px; text-align: center;">
                            {{ __('employees.no_files') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($employee->department && ($employee->department->name === 'الهندسة' || $employee->department->name === 'Engineering'))
        <div style="background: #fff3cd; color: #664d03; border: 1px solid #ffecb5; border-radius: 10px; padding: 20px;">
            <h3 style="margin-top: 0;">{{ __('employees.engineering_section_title') }}</h3>
            <p style="margin-bottom: 15px;">
                {{ __('employees.engineering_notice') }}
            </p>

            <a href="{{ route('engineering-projects.index') }}"
               style="background: #fd7e14; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none;">
                {{ __('employees.view_engineering_projects') }}
            </a>
        </div>
    @endif

</div>
@endsection
