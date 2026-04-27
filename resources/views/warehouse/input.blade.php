@extends('layouts.app')

@section('page_title', __('warehouse.input'))
@section('page_subtitle', __('warehouse.page_title'))

@section('content')

<div class="page-card">

    <div style="margin-bottom:15px;">
        <a href="{{ route('warehouse.index') }}" class="btn btn-secondary">
            {{ __('warehouse.back') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
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

    <form method="POST" action="{{ route('warehouse.store', $sectionKey) }}">
        @csrf

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>{{ __('warehouse.th_name') }}</th>
                        <th>{{ __('warehouse.th_quantity') }}</th>
                        <th>{{ __('warehouse.th_unit') }}</th>
                        <th>{{ __('warehouse.th_notes') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @for($i = 0; $i < 20; $i++)
                        <tr>
                            <td>
                                <input type="text" name="items[{{ $i }}][name]" value="{{ old('items.' . $i . '.name') }}">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][quantity]" value="{{ old('items.' . $i . '.quantity') }}">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][unit]" value="{{ old('items.' . $i . '.unit') }}">
                            </td>
                            <td>
                                <input type="text" name="items[{{ $i }}][notes]" value="{{ old('items.' . $i . '.notes') }}">
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <div style="margin-top:20px;">
            <button type="submit" class="btn btn-primary">
                {{ __('warehouse.save') }}
            </button>
        </div>

    </form>

</div>

@endsection
