@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">إضافة مستخدم جديد</h2>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">رجوع</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الاسم</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">الصلاحية</label>
                        <select name="role" class="form-select" required>
                            <option value="">-- اختر الصلاحية --</option>

                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                Admin
                            </option>

                            <option value="hr" {{ old('role') == 'hr' ? 'selected' : '' }}>
                                HR
                            </option>

                            <option value="engineer" {{ old('role') == 'engineer' ? 'selected' : '' }}>
                                Engineer
                            </option>

                            <option value="factory_manager" {{ old('role') == 'factory_manager' ? 'selected' : '' }}>
                                Factory Manager
                            </option>

                            <!-- 🔥 الجديد -->
                            <option value="manager" {{ old('role') == 'manager' ? 'selected' : '' }}>
                                Manager (مدير)
                            </option>

                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">حفظ المستخدم</button>
            </form>
        </div>
    </div>
</div>
@endsection