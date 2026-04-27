@extends('layouts.app')

@section('page_title', __('purchases.material_show_title', ['id' => $materialRequest->id]))
@section('page_subtitle', __('purchases.material_show_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">{{ __('purchases.material_show_title', ['id' => $materialRequest->id]) }}</h1>
            <p class="page-subtitle">{{ __('purchases.material_show_subtitle') }}</p>
        </div>
        <a href="{{ route('purchases.material-requests.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>
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

    <div class="page-card" style="margin-top:12px;">
        <div class="details-grid">
            <div class="detail-box">
                <strong>{{ __('purchases.converted_purchases_count') }}</strong>
                <div>{{ $convertedPurchasesCount ?? 0 }}</div>
            </div>
        </div>
        @if(($convertedPurchasesCount ?? 0) === 0 && in_array($materialRequest->status, ['approved', 'processing'], true))
            <div class="actions-row" style="margin-top:10px;">
                <form method="POST" action="{{ route('purchases.material-requests.convert', $materialRequest) }}">
                    @csrf
                    <button class="btn btn-primary" type="submit">{{ __('purchases.convert_button') }}</button>
                </form>
            </div>
        @else
            <p style="margin-top:10px; color:#9ca3af;">{{ __('purchases.convert_already_or_status') }}</p>
        @endif
    </div>

    <div class="form-grid">
        <div class="detail-box"><strong>{{ __('purchases.th_project') }}</strong><br>{{ $materialRequest->project->name ?? '-' }}</div>
        <div class="detail-box"><strong>{{ __('purchases.project_code') }}</strong><br>{{ $materialRequest->project->project_code ?? '-' }}</div>
        <div class="detail-box"><strong>{{ __('purchases.created_by') }}</strong><br>{{ $materialRequest->creator->name ?? '-' }}</div>
        <div class="detail-box"><strong>{{ __('purchases.th_status') }}</strong><br><span class="badge badge-blue">{{ $materialRequest->status }}</span></div>
        <div class="detail-box"><strong>{{ __('purchases.th_submitted_at') }}</strong><br>{{ $materialRequest->submitted_at?->format('Y-m-d H:i') ?? '-' }}</div>
        <div class="detail-box"><strong>{{ __('purchases.approved_by') }}</strong><br>{{ $materialRequest->approver?->name ?? '-' }}</div>
        <div class="detail-box"><strong>{{ __('purchases.attachment') }}</strong><br>
            @if($materialRequest->attachment_path)
                <a href="{{ route('architect.material-requests.attachment', $materialRequest) }}" class="btn btn-secondary btn-sm">{{ __('purchases.open_attachment') }}</a>
            @else
                -
            @endif
        </div>
    </div>

    <div class="page-card" style="margin-top:16px;">
        <h3 style="margin-top:0;">{{ __('purchases.architect_notes') }}</h3>
        <p style="margin:0;">{{ $materialRequest->notes ?? '-' }}</p>
    </div>

    @if($materialRequest->rejection_reason)
        <div class="page-card" style="margin-top:16px;">
            <h3 style="margin-top:0; color:#ef4444;">{{ __('purchases.rejection_reason_title') }}</h3>
            <p style="margin:0;">{{ $materialRequest->rejection_reason }}</p>
        </div>
    @endif

    <div class="page-card" style="margin-top:16px;">
        <h3 style="margin-top:0;">{{ __('purchases.materials_to_purchase') }}</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('purchases.th_number') }}</th>
                        <th>{{ __('purchases.th_material') }}</th>
                        <th>{{ __('purchases.field_description') }}</th>
                        <th>{{ __('purchases.th_quantity') }}</th>
                        <th>{{ __('purchases.th_unit') }}</th>
                        <th>{{ __('common.notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materialRequest->items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->material_name }}</td>
                            <td>{{ $item->description ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ $item->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-row">{{ __('purchases.materials_empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="actions-row" style="margin-top:12px;">
        @if($materialRequest->status === 'submitted')
            <form method="POST" action="{{ route('purchases.material-requests.approve', $materialRequest) }}">
                @csrf
                <button class="btn btn-primary" type="submit">{{ __('purchases.approve_request') }}</button>
            </form>

            <form method="POST" action="{{ route('purchases.material-requests.reject', $materialRequest) }}" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                @csrf
                <input type="text" name="rejection_reason" placeholder="{{ __('purchases.reject_reason_placeholder') }}" required style="min-width:260px;">
                <button class="btn btn-danger" type="submit">{{ __('purchases.reject_request') }}</button>
            </form>
        @endif

        @if(in_array($materialRequest->status, ['approved', 'processing'], true))
            <form method="POST" action="{{ route('purchases.material-requests.status', $materialRequest) }}">
                @csrf
                <input type="hidden" name="status" value="processing">
                <button class="btn btn-warning" type="submit">{{ __('purchases.status_processing_btn') }}</button>
            </form>
            <form method="POST" action="{{ route('purchases.material-requests.status', $materialRequest) }}">
                @csrf
                <input type="hidden" name="status" value="completed">
                <button class="btn btn-primary" type="submit">{{ __('purchases.status_completed_btn') }}</button>
            </form>
        @endif
    </div>
</div>
@endsection
