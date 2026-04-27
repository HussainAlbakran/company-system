@extends('layouts.app')

@section('page_title', __('departments.create_title'))
@section('page_subtitle', __('departments.create_subtitle'))

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">{{ __('departments.create_title') }}</h1>
            <p style="color:#6b7280; margin-top:8px;">
                {{ __('departments.create_subtitle') }}
            </p>
        </div>

        <a href="{{ route('departments.index') }}" class="btn btn-secondary">
            {{ __('common.back') }}
        </a>
    </div>

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-right:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('departments.store') }}" method="POST" class="form-card">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>{{ __('departments.field_name') }}</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="{{ __('departments.field_name_placeholder') }}"
                       required>
            </div>

            <div class="form-group">
                <label>{{ __('departments.field_manager') }}</label>
                <select name="manager_user_id">
                    <option value="">{{ __('departments.manager_not_selected') }}</option>
                    @foreach($managerUsers as $user)
                        <option value="{{ $user->id }}" {{ (string) old('manager_user_id') === (string) $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }}) - {{ $user->getRoleLabel() }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                {{ __('departments.save_department') }}
            </button>

            <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                {{ __('common.cancel') }}
            </a>
        </div>

    </form>

</div>

@endsection
