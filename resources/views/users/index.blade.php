@extends('layouts.app')

@section('page_title', __('users.page_title'))
@section('page_subtitle', __('users.page_subtitle'))

@section('content')
<div class="dashboard-stack">
<section class="dashboard-panel">
    <div class="panel-head">
        <div>
            <h2 class="panel-title">{{ __('users.panel_title') }}</h2>
            <p class="panel-subtitle">{{ __('users.panel_subtitle') }}</p>
        </div>

        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            {{ __('users.add_user') }}
        </a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>{{ __('users.table_name') }}</th>
                    <th>{{ __('users.table_email') }}</th>
                    <th>{{ __('users.table_role') }}</th>
                    <th>{{ __('users.table_status') }}</th>
                    <th>{{ __('users.table_active') }}</th>
                    <th style="min-width:220px;">{{ __('users.table_actions') }}</th>
                </tr>
            </thead>

            <tbody>

                @forelse($users as $user)

                <tr>

                    <td style="font-weight:700; color:#000000; font-size:0.98rem;">
                        {{ $user->name }}
                    </td>

                    <td style="font-weight:600; color:#000000; font-size:0.95rem; direction:ltr; text-align:start; word-break:break-all;">
                        {{ $user->email }}
                    </td>

                    <td>
                        <span class="badge badge-blue">
                            {{ strtoupper($user->getRoleLabel()) }}
                        </span>
                    </td>

                    <td>
                        @if($user->approval_status == 'approved')
                            <span class="badge badge-green">{{ __('users.status_approved') }}</span>

                        @elseif($user->approval_status == 'pending')
                            <span class="badge badge-orange">{{ __('users.status_pending') }}</span>

                        @elseif($user->approval_status == 'rejected')
                            <span class="badge badge-gray">{{ __('users.status_rejected') }}</span>

                        @elseif($user->approval_status == 'suspended')
                            <span class="badge badge-red">{{ __('users.status_suspended') }}</span>

                        @else
                            <span class="badge badge-gray">-</span>
                        @endif
                    </td>

                    <td>
                        @if($user->is_active)
                            <span class="badge badge-green">{{ __('users.active_yes') }}</span>
                        @else
                            <span class="badge badge-red">{{ __('users.active_no') }}</span>
                        @endif
                    </td>

                    <td>
                        <div class="actions-row">

                            <a href="{{ route('users.edit', $user->id) }}"
                               class="btn btn-warning btn-sm">
                               {{ __('users.edit') }}
                            </a>

                            @if($user->is_active && auth()->id() != $user->id)
                            <form action="{{ route('users.suspend', $user->id) }}"
                                  method="POST"
                                  onsubmit="return confirm(@json(__('users.confirm_suspend')))">
                                @csrf
                                <button class="btn btn-warning btn-sm">
                                    {{ __('users.suspend') }}
                                </button>
                            </form>
                            @endif

                            @if(!$user->is_active)
                            <form action="{{ route('users.reactivate', $user->id) }}"
                                  method="POST"
                                  onsubmit="return confirm(@json(__('users.confirm_activate')))">
                                @csrf
                                <button class="btn btn-success btn-sm">
                                    {{ __('users.activate') }}
                                </button>
                            </form>
                            @endif

                            @if(auth()->id() != $user->id)
                            <form action="{{ route('users.destroy',$user->id) }}"
                                  method="POST"
                                  onsubmit="return confirm(@json(__('users.confirm_delete')))">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm">
                                    {{ __('users.delete') }}
                                </button>
                            </form>
                            @endif

                        </div>
                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="6" class="empty-row">
                        {{ __('users.no_users') }}
                    </td>
                </tr>

                @endforelse

            </tbody>
        </table>

        @if($users->hasPages())
            <div style="margin-top: 18px;">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</section>
</div>
@endsection
