@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">تعديل القسم</h1>
            <p style="color:#6b7280; margin-top:8px;">
                تحديث بيانات القسم
            </p>
        </div>

        <a href="{{ route('departments.index') }}" class="btn btn-secondary">
            رجوع
        </a>
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

    <form action="{{ route('departments.update', $department->id) }}"
          method="POST"
          class="form-card">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label>اسم القسم</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $department->name) }}"
                       required>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                تحديث القسم
            </button>

            <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                إلغاء
            </a>
        </div>

    </form>

</div>

@endsection