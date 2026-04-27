@extends('layouts.app')

@section('page_title', __('factory.factory_edit_page_title'))
@section('page_subtitle', __('factory.factory_create_sub', ['name' => $project->name]))

@section('content')
<x-ui.card :title="__('factory.factory_edit_card_title')" :subtitle="__('factory.factory_create_sub', ['name' => $project->name])">
    <div class="actions-row" style="margin-bottom:12px;">
        <a href="{{ route('installations.show', $project) }}" class="btn btn-secondary btn-sm">{{ __('factory.back_to_project') }}</a>
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

    @include('installations.factory-requests.form', [
        'project' => $project,
        'installationRequest' => $installationRequest,
        'items' => $items,
    ])
</x-ui.card>
@endsection
