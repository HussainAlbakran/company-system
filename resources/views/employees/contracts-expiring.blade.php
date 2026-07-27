@extends('layouts.app')

@section('page_title', __('employees.contracts_expiring_title'))
@section('page_subtitle', __('employees.contracts_expiring_subtitle', ['days' => $windowDays]))

@section('content')
<div class="dashboard-stack">
    <section class="dashboard-panel">
        <div class="panel-head" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <div>
                <h2 class="panel-title">{{ __('employees.contracts_expiring_title') }}</h2>
                <p class="panel-subtitle">
                    {{ __('employees.contracts_expiring_subtitle', ['days' => $windowDays]) }}
                </p>
            </div>

            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                <span class="badge badge-orange" style="padding:8px 12px;">
                    {{ __('employees.contracts_expiring_count', ['count' => $employees->count()]) }}
                </span>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                    {{ __('employees.back_to_employees') }}
                </a>
            </div>
        </div>

        <div class="table-wrap" style="margin-top:14px;">
            <table>
                <thead>
                    <tr>
                        <th style="width:48px;">{{ __('employees.payroll_th_number') }}</th>
                        <th>{{ __('employees.th_name') }}</th>
                        <th>{{ __('employees.contracts_expiry_date') }}</th>
                        <th>{{ __('employees.contracts_days_remaining') }}</th>
                        <th>{{ __('employees.th_department') }}</th>
                        <th>{{ __('employees.th_actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                        @php
                            $days = (int) ($employee->contract_days_remaining ?? 0);
                            $isExpired = (bool) ($employee->contract_is_expired ?? $days < 0);
                            $isUrgent = (bool) ($employee->contract_is_urgent ?? $days <= 7);
                        @endphp
                        <tr>
                            <td style="font-weight:700; color:#000;">{{ $loop->iteration }}</td>
                            <td style="font-weight:700; color:#000;">
                                <a href="{{ route('employees.show', $employee) }}" style="color:inherit;">
                                    {{ $employee->name }}
                                </a>
                            </td>
                            <td style="color:#000; font-weight:600;">
                                {{ optional($employee->contract_end_date)->format('Y-m-d') ?? $employee->contract_end_date }}
                            </td>
                            <td>
                                @if($isExpired)
                                    <span class="badge badge-red">
                                        {{ __('employees.contracts_expired_days', ['days' => abs($days)]) }}
                                    </span>
                                @elseif($isUrgent)
                                    <span class="badge badge-orange">
                                        {{ __('employees.contracts_remaining_days', ['days' => $days]) }}
                                    </span>
                                @else
                                    <span class="badge badge-blue">
                                        {{ __('employees.contracts_remaining_days', ['days' => $days]) }}
                                    </span>
                                @endif
                            </td>
                            <td style="color:#000;">{{ $employee->department->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-success btn-sm">
                                    {{ __('employees.profile_link') }}
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                                    {{ __('employees.edit') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-row">
                                {{ __('employees.contracts_expiring_empty', ['days' => $windowDays]) }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
