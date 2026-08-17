@extends('layouts.app')

@section('page_title', __('audit.page_title'))
@section('page_subtitle', __('audit.page_subtitle'))

@php
    use App\Helpers\AuditLabelHelper;

    $actionBadgeClass = static function (?string $action): string {
        return match ($action) {
            'delete', 'unauthorized_access' => 'badge-red',
            'role_changed', 'suspend', 'reject' => 'badge-orange',
            'login', 'create', 'approve', 'file_uploaded', 'password_changed', 'reactivate' => 'badge-green',
            'logout', 'update', 'read', 'ai_request', 'ai_response', 'ai_opened' => 'badge-blue',
            default => 'badge-gray',
        };
    };
@endphp

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('audit.page_title') }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">
                {{ __('audit.page_subtitle') }}
            </p>
            <p style="margin:6px 0 0; color:#6b7280; font-size:13px;">
                {{ __('audit.retention_notice') }}
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            {{ __('audit.back_to_dashboard') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom:15px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="page-card" style="margin-bottom: 24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">{{ __('audit.filter_title') }}</h2>
        </div>

        <form method="GET" action="{{ route('audit.index') }}">
            <div class="form-grid">

                <div class="form-group">
                    <label>{{ __('audit.filter_user') }}</label>
                    <select name="user_id">
                        <option value="">{{ __('audit.filter_user_all') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('audit.filter_action') }}</label>
                    <select name="action">
                        <option value="">{{ __('audit.filter_action_all') }}</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ AuditLabelHelper::actionLabel($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('audit.filter_model') }}</label>
                    <select name="model">
                        <option value="">{{ __('audit.filter_model_all') }}</option>
                        @foreach($models as $model)
                            <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>
                                {{ AuditLabelHelper::modelLabel($model) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>{{ __('audit.filter_date_from') }}</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="form-group">
                    <label>{{ __('audit.filter_date_to') }}</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>

            </div>

            <div class="form-actions" style="margin-top: 16px;">
                <button type="submit" class="btn btn-primary">{{ __('common.search') }}</button>
                <a href="{{ route('audit.index') }}" class="btn btn-secondary">{{ __('common.reset') }}</a>
            </div>
        </form>
    </div>

    <div class="page-card">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">{{ __('audit.results_title') }}</h2>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('audit.th_date') }}</th>
                        <th>{{ __('audit.th_user') }}</th>
                        <th>{{ __('audit.th_action') }}</th>
                        <th>{{ __('audit.th_model') }}</th>
                        <th>{{ __('audit.th_section') }}</th>
                        <th>{{ __('audit.th_description') }}</th>
                        <th>{{ __('audit.th_details') }}</th>
                        <th>{{ __('audit.th_ip') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($logs as $log)
                        @php
                            $meta = AuditLabelHelper::parseMetadata($log->description);
                            $routeName = $meta['route'] ?? null;
                            $sectionLabel = AuditLabelHelper::sectionLabel($routeName, $log->model);
                        @endphp
                        <tr>
                            <td>
                                <div style="color:#000000; font-weight:600;">{{ AuditLabelHelper::formatDate($log->created_at) }}</div>
                                <div style="font-size:12px; color:#000000;">
                                    {{ AuditLabelHelper::formatTime($log->created_at) }}
                                </div>
                            </td>

                            <td>
                                <strong style="color:#000000;">{{ $log->user->name ?? '-' }}</strong>
                                <div style="font-size:12px; color:#111827;">
                                    {{ $log->user->email ?? '' }}
                                </div>
                            </td>

                            <td>
                                <span class="badge {{ $actionBadgeClass($log->action) }}">
                                    {{ AuditLabelHelper::actionLabel($log->action) }}
                                </span>
                            </td>

                            <td style="color:#000000;">{{ AuditLabelHelper::modelLabel($log->model) }}</td>

                            <td style="color:#000000; font-weight:600;">{{ $sectionLabel }}</td>

                            <td style="min-width:260px; color:#000000;">
                                {{ AuditLabelHelper::description($log) }}
                            </td>

                            <td>
                                <details>
                                    <summary style="cursor:pointer; color:#2563eb;">{{ __('audit.details_toggle') }}</summary>
                                    <div style="margin-top:8px; font-size:12px; line-height:1.6; color:#111827;">
                                        <div><strong>{{ __('audit.detail_keys.route') }}:</strong> {{ $sectionLabel }}</div>
                                        <div><strong>{{ __('audit.detail_keys.url') }}:</strong> {{ $meta['url'] ?? '-' }}</div>
                                        <div><strong>{{ __('audit.detail_keys.method') }}:</strong> {{ $meta['method'] ?? '-' }}</div>
                                        <div><strong>{{ __('audit.detail_keys.ip') }}:</strong> {{ $meta['ip'] ?? '-' }}</div>
                                        <div><strong>{{ __('audit.detail_keys.user_agent') }}:</strong> {{ $meta['user_agent'] ?? '-' }}</div>
                                        <div><strong>{{ __('audit.detail_keys.timestamp') }}:</strong> {{ isset($meta['timestamp']) ? $meta['timestamp'] : AuditLabelHelper::formatDate($log->created_at).' '.AuditLabelHelper::formatTime($log->created_at) }}</div>
                                        @if(isset($meta['old_role']) || isset($meta['new_role']) || isset($meta['changed_by']))
                                            <div><strong>{{ __('audit.detail_keys.old_role') }}:</strong> {{ $meta['old_role'] ?? '-' }}</div>
                                            <div><strong>{{ __('audit.detail_keys.new_role') }}:</strong> {{ $meta['new_role'] ?? '-' }}</div>
                                            <div><strong>{{ __('audit.detail_keys.changed_by') }}:</strong> {{ $meta['changed_by'] ?? '-' }}</div>
                                        @endif
                                        @if(isset($meta['question_preview']) || isset($meta['allowed_modules']))
                                            <div><strong>{{ __('audit.detail_keys.question_preview') }}:</strong> {{ $meta['question_preview'] ?? '-' }}</div>
                                            <div><strong>{{ __('audit.detail_keys.allowed_modules') }}:</strong> {{ $meta['allowed_modules'] ?? '-' }}</div>
                                        @endif
                                        @if(isset($meta['file_name']) || isset($meta['module']))
                                            <div><strong>{{ __('audit.detail_keys.module') }}:</strong> {{ $meta['module'] ?? '-' }}</div>
                                            <div><strong>{{ __('audit.detail_keys.file_name') }}:</strong> {{ $meta['file_name'] ?? '-' }}</div>
                                        @endif
                                    </div>
                                </details>
                            </td>

                            <td>{{ $meta['ip'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-row">
                                {{ __('audit.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div style="margin-top:20px;">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
