@extends('layouts.app')

@section('page_title', __('leaves.index_title'))
@section('page_subtitle', __('leaves.index_subtitle'))

@section('content')

<div class="page-card">

    <div class="page-header">
        <h2>{{ __('leaves.index_title') }}</h2>
        <p>{{ __('leaves.index_subtitle') }}</p>
    </div>

    @if(session('success'))
        <div class="alert-success" style="margin-bottom:16px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger" style="margin-bottom:16px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('leaves.th_number') }}</th>
                    <th>{{ __('leaves.th_employee') }}</th>
                    <th>{{ __('leaves.th_start') }}</th>
                    <th>{{ __('leaves.th_end') }}</th>
                    <th>{{ __('leaves.th_days') }}</th>
                    <th>{{ __('leaves.th_balance') }}</th>
                    <th>{{ __('leaves.th_status') }}</th>
                    <th>{{ __('leaves.th_reason') }}</th>
                    <th>{{ __('leaves.th_action') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($leaves as $leave)
                    <tr>
                        <td>{{ $leave->id }}</td>

                        <td>
                            {{ $leave->employee->name ?? '-' }}
                        </td>

                        <td>{{ $leave->start_date }}</td>

                        <td>{{ $leave->end_date }}</td>

                        <td>{{ $leave->days }}</td>

                        <td>
                            {{ $leave->employee->leave_balance ?? 0 }}
                        </td>

                        <td>
                            @if($leave->status === 'approved')
                                <span class="badge badge-green">{{ __('leaves.status_approved') }}</span>
                            @elseif($leave->status === 'rejected')
                                <span class="badge badge-red">{{ __('leaves.status_rejected') }}</span>
                            @else
                                <span class="badge badge-orange">{{ __('leaves.status_pending') }}</span>
                            @endif
                        </td>

                        <td>{{ $leave->reason ?? '-' }}</td>

                        <td>
                            @if($leave->status === 'pending')
                                <div class="actions-row">
                                    <form action="{{ route('leaves.approve', $leave->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            {{ __('leaves.approve') }}
                                        </button>
                                    </form>

                                    <form action="{{ route('leaves.reject', $leave->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            {{ __('leaves.reject') }}
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="badge badge-gray">{{ __('leaves.status_processed') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-row">
                            {{ __('leaves.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
