@extends('layouts.app')

@section('page_title', __('employees.payroll_registers_title'))
@section('page_subtitle', __('employees.payroll_registers_subtitle'))

@section('content')
<div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
    <div>
        <h1 class="page-title">{{ __('employees.payroll_registers_title') }}</h1>
        <p style="margin:8px 0 0; color:#6b7280;">{{ __('employees.payroll_registers_subtitle') }}</p>
    </div>
    @php
        $hasPendingRegister = $registers->contains(fn ($r) => $r->isPending());
    @endphp
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('employees.payroll-register') }}" class="btn btn-primary">{{ __('employees.payroll_current_btn') }}</a>
        @unless($hasPendingRegister)
            <form method="POST" action="{{ route('employees.payroll-registers.create') }}">
                @csrf
                <button type="submit" class="btn btn-success">{{ __('employees.payroll_new_btn') }}</button>
            </form>
        @endunless
    </div>
</div>

@if(session('success'))
    <div class="alert-success" style="margin-bottom:12px;">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert-danger" style="margin-bottom:12px;">{{ session('error') }}</div>
@endif

<div class="page-card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('employees.payroll_th_period') }}</th>
                    <th>{{ __('employees.payroll_th_status') }}</th>
                    <th>{{ __('employees.payroll_th_approved_at') }}</th>
                    <th>{{ __('employees.payroll_th_approved_by') }}</th>
                    <th>{{ __('employees.payroll_th_actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registers as $register)
                    <tr>
                        <td>{{ $register->id }}</td>
                        <td>{{ $register->month }}/{{ $register->year }}</td>
                        <td>
                            @if($register->isApproved())
                                <span class="badge badge-green">{{ __('employees.payroll_status_approved') }}</span>
                            @else
                                <span class="badge badge-orange">{{ __('employees.payroll_status_pending') }}</span>
                            @endif
                        </td>
                        <td>{{ $register->approved_at?->format('Y-m-d H:i') ?? '-' }}</td>
                        <td>{{ $register->approver->name ?? '-' }}</td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                <a href="{{ route('employees.payroll-register.show', $register) }}" class="btn btn-secondary btn-sm">
                                    {{ __('employees.payroll_view_btn') }}
                                </a>
                                <a href="{{ route('employees.payroll-register.show', $register) }}" target="_blank" class="btn btn-primary btn-sm">
                                    {{ __('employees.payroll_print_btn') }}
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-row">{{ __('employees.payroll_registers_empty') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
