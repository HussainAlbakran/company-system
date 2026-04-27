@extends('layouts.app')

@section('page_title', __('dashboard.page_title'))
@section('page_subtitle', __('dashboard.basic_account_subtitle'))

@section('content')
<div class="dashboard-stack w-full" style="max-width:520px;">
    <section class="dashboard-panel">
        <div class="panel-head">
            <div>
                <h3 class="panel-title">{{ __('dashboard.basic_welcome', ['name' => auth()->user()->name]) }}</h3>
                <p class="panel-subtitle">{{ __('dashboard.basic_services_hint') }}</p>
            </div>
        </div>
        <div class="actions-row" style="margin-top:12px; flex-direction:column; align-items:stretch; gap:10px;">
            <a href="{{ route('leaves.create') }}" class="btn btn-primary" style="width:100%; justify-content:center;">{{ __('navigation.leave_request') }}</a>
            <a href="{{ route('support.index') }}" class="btn btn-secondary" style="width:100%; justify-content:center;">{{ __('navigation.technical_support') }}</a>
            <a href="{{ route('profile.show') }}" class="btn btn-secondary" style="width:100%; justify-content:center;">{{ __('navigation.profile') }}</a>
        </div>
    </section>
</div>
@endsection
