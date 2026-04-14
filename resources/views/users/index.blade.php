@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
        <div>
            <h1 class="page-title">👥 المستخدمون</h1>
            <p style="color:#6b7280;">إدارة جميع حسابات النظام</p>
        </div>

        <a href="{{ route('users.create') }}" class="btn btn-primary">
            ➕ إضافة مستخدم
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom:15px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>البريد</th>
                    <th>الدور</th>
                    <th>الحالة</th>
                    <th>نشط؟</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>

            <tbody>

                @forelse($users as $user)

                <tr>

                    <td style="font-weight:700;">
                        {{ $user->name }}
                    </td>

                    <td>{{ $user->email }}</td>

                    <td>
                        <span class="badge badge-blue">
                            {{ strtoupper($user->role) }}
                        </span>
                    </td>

                    <td>
                        @if($user->approval_status == 'approved')
                            <span class="badge badge-green">✔ معتمد</span>

                        @elseif($user->approval_status == 'pending')
                            <span class="badge badge-orange">⏳ انتظار</span>

                        @elseif($user->approval_status == 'rejected')
                            <span class="badge badge-gray">❌ مرفوض</span>

                        @elseif($user->approval_status == 'suspended')
                            <span class="badge badge-red">🚫 موقوف</span>

                        @else
                            <span class="badge badge-gray">-</span>
                        @endif
                    </td>

                    <td>
                        @if($user->is_active)
                            <span class="badge badge-green">نشط</span>
                        @else
                            <span class="badge badge-red">غير نشط</span>
                        @endif
                    </td>

                    <td style="display:flex; gap:6px; flex-wrap:wrap;">

                        <!-- تعديل -->
                        <a href="{{ route('users.edit', $user->id) }}"
                           class="btn btn-warning btn-sm">
                           تعديل
                        </a>

                        <!-- إيقاف -->
                        @if($user->is_active && auth()->id() != $user->id)
                        <form action="{{ route('users.suspend', $user->id) }}"
                              method="POST"
                              onsubmit="return confirm('إيقاف المستخدم؟')">
                            @csrf
                            <button class="btn btn-orange btn-sm">
                                إيقاف
                            </button>
                        </form>
                        @endif

                        <!-- تفعيل -->
                        @if(!$user->is_active)
                        <form action="{{ route('users.reactivate', $user->id) }}"
                              method="POST"
                              onsubmit="return confirm('إعادة تفعيل المستخدم؟')">
                            @csrf
                            <button class="btn btn-success btn-sm">
                                تفعيل
                            </button>
                        </form>
                        @endif

                        <!-- حذف -->
                        @if(auth()->id() != $user->id)
                        <form action="{{ route('users.destroy',$user->id) }}"
                              method="POST"
                              onsubmit="return confirm('حذف المستخدم؟')">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm">
                                حذف
                            </button>
                        </form>
                        @endif

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="6" class="empty-row">
                        لا يوجد مستخدمون
                    </td>
                </tr>

                @endforelse

            </tbody>
        </table>
    </div>

</div>
@endsection