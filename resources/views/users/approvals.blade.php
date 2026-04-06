@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h2 class="mb-1">موافقات المستخدمين</h2>
            <p class="text-muted mb-0">إدارة طلبات التسجيل، الإيقاف، وإعادة التفعيل</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            رجوع إلى المستخدمين
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">

        {{-- Pending Users --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-warning-subtle border-0 rounded-top-4 py-3">
                    <h4 class="mb-0">المستخدمون بانتظار الموافقة</h4>
                </div>
                <div class="card-body">
                    @if($pendingUsers->count())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الدور</th>
                                        <th>تاريخ التسجيل</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingUsers as $user)
                                        <tr>
                                            <td class="fw-semibold">{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role }}</td>
                                            <td>{{ $user->created_at?->format('Y-m-d h:i A') }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                    <form action="{{ route('users.approve', $user->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm px-3">
                                                            قبول
                                                        </button>
                                                    </form>

                                                    <button class="btn btn-danger btn-sm px-3" type="button" data-bs-toggle="collapse" data-bs-target="#reject-user-{{ $user->id }}">
                                                        رفض
                                                    </button>
                                                </div>

                                                <div class="collapse mt-3" id="reject-user-{{ $user->id }}">
                                                    <form action="{{ route('users.reject', $user->id) }}" method="POST" class="border rounded-3 p-3 bg-light">
                                                        @csrf
                                                        <label class="form-label fw-semibold">سبب الرفض (اختياري)</label>
                                                        <textarea name="rejection_reason" class="form-control mb-2" rows="3" placeholder="اكتب سبب الرفض هنا..."></textarea>
                                                        <button type="submit" class="btn btn-danger btn-sm">تأكيد الرفض</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            لا يوجد مستخدمون بانتظار الموافقة حاليًا.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Approved Users --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-success-subtle border-0 rounded-top-4 py-3">
                    <h4 class="mb-0">المستخدمون المعتمدون</h4>
                </div>
                <div class="card-body">
                    @if($approvedUsers->count())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الدور</th>
                                        <th>تاريخ الاعتماد</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedUsers as $user)
                                        <tr>
                                            <td class="fw-semibold">{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role }}</td>
                                            <td>{{ $user->approved_at ? \Carbon\Carbon::parse($user->approved_at)->format('Y-m-d h:i A') : '-' }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                    <form action="{{ route('users.suspend', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إيقاف هذا المستخدم؟');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-sm px-3">
                                                            إيقاف المستخدم
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            لا يوجد مستخدمون معتمدون.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Suspended Users --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-danger-subtle border-0 rounded-top-4 py-3">
                    <h4 class="mb-0">المستخدمون الموقوفون</h4>
                </div>
                <div class="card-body">
                    @if($suspendedUsers->count())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الدور</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suspendedUsers as $user)
                                        <tr>
                                            <td class="fw-semibold">{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                    <form action="{{ route('users.reactivate', $user->id) }}" method="POST" onsubmit="return confirm('هل تريد إعادة تفعيل هذا المستخدم؟');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-sm px-3">
                                                            إلغاء الإيقاف
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            لا يوجد مستخدمون موقوفون.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Rejected Users --}}
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-secondary-subtle border-0 rounded-top-4 py-3">
                    <h4 class="mb-0">المستخدمون المرفوضون</h4>
                </div>
                <div class="card-body">
                    @if($rejectedUsers->count())
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الدور</th>
                                        <th>سبب الرفض</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rejectedUsers as $user)
                                        <tr>
                                            <td class="fw-semibold">{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->role }}</td>
                                            <td>{{ $user->rejection_reason ?: '-' }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                    <form action="{{ route('users.approve', $user->id) }}" method="POST" onsubmit="return confirm('هل تريد اعتماد هذا المستخدم؟');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm px-3">
                                                            اعتماد المستخدم
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            لا يوجد مستخدمون مرفوضون.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection