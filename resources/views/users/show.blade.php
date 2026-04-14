@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">تفاصيل المستخدم</h2>

    <div class="card">
        <div class="card-body">
            <p><strong>الاسم:</strong> {{ $user->name }}</p>
            <p><strong>البريد:</strong> {{ $user->email }}</p>
            <p><strong>الدور:</strong> {{ $user->role ?? '-' }}</p>
            <p><strong>حالة الموافقة:</strong> {{ $user->approval_status ?? '-' }}</p>
            <p><strong>نشط:</strong> {{ !empty($user->is_active) ? 'نعم' : 'لا' }}</p>
        </div>
    </div>
</div>
@endsection