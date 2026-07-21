@extends('layouts.app')

@section('page_title', __('assets.with_employees_title'))
@section('page_subtitle', __('assets.with_employees_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('assets.with_employees_title') }}</h1>
            <p style="color:#6b7280;">{{ __('assets.with_employees_subtitle') }}</p>
        </div>
        <a href="{{ route('assets.index') }}" class="btn btn-secondary btn-sm">{{ __('common.back') }}</a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('assets.th_name') }}</th>
                    <th>{{ __('assets.th_serial') }}</th>
                    <th>{{ __('assets.th_employee') }}</th>
                    <th>{{ __('assets.th_start_date') }}</th>
                    <th>{{ __('assets.th_status') }}</th>
                    <th>{{ __('employees.th_action') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activeAssignments as $assignment)
                    <tr>
                        <td>{{ optional($assignment->asset)->name ?? '-' }}</td>
                        <td>{{ optional($assignment->asset)->serial_number ?? '-' }}</td>
                        <td>{{ optional($assignment->employee)->name ?? '-' }}</td>
                        <td>{{ optional($assignment->assigned_at)->format('Y-m-d H:i') ?? '-' }}</td>
                        <td><span class="badge badge-green">{{ __('assets.assignment_active') }}</span></td>
                        <td>
                            <div class="actions-row">
                                @if($assignment->asset)
                                    <a href="{{ route('assets.show', $assignment->asset) }}" class="btn btn-secondary btn-sm">{{ __('common.view') }}</a>
                                @endif
                                <form method="POST" action="{{ route('assets.assignments.return', $assignment) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">إرجاع الأصل</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">لا توجد أصول بعهدة موظفين حالياً</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:16px;">
        {{ $activeAssignments->links() }}
    </div>
</div>
@endsection
