@extends('layouts.app')

@section('page_title', __('profile.page_title'))
@section('page_subtitle', __('profile.page_subtitle'))

@section('content')
<style>
    .self-profile-page .summary-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
    .self-profile-page .summary-box { min-width: 0; }
    .self-profile-page .summary-label {
        display: block;
        margin-bottom: 6px;
        color: #111827;
        font-size: 11px;
        font-weight: 700;
    }
    .self-profile-page .summary-value {
        min-width: 0;
        overflow-wrap: anywhere;
        word-break: break-word;
        color: #111827;
        line-height: 1.45;
    }
    .self-profile-page .section-title {
        margin: 0 0 12px;
        font-size: 20px;
        color: #111827;
    }
    .self-profile-page .readonly-badge {
        display: inline-block;
        margin-inline-start: 8px;
        font-size: 11px;
        color: #6b7280;
        font-weight: 600;
    }
    .self-profile-page .update-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }
    @media (max-width: 768px) {
        .self-profile-page .summary-grid,
        .self-profile-page .update-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="dashboard-stack self-profile-page">
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if(!$employee)
        <section class="dashboard-panel">
            <p class="panel-subtitle">{{ __('profile.no_employee_linked') }}</p>
        </section>
    @endif

    @if($employee)
        {{-- 1. البيانات الشخصية --}}
        <section class="dashboard-panel">
            <h2 class="section-title">{{ __('profile.section_personal') }}</h2>
            <div class="summary-grid">
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('common.name') }}</span>
                    <div class="summary-value">{{ $employee->name ?? $user->name }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('common.email') }}</span>
                    <div class="summary-value">{{ $user->email }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('employees.th_phone') }}</span>
                    <div class="summary-value">{{ $employee->phone ?? '-' }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('employees.th_department') }}</span>
                    <div class="summary-value">{{ $employee->department->name ?? '-' }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('employees.th_job_title') }}</span>
                    <div class="summary-value">{{ $employee->job_title ?? '-' }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('employees.th_employee_number') }}</span>
                    <div class="summary-value">{{ $employee->employee_number ?? '-' }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('profile.hire_date') }}</span>
                    <div class="summary-value">{{ $employee->hire_date?->format('Y-m-d') ?? '-' }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('employees.th_status') }}</span>
                    <div class="summary-value">
                        @php
                            $statusKey = match ($employee->status) {
                                'active' => 'employees.status_active',
                                'inactive' => 'employees.status_inactive',
                                'suspended' => 'employees.status_suspended',
                                default => null,
                            };
                        @endphp
                        {{ $statusKey ? __($statusKey) : ($employee->status ?? '-') }}
                    </div>
                </div>
            </div>
        </section>

        @php
            $payroll = $profileData['payroll'] ?? [];
        @endphp

        {{-- 2. البيانات المالية --}}
        <section class="dashboard-panel">
            <h2 class="section-title">
                {{ __('profile.section_financial') }}
                <span class="readonly-badge">{{ __('profile.read_only') }}</span>
            </h2>
            <p class="panel-subtitle" style="margin-bottom:12px;">
                {{ __('profile.financial_period', ['month' => $profileData['payroll_month'], 'year' => $profileData['payroll_year']]) }}
            </p>
            <div class="summary-grid">
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('profile.base_salary') }}</span>
                    <div class="summary-value">{{ number_format($payroll['base_salary'] ?? 0, 2) }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('profile.allowances') }}</span>
                    <div class="summary-value">{{ number_format($payroll['allowances_total'] ?? 0, 2) }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('profile.deductions') }}</span>
                    <div class="summary-value">{{ number_format($payroll['deductions_total'] ?? 0, 2) }}</div>
                </div>
                @if(($payroll['deductions_breakdown']['advance'] ?? 0) > 0)
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('employee_advance.planned_deduction') }}</span>
                    <div class="summary-value">{{ number_format($payroll['deductions_breakdown']['advance'], 2) }}</div>
                </div>
                @endif
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('profile.overtime_hours') }}</span>
                    <div class="summary-value">{{ number_format($payroll['overtime_hours'] ?? 0, 2) }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('profile.final_salary') }}</span>
                    <div class="summary-value"><strong>{{ number_format($payroll['final_salary'] ?? 0, 2) }}</strong></div>
                </div>
            </div>

            @php
                $openCustody = $profileData['open_custody'] ?? null;
            @endphp
            <h3 style="margin:16px 0 8px; font-size:16px;">{{ __('profile.open_custody_title') }}</h3>
            @if($openCustody)
                <div class="detail-box" style="margin-bottom:12px; padding:12px; border:1px solid #e5e7eb; border-radius:8px;">
                    <div style="display:grid; gap:6px; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); margin-bottom:10px;">
                        <div><strong>{{ __('financial_custody.issued_at') }}:</strong> {{ $openCustody->issued_at?->format('Y-m-d') ?? '-' }}</div>
                        <div><strong>{{ __('financial_custody.amount_issued') }}:</strong> {{ number_format((float) $openCustody->amount_issued, 2) }}</div>
                        @if((float) $openCustody->carried_over_amount > 0)
                        <div><strong>{{ __('financial_custody.carried_over_amount') }}:</strong> {{ number_format((float) $openCustody->carried_over_amount, 2) }}</div>
                        <div><strong>{{ __('financial_custody.new_cash_amount') }}:</strong> {{ number_format($openCustody->newCashAmount(), 2) }}</div>
                        @endif
                        <div><strong>{{ __('financial_custody.total_spent') }}:</strong> {{ number_format($openCustody->totalSpent(), 2) }}</div>
                        <div><strong>{{ __('financial_custody.amount_remaining') }}:</strong> {{ number_format((float) $openCustody->amount_remaining, 2) }}</div>
                        <div><strong>{{ __('financial_custody.status') }}:</strong> <span class="badge badge-orange">{{ __('financial_custody.status_open') }}</span></div>
                    </div>
                    @if($openCustody->transactions->whereIn('action', ['partial_settlement', 'full_settlement', 'return_remaining', 'carryover_in'])->isNotEmpty())
                        <strong>{{ __('financial_custody.transactions_title') }}:</strong>
                        <ul style="margin:8px 0 0; padding-right:18px;">
                            @foreach($openCustody->transactions->whereIn('action', ['partial_settlement', 'full_settlement', 'return_remaining', 'carryover_in']) as $tx)
                                <li style="margin-bottom:6px;">
                                    @if($tx->action === 'return_remaining')
                                        {{ __('financial_custody.action_return') }}: {{ number_format((float) $tx->amount_settled, 2) }}
                                    @elseif($tx->action === 'carryover_in')
                                        {{ __('financial_custody.action_carryover_in') }}: {{ number_format((float) $tx->amount_settled, 2) }}
                                    @else
                                        {{ __('financial_custody.th_settled') }}: {{ number_format((float) $tx->amount_settled, 2) }}
                                        @if($tx->purchase_description)
                                            — {{ $tx->purchase_description }}
                                        @endif
                                    @endif
                                    @if($tx->notes)
                                        <span style="color:#6b7280;">({{ $tx->notes }})</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @else
                <p class="panel-subtitle">{{ __('profile.no_open_custody') }}</p>
            @endif

            @if(($profileData['advances'] ?? collect())->isNotEmpty())
            <h3 style="margin:16px 0 8px; font-size:16px;">{{ __('profile.advances_title') }}</h3>
            @foreach($profileData['advances'] as $advance)
                <div class="detail-box" style="margin-bottom:12px; padding:12px; border:1px solid #e5e7eb; border-radius:8px;">
                    <div><strong>{{ __('employee_advance.total_amount') }}:</strong> {{ number_format((float)$advance->total_amount, 2) }}</div>
                    <div><strong>{{ __('employee_advance.issued_at') }}:</strong> {{ $advance->issued_at?->format('Y-m-d') ?? '-' }}</div>
                    <div><strong>{{ __('employee_advance.repayment_start') }}:</strong> {{ $advance->repaymentStartLabel() }}</div>
                    <div><strong>{{ __('employee_advance.installment_count') }}:</strong> {{ $advance->installments_paid }} / {{ $advance->installment_count }}</div>
                    <div><strong>{{ __('employee_advance.installment_amount') }}:</strong> {{ number_format((float)$advance->installment_amount, 2) }} / {{ __('profile.monthly_from_payroll') }}</div>
                    @if((float)($advance->base_salary_at_issue ?? 0) > 0)
                    <div><strong>{{ __('profile.base_salary') }}:</strong> {{ number_format((float)$advance->base_salary_at_issue, 2) }}</div>
                    @endif
                    <div><strong>{{ __('employee_advance.remaining_balance') }}:</strong> {{ number_format($advance->remainingBalance(), 2) }}</div>
                    <div><strong>{{ __('employee_advance.status') }}:</strong> {{ $advance->isActive() ? __('employee_advance.status_active') : __('employee_advance.status_completed') }}</div>
                    @if($advance->payments->isNotEmpty())
                        <div style="margin-top:10px;">
                            <strong>{{ __('employee_advance.payments_title') }}:</strong>
                            <ul style="margin:6px 0 0; padding-right:18px;">
                                @foreach($advance->payments as $payment)
                                    <li>
                                        {{ number_format((float) $payment->amount, 2) }}
                                        —
                                        @if($payment->payrollRegister)
                                            {{ __('employee_advance.payroll_register_link', ['period' => $payment->payrollRegister->periodLabel()]) }}
                                        @else
                                            {{ $payment->month }}/{{ $payment->year }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
            @endif
        </section>

        {{-- 3. الإجازات --}}
        <section class="dashboard-panel">
            <h2 class="section-title">{{ __('profile.section_leaves') }}</h2>
            <div class="summary-grid" style="margin-bottom:16px;">
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('profile.leave_balance') }}</span>
                    <div class="summary-value">{{ (int) ($employee->leave_balance ?? 0) }}</div>
                </div>
                @if(auth()->user()->canAccessLeaveRequestNavigation())
                <div class="detail-box summary-box" style="display:flex; align-items:end;">
                    <a href="{{ route('leaves.create') }}" class="btn btn-primary btn-sm">{{ __('profile.leave_request_btn') }}</a>
                </div>
                @endif
            </div>

            @if(($profileData['recent_leaves'] ?? collect())->isNotEmpty())
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('profile.leave_from') }}</th>
                                <th>{{ __('profile.leave_to') }}</th>
                                <th>{{ __('profile.leave_days') }}</th>
                                <th>{{ __('profile.leave_status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($profileData['recent_leaves'] as $leave)
                                <tr>
                                    <td>{{ $leave->start_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td>{{ $leave->end_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td>{{ (int) $leave->days }}</td>
                                    <td>
                                        @php
                                            $leaveStatus = match ($leave->status) {
                                                'approved' => 'leaves.status_approved',
                                                'rejected' => 'leaves.status_rejected',
                                                'pending' => 'leaves.status_pending',
                                                default => null,
                                            };
                                        @endphp
                                        {{ $leaveStatus ? __($leaveStatus) : $leave->status }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="panel-subtitle">{{ __('profile.no_leaves') }}</p>
            @endif
        </section>

        {{-- 4. العقد --}}
        <section class="dashboard-panel">
            <h2 class="section-title">
                {{ __('profile.section_contract') }}
                <span class="readonly-badge">{{ __('profile.read_only') }}</span>
            </h2>
            <div class="summary-grid">
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('profile.contract_start') }}</span>
                    <div class="summary-value">{{ $employee->contract_start_date?->format('Y-m-d') ?? '-' }}</div>
                </div>
                <div class="detail-box summary-box">
                    <span class="summary-label">{{ __('profile.contract_end') }}</span>
                    <div class="summary-value">{{ $employee->contract_end_date?->format('Y-m-d') ?? '-' }}</div>
                </div>
            </div>
        </section>
    @endif

    {{-- تحديث البيانات --}}
    <section class="dashboard-panel">
        <h2 class="section-title">{{ __('profile.section_update') }}</h2>
        <p class="panel-subtitle" style="margin-bottom:14px;">{{ __('profile.update_hint') }}</p>

        <form method="post" action="{{ route('profile.update') }}" class="update-grid">
            @csrf
            @method('patch')

            <div class="form-group">
                <label for="name">{{ __('common.name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                @error('name')<div class="text-danger" style="font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="email">{{ __('common.email') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                @error('email')<div class="text-danger" style="font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="phone">{{ __('employees.th_phone') }}</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $employee?->phone) }}" autocomplete="tel" @disabled(!$employee)>
                @error('phone')<div class="text-danger" style="font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="current_password">{{ __('profile.current_password') }}</label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password">
                @error('current_password')<div class="text-danger" style="font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password">{{ __('profile.new_password') }}</label>
                <input type="password" id="password" name="password" autocomplete="new-password">
                @error('password')<div class="text-danger" style="font-size:12px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ __('profile.password_confirm') }}</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
            </div>

            <div class="form-group" style="grid-column:1/-1; margin-top:8px;">
                <button type="submit" class="btn btn-primary">{{ __('profile.update_btn') }}</button>
            </div>
        </form>
    </section>

    @if($employee && ($profileData['custody_history'] ?? collect())->isNotEmpty())
        <section class="dashboard-panel">
            <h2 class="section-title">{{ __('profile.custody_history_title') }}</h2>
            <p class="panel-subtitle" style="margin-bottom:12px;">{{ __('profile.custody_history_hint') }}</p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('profile.custody_th_id') }}</th>
                            <th>{{ __('profile.custody_th_issued_at') }}</th>
                            <th>{{ __('financial_custody.amount_issued') }}</th>
                            <th>{{ __('profile.custody_th_carried_over') }}</th>
                            <th>{{ __('profile.custody_th_new_cash') }}</th>
                            <th>{{ __('financial_custody.total_spent') }}</th>
                            <th>{{ __('financial_custody.amount_remaining') }}</th>
                            <th>{{ __('financial_custody.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($profileData['custody_history'] as $historyCustody)
                            <tr>
                                <td>{{ $historyCustody->id }}</td>
                                <td>{{ $historyCustody->issued_at?->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ number_format((float) $historyCustody->amount_issued, 2) }}</td>
                                <td>{{ number_format((float) $historyCustody->carried_over_amount, 2) }}</td>
                                <td>{{ number_format($historyCustody->newCashAmount(), 2) }}</td>
                                <td>{{ number_format($historyCustody->totalSpent(), 2) }}</td>
                                <td>{{ number_format((float) $historyCustody->amount_remaining, 2) }}</td>
                                <td>
                                    @if($historyCustody->isOpen())
                                        <span class="badge badge-orange">{{ __('financial_custody.status_open') }}</span>
                                    @else
                                        <span class="badge badge-gray">{{ __('financial_custody.status_closed') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    @if($canDeleteAccount)
        <section class="dashboard-panel">
            @include('profile.partials.delete-user-form')
        </section>
    @endif
</div>
@endsection
