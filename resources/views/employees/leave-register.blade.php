@extends('layouts.app')

@section('page_title', __('employees.leave_register_title'))
@section('page_subtitle', __('employees.leave_register_subtitle'))

@section('content')
<x-ui.card :title="__('employees.leave_register_title')" :subtitle="__('employees.leave_register_subtitle')">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
        <div>
            <h1 class="page-title">{{ __('employees.leave_register_title') }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">{{ __('employees.leave_register_heading_sub') }}</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('employees.back_to_employees') }}</a>
    </div>

    <div class="page-card" style="margin-bottom:24px;">
        <form method="GET" action="{{ route('employees.leave-register') }}">
            <div class="form-grid" style="align-items:end;">
                <div class="form-group">
                    <label>{{ __('employees.search_label') }}</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('employees.leave_register_search_placeholder') }}"
                    >
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">{{ __('employees.search') }}</button>
                    <a href="{{ route('employees.leave-register') }}" class="btn btn-secondary">{{ __('employees.reset') }}</a>
                </div>
            </div>
        </form>
    </div>

    <x-ui.table>
        <thead>
            <tr>
                <th>{{ __('employees.th_name') }}</th>
                <th>{{ __('employees.th_email') }}</th>
                <th>{{ __('employees.leave_register_th_count') }}</th>
                <th>{{ __('employees.leave_balance_days') }}</th>
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
                    <td>
                        @if($employee->email && filter_var($employee->email, FILTER_VALIDATE_EMAIL))
                            <a href="mailto:{{ $employee->email }}" class="employee-link">{{ $employee->email }}</a>
                        @else
                            {{ $employee->email ?? '-' }}
                        @endif
                    </td>
                    <td>{{ $employee->leaves_count }}</td>
                    <td>{{ $employee->leave_balance ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty-row">{{ __('employees.leave_register_empty') }}</td>
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
