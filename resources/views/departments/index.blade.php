@extends('layouts.app')

@section('page_title', __('departments.page_title'))
@section('page_subtitle', __('departments.page_subtitle'))

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">{{ __('departments.page_title') }}</h1>
            <p style="color:#6b7280; margin-top:8px;">
                {{ __('departments.page_subtitle') }}
            </p>
        </div>

        <a href="{{ route('departments.create') }}" class="btn btn-primary">
            + {{ __('departments.add_department') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-wrap">

        <table>
            <thead>
                <tr>
                    <th>{{ __('departments.th_number') }}</th>
                    <th>{{ __('departments.th_name') }}</th>
                    <th>{{ __('departments.th_manager') }}</th>
                    <th style="width:180px;">{{ __('common.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse($departments as $department)
                    <tr>
                        <td>{{ $department->id }}</td>

                        <td>
                            <strong>{{ $department->name }}</strong>
                        </td>

                        <td>
                            @if($department->managerUser && $department->managerUser->email)
                                {{ $department->managerUser->name }}<br>
                                <small style="color:#94a3b8;">{{ $department->managerUser->email }}</small>
                            @else
                                <span class="badge badge-gray">{{ __('departments.manager_unset') }}</span>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex; gap:8px;">

                                <a href="{{ route('departments.edit', $department->id) }}"
                                   class="btn btn-warning btn-sm">
                                    {{ __('common.edit') }}
                                </a>

                                <form action="{{ route('departments.destroy', $department->id) }}"
                                      method="POST"
                                      onsubmit="return confirm(@json(__('departments.confirm_delete')))">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm">
                                        {{ __('common.delete') }}
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="4" class="empty-row">
                            {{ __('departments.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</div>

@endsection
