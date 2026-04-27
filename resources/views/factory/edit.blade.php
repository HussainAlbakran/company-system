@extends('layouts.app')

@section('page_title', __('factory.edit_order_title'))
@section('page_subtitle', __('factory.index_page_subtitle'))

@section('content')
<div class="page-card">

    <div class="page-header">
        <h1 class="page-title">{{ __('factory.edit_order_title') }}</h1>
    </div>

    <form action="{{ route('factory.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>{{ __('factory.field_order_number') }}</label>
                <input type="text" name="order_number" value="{{ old('order_number', $order->order_number) }}" required>
            </div>

            <div class="form-group">
                <label>{{ __('factory.product_name') }}</label>
                <input type="text" name="product_name" value="{{ old('product_name', $order->product_name) }}" required>
            </div>

            <div class="form-group form-group-full">
                <label>{{ __('factory.required_quantity_readonly') }}</label>
                <div class="form-control" style="background:rgba(15,23,42,.65); border-style:dashed; color:#e2e8f0;">
                    {{ number_format((float) $order->planned_quantity, 2) }}
                </div>
                @if($order->project && $order->project->required_concrete_quantity !== null)
                    <p class="page-subtitle" style="margin-top:6px; font-size:12px;">{{ __('factory.design_source_note', ['qty' => number_format((float) $order->project->required_concrete_quantity, 2)]) }}</p>
                @endif
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ __('factory.update') }}</button>
            <a href="{{ route('factory.index') }}" class="btn btn-secondary">{{ __('common.back') }}</a>
        </div>

    </form>
</div>
@endsection