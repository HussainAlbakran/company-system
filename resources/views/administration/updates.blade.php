@extends('layouts.app')

@section('page_title', __('administration.updates_title'))
@section('page_subtitle', __('administration.updates_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 class="page-title">{{ __('administration.updates_review_title') }}</h1>
            <p class="page-subtitle">{{ __('administration.updates_review_subtitle') }}</p>
        </div>
        <a href="{{ route('administration.index') }}" class="btn btn-secondary btn-sm">{{ __('administration.back_to_center') }}</a>
    </div>

    <div class="table-wrap" style="margin-top:12px;">
        <table>
            <thead>
                <tr>
                    <th>{{ __('administration.th_date') }}</th>
                    <th>{{ __('administration.th_project') }}</th>
                    <th>{{ __('administration.th_title') }}</th>
                    <th>{{ __('common.description') }}</th>
                    <th>{{ __('administration.th_progress') }}</th>
                    <th>{{ __('administration.th_recorded_by') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($updates as $u)
                    <tr>
                        <td>{{ $u->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $u->project->name ?? '—' }}</td>
                        <td>{{ $u->title }}</td>
                        <td style="max-width:220px; white-space:pre-wrap; font-size:11px;">{{ \Illuminate\Support\Str::limit($u->description ?? '', 120) }}</td>
                        <td>{{ (int) $u->progress }}٪</td>
                        <td>{{ $u->creator?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-row">{{ __('administration.no_updates') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:12px;">{{ $updates->links() }}</div>
</div>
@endsection
