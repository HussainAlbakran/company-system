@extends('layouts.app')

@section('page_title', __('purchases.show_title'))
@section('page_subtitle', __('purchases.show_subtitle'))

@section('content')
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 class="page-title">{{ __('purchases.show_title') }}</h1>
            <p style="margin:8px 0 0; color:#6b7280;">{{ __('purchases.show_subtitle') }}</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>
    </div>

    <div class="form-grid">
        <div class="detail-box"><strong>{{ __('purchases.th_project') }}</strong><br>{{ $purchase->project->name ?? '-' }}</div>
        <div class="detail-box"><strong>{{ __('purchases.th_line') }}</strong><br>{{ $purchase->title }}</div>
        <div class="detail-box"><strong>{{ __('purchases.field_type') }}</strong><br>{{ $purchase->type ?? '-' }}</div>
        <div class="detail-box"><strong>{{ __('purchases.th_quantity') }}</strong><br>{{ $purchase->quantity ?? 1 }}</div>
        <div class="detail-box"><strong>{{ __('purchases.th_cost') }}</strong><br>{{ number_format((float) $purchase->cost, 2) }}</div>
        <div class="detail-box"><strong>{{ __('purchases.th_vendor') }}</strong><br>{{ $purchase->vendor ?? '-' }}</div>
        <div class="detail-box"><strong>{{ __('purchases.th_date') }}</strong><br>{{ $purchase->purchase_date ?? '-' }}</div>
        <div class="detail-box"><strong>{{ __('purchases.created_by') }}</strong><br>{{ $purchase->creator->name ?? '-' }}</div>
        <div class="detail-box">
            <strong>{{ __('purchases.architect_source') }}</strong><br>
            @if($purchase->architect_material_request_id)
                <a href="{{ route('purchases.material-requests.show', $purchase->architect_material_request_id) }}" class="btn btn-secondary btn-sm">
                    {{ __('purchases.request_number', ['id' => $purchase->architect_material_request_id]) }}
                </a>
            @else
                -
            @endif
        </div>
    </div>

    <div class="page-card" style="margin-top:20px;">
        <h3 style="margin-top:0;">{{ __('purchases.section_description') }}</h3>
        <p style="margin:0;">{{ $purchase->description ?? '-' }}</p>
    </div>

    <div class="page-card" style="margin-top:20px;">
        <h3 style="margin-top:0;">{{ __('purchases.section_notes') }}</h3>
        <p style="margin:0;">{{ $purchase->notes ?? '-' }}</p>
    </div>
</div>
@endsection
