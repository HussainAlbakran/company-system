@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="page-title">سجل العمليات والتقارير</h1>
            <p style="margin:8px 0 0; color:#6b7280;">
                عرض جميع العمليات التي تمت داخل النظام: إضافة، تعديل، حذف، قبول، رفض، إيقاف، إعادة تفعيل
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            رجوع إلى لوحة التحكم
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

    <div class="page-card" style="margin-bottom: 24px;">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">فلترة التقارير</h2>
        </div>

        <form method="GET" action="{{ route('audit.index') }}">
            <div class="form-grid">

                <div class="form-group">
                    <label>المستخدم</label>
                    <select name="user_id">
                        <option value="">كل المستخدمين</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>العملية</label>
                    <select name="action">
                        <option value="">كل العمليات</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>النوع / القسم</label>
                    <select name="model">
                        <option value="">كل الأنواع</option>
                        @foreach($models as $model)
                            <option value="{{ $model }}" {{ request('model') == $model ? 'selected' : '' }}>
                                {{ $model }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>من تاريخ</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="form-group">
                    <label>إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>

            </div>

            <div class="form-actions" style="margin-top: 16px;">
                <button type="submit" class="btn btn-primary">بحث</button>
                <a href="{{ route('audit.index') }}" class="btn btn-secondary">إعادة ضبط</a>
            </div>
        </form>
    </div>

    <div class="page-card">
        <div class="page-header">
            <h2 style="margin:0; font-size:22px;">نتائج سجل العمليات</h2>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th>العملية</th>
                        <th>النوع</th>
                        <th>رقم السجل</th>
                        <th>الوصف</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>

                            <td>
                                <strong>{{ $log->user->name ?? '-' }}</strong>
                                <div style="font-size:12px; color:#6b7280;">
                                    {{ $log->user->email ?? '' }}
                                </div>
                            </td>

                            <td>
                                @if($log->action === 'create')
                                    <span class="badge badge-green">إضافة</span>
                                @elseif($log->action === 'update')
                                    <span class="badge badge-blue">تعديل</span>
                                @elseif($log->action === 'delete')
                                    <span class="badge badge-red">حذف</span>
                                @elseif($log->action === 'approve')
                                    <span class="badge badge-green">اعتماد</span>
                                @elseif($log->action === 'reject')
                                    <span class="badge badge-gray">رفض</span>
                                @elseif($log->action === 'suspend')
                                    <span class="badge badge-orange">إيقاف</span>
                                @elseif($log->action === 'reactivate')
                                    <span class="badge badge-teal">إعادة تفعيل</span>
                                @else
                                    <span class="badge badge-gray">{{ $log->action }}</span>
                                @endif
                            </td>

                            <td>{{ $log->model ?? '-' }}</td>

                            <td>{{ $log->model_id ?? '-' }}</td>

                            <td style="min-width:260px;">
                                {{ $log->description ?? '-' }}
                            </td>

                            <td>
                                <div>{{ $log->created_at?->format('Y-m-d') }}</div>
                                <div style="font-size:12px; color:#6b7280;">
                                    {{ $log->created_at?->format('h:i A') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-row">
                                لا توجد عمليات مطابقة للبحث
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div style="margin-top:20px;">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection