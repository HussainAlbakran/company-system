@extends('layouts.app')

@php
    $userRoleKeys = [
        'super_admin', 'admin', 'sales_manager', 'sales', 'engineering_manager',
        'hr', 'hr_manager', 'engineer', 'procurement_manager', 'procurement',
        'operations_manager', 'factory_manager', 'manager', 'user',
    ];
@endphp

@section('page_title', __('users.edit_title'))
@section('page_subtitle', '')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ __('users.edit_title') }}</h2>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('users.back') }}</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('common.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('common.email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('users.role') }}</label>
                        @if($canEditRole ?? false)
                            <select name="role" class="form-select" required>
                                @foreach($userRoleKeys as $roleKey)
                                <option value="{{ $roleKey }}" {{ old('role', $user->role) == $roleKey ? 'selected' : '' }}>{{ __('roles.'.$roleKey) }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ $user->getRoleLabel() }}" disabled>
                        @endif
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('users.new_password_optional') }}</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{ __('users.update_user') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
