@extends('layouts.app')

@php
    $userRoleKeys = [
        'super_admin', 'admin', 'finance', 'sales_manager', 'sales', 'engineering_manager',
        'hr', 'hr_manager', 'engineer', 'procurement_manager', 'procurement',
        'operations_manager', 'factory_manager', 'manager', 'user',
    ];
@endphp

@section('page_title', __('users.create_page_title'))
@section('page_subtitle', __('users.create_page_subtitle'))

@section('content')
<div class="dashboard-stack">
    <section class="dashboard-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">{{ __('users.create_panel_title') }}</h2>
                <p class="panel-subtitle">{{ __('users.create_panel_subtitle') }}</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">{{ __('users.back') }}</a>
        </div>

        <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="form-grid">
                    <div>
                        <label for="user_name">{{ __('users.label_name') }}</label>
                        <input
                            type="text"
                            id="user_name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="{{ __('users.placeholder_name') }}"
                            autocomplete="name"
                            required
                        >
                    </div>

                    <div>
                        <label for="user_email">{{ __('users.label_email') }}</label>
                        <input
                            type="email"
                            id="user_email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="{{ __('users.placeholder_email') }}"
                            autocomplete="email"
                            dir="ltr"
                            required
                        >
                    </div>

                    <div>
                        <label>{{ __('auth.password') }}</label>
                        <input type="password" name="password" required>
                    </div>

                    <div>
                        <label>{{ __('users.role') }}</label>
                        <select name="role" required>
                            <option value="">{{ __('users.select_role') }}</option>

                            @foreach($userRoleKeys as $roleKey)
                            <option value="{{ $roleKey }}" {{ old('role') == $roleKey ? 'selected' : '' }}>
                                {{ __('roles.'.$roleKey) }}
                            </option>
                            @endforeach

                        </select>
                    </div>
                </div>

                <div class="actions-row" style="margin-top:12px;">
                    <button type="submit" class="btn btn-primary">{{ __('users.save_user') }}</button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('common.cancel') }}</a>
                </div>
        </form>
    </section>
</div>
@endsection
