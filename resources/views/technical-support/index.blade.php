@extends('layouts.app')

@section('page_title', __('support.page_title'))
@section('page_subtitle', __('support.page_subtitle'))

@section('content')

<div class="page-card">

    <div class="page-header">
        <h1 class="page-title">{{ __('support.page_title') }}</h1>
        <p style="color:#6b7280;">
            {{ __('support.page_subtitle') }}
        </p>
    </div>

    <div class="details-grid" style="margin-top:20px;">

        <div class="detail-box">
            <strong>{{ __('support.email_label') }}</strong>
            <div>
                <a href="mailto:altaqaddum.system@gmail.com">
                    altaqaddum.system@gmail.com
                </a>
            </div>
        </div>

        <div class="detail-box">
            <strong>{{ __('support.phone_label') }}</strong>
            <div>
                <a href="tel:0590548089">
                    0590548089
                </a>
            </div>
        </div>

    </div>

</div>

@endsection
