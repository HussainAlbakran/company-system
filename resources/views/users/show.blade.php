@extends('layouts.app')

@section('page_title', __('users.show_title'))
@section('page_subtitle', '')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('users.show_title') }}</h2>

    <div class="card">
        <div class="card-body">
            <p>
                <strong>{{ __('users.label_name') }}:</strong>
                <span style="font-weight:700; color:#000000;">{{ $user->name }}</span>
            </p>
            <p>
                <strong>{{ __('users.label_email') }}:</strong>
                <span style="font-weight:600; color:#000000; direction:ltr; display:inline-block;">{{ $user->email }}</span>
            </p>
            <p><strong>{{ __('users.label_role') }}:</strong> {{ $user->getRoleLabel() ?? '-' }}</p>
            <p><strong>{{ __('users.label_approval') }}:</strong>
                @if($user->approval_status)
                    {{ __('users.status_'.$user->approval_status) }}
                @else
                    -
                @endif
            </p>
            <p><strong>{{ __('users.label_active') }}:</strong> {{ !empty($user->is_active) ? __('users.yes') : __('users.no') }}</p>
        </div>
    </div>
</div>
@endsection
