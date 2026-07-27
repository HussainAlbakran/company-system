@extends('layouts.app')

@section('page_title', __('employees.page_title'))
@section('page_subtitle', __('employees.page_subtitle'))

@section('content')
<x-ui.card :title="__('employees.card_title')" :subtitle="__('employees.card_subtitle')">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('employees.heading_title') }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">{{ __('employees.heading_subtitle') }}</p>
        </div>

        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <a href="{{ route('employees.residency-expiring') }}" class="btn btn-danger">
                {{ __('employees.residency_expiring_btn') }}
            </a>
            <a href="{{ route('employees.leave-register') }}" class="btn btn-secondary">
                {{ __('employees.leave_register_btn') }}
            </a>
            <a href="{{ route('employees.payroll-register') }}" class="btn btn-secondary">
                {{ __('employees.payroll_register_btn') }}
            </a>
            <a href="{{ route('employees.payroll-registers.index') }}" class="btn btn-secondary">
                {{ __('employees.payroll_registers_btn') }}
            </a>
            <a href="{{ route('employees.create') }}" class="btn btn-success">
                ➕ {{ __('employees.add_employee') }}
            </a>
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
        <form method="GET" action="{{ route('employees.index') }}">
            <div class="form-grid" style="align-items:end;">
                <div class="form-group">
                    <label>{{ __('employees.search_label') }}</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('employees.search_placeholder') }}"
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">{{ __('employees.search') }}</button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('employees.reset') }}</a>
                </div>
            </div>
        </form>
    </div>

    <x-ui.table>
            <thead>
                <tr>
                    <th>{{ __('employees.th_name') }}</th>
                    <th>{{ __('employees.th_employee_number') }}</th>
                    <th>{{ __('employees.th_job_title') }}</th>
                    <th>{{ __('employees.th_phone') }}</th>
                    <th>{{ __('employees.th_email') }}</th>
                    <th>{{ __('employees.th_department') }}</th>
                    <th>{{ __('employees.th_residency_expiry') }}</th>
                    <th>{{ __('employees.th_passport') }}</th>
                    <th>{{ __('employees.th_passport_expiry') }}</th>
                    <th>{{ __('employees.th_custody') }}</th>
                    <th>{{ __('employees.th_status') }}</th>
                    <th>{{ __('employees.th_actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>
                            <a href="{{ route('employees.show', $employee) }}" class="employee-link">
                                {{ $employee->name }}
                            </a>
                        </td>

                        <td>{{ $employee->employee_number ?? '-' }}</td>
                        <td>{{ $employee->job_title ?? '-' }}</td>
                        <td>{{ $employee->phone ?? '-' }}</td>
                        <td>{{ $employee->email ?? '-' }}</td>
                        <td>{{ $employee->department->name ?? '-' }}</td>
                        <td>{{ $employee->residency_expiry_date ?? '-' }}</td>
                        <td>{{ $employee->passport_number ?? '-' }}</td>
                        <td>{{ $employee->passport_expiry_date ?? '-' }}</td>
                        <td>
                            @if($employee->has_custody)
                                <span class="badge badge-green">{{ __('employees.custody_exists') }}</span>
                            @else
                                <span class="badge badge-gray">{{ __('employees.custody_none') }}</span>
                            @endif
                        </td>
                        <td>
                            @if(($employee->status ?? '') === 'active')
                                <span class="badge badge-green">{{ __('employees.status_active') }}</span>
                            @elseif(($employee->status ?? '') === 'inactive')
                                <span class="badge badge-red">{{ __('employees.status_inactive') }}</span>
                            @elseif(($employee->status ?? '') === 'suspended')
                                <span class="badge badge-gray">{{ __('employees.status_suspended') }}</span>
                            @else
                                <span class="badge badge-gray">{{ $employee->status ?? '-' }}</span>
                            @endif
                        </td>

                        <td>
                            <div class="actions-row">
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-success btn-sm">
                                    {{ __('employees.profile_link') }}
                                </a>

                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                                    {{ __('employees.edit') }}
                                </a>

                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm(@json(__('employees.confirm_delete_employee')))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        {{ __('employees.delete') }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="empty-row">{{ __('employees.empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
    </x-ui.table>

    @if(method_exists($employees, 'links'))
        <div style="margin-top:20px;">
            {{ $employees->links() }}
        </div>
    @endif

</x-ui.card>
@endsection
