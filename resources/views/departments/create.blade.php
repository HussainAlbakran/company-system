@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h1 class="page-title">إضافة قسم جديد</h1>
            <p style="color:#6b7280; margin-top:8px;">
                قم بإدخال بيانات القسم لإضافته للنظام
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

    <form action="{{ route('departments.store') }}" method="POST" class="form-card">
        @csrf

        <div class="form-grid">

            <div class="form-group">
                <label>اسم القسم</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="مثال: قسم الهندسة"
                       required>
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                حفظ القسم
            </button>

            <a href="{{ route('departments.index') }}" class="btn btn-secondary">
                إلغاء
            </a>
        </div>

    </form>

</div>

@endsection