@extends('layouts.app')

@section('page_title', __('departments.edit_title'))
@section('page_subtitle', __('departments.edit_subtitle'))

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">{{ __('departments.edit_title') }}</h1>
            <p style="color:#6b7280; margin-top:8px;">
                {{ __('departments.edit_subtitle') }}
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

    <form action="{{ route('departments.update', $department->id) }}"
          method="POST"
          class="form-card">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>{{ __('departments.field_name') }}</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $department->name) }}"
                       required>
            </div>

            <div class="form-group">
                <label>{{ __('departments.field_manager') }}</label>
                <select name="manager_user_id">
                    <option value="">{{ __('departments.manager_not_selected') }}</option>
                    @foreach($managerUsers as $user)
                        <option value="{{ $user->id }}" {{ (string) old('manager_user_id', $department->manager_user_id) === (string) $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }}) - {{ $user->getRoleLabel() }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                {{ __('departments.update_department') }}
            </button>

            <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                {{ __('common.cancel') }}
            </a>
        </div>

    </form>

</div>

@endsection
