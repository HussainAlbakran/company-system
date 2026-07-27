@extends('layouts.app')

@section('page_title', __('users.approvals_title'))
@section('page_subtitle', __('users.approvals_subtitle'))

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="mb-1">{{ __('users.approvals_title') }}</h2>
            <p class="text-muted mb-0">{{ __('users.approvals_subtitle') }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            {{ __('users.back_to_users') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- Pending Users --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-warning-subtle border-0 rounded-top-4 py-3">
                    <h4 class="mb-0">{{ __('users.section_pending') }}</h4>
                </div>
                <div class="card-body">
                    @if($pendingUsers->count())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('users.table_name') }}</th>
                                        <th>{{ __('users.table_email') }}</th>
                                        <th>{{ __('users.table_role') }}</th>
                                        <th>{{ __('users.registered_at') }}</th>
                                        <th class="text-center">{{ __('users.table_actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingUsers as $user)
                                        <tr>
                                            <td class="fw-bold" style="color:#000000;">{{ $user->name }}</td>
                                            <td class="fw-semibold" dir="ltr" style="color:#000000; text-align:start; word-break:break-all;">{{ $user->email }}</td>
                                            <td>{{ $user->getRoleLabel() }}</td>
                                            <td>{{ $user->created_at?->format('Y-m-d h:i A') }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                    <form action="{{ route('users.approve', $user->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm px-3">
                                                            {{ __('users.approve') }}
                                                        </button>
                                                    </form>

                                                    <button class="btn btn-danger btn-sm px-3" type="button" data-bs-toggle="collapse" data-bs-target="#reject-user-{{ $user->id }}">
                                                        {{ __('users.reject') }}
                                                    </button>
                                                </div>

                                                <div class="collapse mt-3" id="reject-user-{{ $user->id }}">
                                                    <form action="{{ route('users.reject', $user->id) }}" method="POST" class="border rounded-3 p-3 bg-light">
                                                        @csrf
                                                        <label class="form-label fw-semibold">{{ __('users.rejection_reason_optional') }}</label>
                                                        <textarea name="rejection_reason" class="form-control mb-2" rows="3" placeholder="{{ __('users.rejection_placeholder') }}"></textarea>
                                                        <button type="submit" class="btn btn-danger btn-sm">{{ __('users.confirm_reject') }}</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            {{ __('users.empty_pending') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Approved Users --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-success-subtle border-0 rounded-top-4 py-3">
                    <h4 class="mb-0">{{ __('users.section_approved') }}</h4>
                </div>
                <div class="card-body">
                    @if($approvedUsers->count())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('users.table_name') }}</th>
                                        <th>{{ __('users.table_email') }}</th>
                                        <th>{{ __('users.table_role') }}</th>
                                        <th>{{ __('users.approved_at') }}</th>
                                        <th class="text-center">{{ __('users.table_actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedUsers as $user)
                                        <tr>
                                            <td class="fw-bold" style="color:#000000;">{{ $user->name }}</td>
                                            <td class="fw-semibold" dir="ltr" style="color:#000000; text-align:start; word-break:break-all;">{{ $user->email }}</td>
                                            <td>{{ $user->getRoleLabel() }}</td>
                                            <td>{{ $user->approved_at ? \Carbon\Carbon::parse($user->approved_at)->format('Y-m-d h:i A') : '-' }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                    <form action="{{ route('users.suspend', $user->id) }}" method="POST" onsubmit="return confirm(@json(__('users.confirm_suspend_user')));">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-sm px-3">
                                                            {{ __('users.suspend_user') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            {{ __('users.empty_approved') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Suspended Users --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-danger-subtle border-0 rounded-top-4 py-3">
                    <h4 class="mb-0">{{ __('users.section_suspended') }}</h4>
                </div>
                <div class="card-body">
                    @if($suspendedUsers->count())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('users.table_name') }}</th>
                                        <th>{{ __('users.table_email') }}</th>
                                        <th>{{ __('users.table_role') }}</th>
                                        <th class="text-center">{{ __('users.table_actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suspendedUsers as $user)
                                        <tr>
                                            <td class="fw-bold" style="color:#000000;">{{ $user->name }}</td>
                                            <td class="fw-semibold" dir="ltr" style="color:#000000; text-align:start; word-break:break-all;">{{ $user->email }}</td>
                                            <td>{{ $user->getRoleLabel() }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                    <form action="{{ route('users.reactivate', $user->id) }}" method="POST" onsubmit="return confirm(@json(__('users.confirm_reactivate_user')));">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-sm px-3">
                                                            {{ __('users.unsuspend_user') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            {{ __('users.empty_suspended') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Rejected Users --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-secondary-subtle border-0 rounded-top-4 py-3">
                    <h4 class="mb-0">{{ __('users.section_rejected') }}</h4>
                </div>
                <div class="card-body">
                    @if($rejectedUsers->count())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('users.table_name') }}</th>
                                        <th>{{ __('users.table_email') }}</th>
                                        <th>{{ __('users.table_role') }}</th>
                                        <th>{{ __('users.rejection_reason') }}</th>
                                        <th class="text-center">{{ __('users.table_actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedUsers as $user)
                                        <tr>
                                            <td class="fw-bold" style="color:#000000;">{{ $user->name }}</td>
                                            <td class="fw-semibold" dir="ltr" style="color:#000000; text-align:start; word-break:break-all;">{{ $user->email }}</td>
                                            <td>{{ $user->getRoleLabel() }}</td>
                                            <td>{{ $user->rejection_reason ?: '-' }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                    <form action="{{ route('users.approve', $user->id) }}" method="POST" onsubmit="return confirm(@json(__('users.confirm_approve_rejected')));">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm px-3">
                                                            {{ __('users.approve_user') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            {{ __('users.empty_rejected') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
