@extends('layouts.app')

@section('page_title', __('architect.material_requirements_title'))
@section('page_subtitle', __('architect.material_requirements_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('architect.material_requirements_title') }}</h1>
            <p class="page-subtitle">{{ __('architect.project_label', ['name' => $project->name]) }} — {{ $project->project_code ?? '-' }}</p>
        </div>
        <div class="actions-row">
            <a href="{{ route('architect.index') }}" class="btn btn-secondary">{{ __('architect.back_to_projects') }}</a>
        </div>
    </div>

    <div class="page-card" style="margin-bottom:16px;">
        <div class="page-header">
            <h2 class="page-title" style="font-size:15px;">{{ __('architect.section_project_info') }}</h2>
        </div>
        <div class="form-grid" style="margin:0;">
            <div class="form-group">
                <label>{{ __('architect.th_project_code') }}</label>
                <div class="form-control" style="background:transparent; border-style:dashed;">{{ $project->project_code ?? '-' }}</div>
            </div>
            <div class="form-group">
                <label>{{ __('architect.th_project_name') }}</label>
                <div class="form-control" style="background:transparent; border-style:dashed;">{{ $project->name }}</div>
            </div>
            <div class="form-group">
                <label>{{ __('architect.th_client') }}</label>
                <div class="form-control" style="background:transparent; border-style:dashed;">{{ $project->client_name ?? '-' }}</div>
            </div>
            <div class="form-group">
                <label>{{ __('architect.th_stage') }}</label>
                <div><span class="badge badge-blue">{{ $project->current_stage ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-right:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-card" style="margin-bottom:16px;">
        <div class="page-header">
            <h2 class="page-title" style="font-size:16px;">{{ __('architect.new_material_request') }}</h2>
            <p class="page-subtitle" style="margin-top:4px;">{{ __('architect.new_material_request_sub') }}</p>
        </div>

        <form method="POST" action="{{ route('architect.material-requests.store', $project) }}" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">
                <div class="form-group form-group-full">
                    <label>{{ __('architect.architect_notes') }}</label>
                    <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
                </div>

                <div class="form-group form-group-full">
                    <label>{{ __('architect.attachment_optional') }}</label>
                    <input type="file" name="attachment" accept=".pdf,.xlsx,.csv">
                </div>
            </div>

            <div style="margin-top:16px;">
                <h2 class="page-title" style="font-size:15px; margin-bottom:4px;">{{ __('architect.request_line_items') }}</h2>
                <p class="page-subtitle" style="font-size:13px; margin-top:0;">{{ __('architect.request_line_items_hint') }}</p>
            </div>

            @php
                $itemsForForm = old('items');
                if (! is_array($itemsForForm) || count($itemsForForm) < 1) {
                    $itemsForForm = [
                        ['material_name' => '', 'description' => '', 'quantity' => '', 'unit' => 'pcs', 'notes' => ''],
                    ];
                }
                $rowKeys = array_map('intval', array_keys($itemsForForm));
                $nextItemIndex = (count($rowKeys) ? max($rowKeys) : -1) + 1;
            @endphp
            @include('architect.material-requests._items-table', ['itemsForForm' => $itemsForForm, 'nextItemIndex' => $nextItemIndex])

            <div class="actions-row" style="margin-top:16px;">
                <button type="submit" name="action_type" value="draft" class="btn btn-warning">{{ __('architect.save_draft') }}</button>
                <button type="submit" name="action_type" value="submit" class="btn btn-primary">{{ __('architect.submit_to_purchasing') }}</button>
            </div>
        </form>
    </div>

    <div class="page-card">
        <div class="page-header">
            <h2 class="page-title" style="font-size:16px;">{{ __('architect.previous_requests_title') }}</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('architect.status') }}</th>
                        <th>{{ __('architect.th_rejection_reason') }}</th>
                        <th>{{ __('architect.th_items_count') }}</th>
                        <th>{{ __('architect.th_created_at') }}</th>
                        <th>{{ __('architect.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materialRequests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td><span class="badge badge-blue">{{ $request->status }}</span></td>
                            <td>{{ $request->rejection_reason ?? '-' }}</td>
                            <td>{{ $request->items_count }}</td>
                            <td>{{ $request->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                @if(in_array($request->status, ['draft', 'rejected'], true))
                                    <a href="{{ route('architect.material-requests.edit', [$project, $request]) }}" class="btn btn-warning btn-sm">{{ __('architect.edit_draft') }}</a>
                                @endif
                                @if($request->attachment_path)
                                    <a href="{{ route('architect.material-requests.attachment', $request) }}" class="btn btn-secondary btn-sm">{{ __('architect.attachment_link') }}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty-row">{{ __('architect.previous_requests_empty') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
