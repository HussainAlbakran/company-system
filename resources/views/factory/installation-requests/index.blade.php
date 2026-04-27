@extends('layouts.app')

@section('page_title', __('factory.install_req_index_title'))
@section('page_subtitle', __('factory.install_req_index_subtitle'))

@section('content')
<x-ui.card :title="__('factory.install_req_card_title')" :subtitle="__('factory.install_req_card_subtitle')">
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <x-ui.table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('factory.project_name') }}</th>
                <th>{{ __('factory.project_code') }}</th>
                <th>{{ __('factory.th_requester') }}</th>
                <th>{{ __('factory.th_status') }}</th>
                <th>{{ __('factory.th_created') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
                <tr>
                    <td>{{ $req->id }}</td>
                    <td><strong>{{ $req->project?->name ?? '-' }}</strong></td>
                    <td>{{ $req->project?->project_code ?? '-' }}</td>
                    <td>{{ $req->creator?->name ?? '-' }}</td>
                    <td>
                        <span class="badge badge-blue">{{ __('factory.installation_status.'.$req->status) }}</span>
                    </td>
                    <td>{{ $req->created_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('factory.installation-requests.show', $req) }}" class="btn btn-primary btn-sm">{{ __('factory.view') }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-row">{{ __('factory.install_req_empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if(method_exists($requests, 'links'))
        <div style="margin-top:16px;">{{ $requests->links() }}</div>
    @endif
</x-ui.card>
@endsection
