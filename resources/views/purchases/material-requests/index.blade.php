@extends('layouts.app')

@section('page_title', __('purchases.material_requests_page_title'))
@section('page_subtitle', __('purchases.material_requests_page_subtitle'))

@section('content')
<x-ui.card :title="__('purchases.material_requests_card_title')" :subtitle="__('purchases.material_requests_card_subtitle')">
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <x-ui.table>
        <thead>
            <tr>
                <th>{{ __('purchases.th_number') }}</th>
                <th>{{ __('purchases.th_project') }}</th>
                <th>{{ __('purchases.th_architect') }}</th>
                <th>{{ __('purchases.th_status') }}</th>
                <th>{{ __('purchases.th_submitted_at') }}</th>
                <th>{{ __('purchases.th_action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->project->name ?? '-' }}</td>
                    <td>{{ $request->creator->name ?? '-' }}</td>
                    <td><span class="badge badge-blue">{{ $request->status }}</span></td>
                    <td>{{ $request->submitted_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>
                        <a href="{{ route('purchases.material-requests.show', $request) }}" class="btn btn-primary btn-sm">{{ __('purchases.open') }}</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-row">{{ __('purchases.material_requests_empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    <div style="margin-top:14px;">
        {{ $requests->links() }}
    </div>
</x-ui.card>
@endsection
