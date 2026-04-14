@extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header">
        <h2>المهندس المعماري</h2>
        <p>المشاريع القادمة من المبيعات</p>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>رقم المشروع</th>
                    <th>اسم المشروع</th>
                    <th>العميل</th>
                    <th>المرحلة الحالية</th>
                    <th>الإجراء</th>
                </tr>
            </thead>

            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>{{ $project->project_code }}</td>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->client_name }}</td>

                        <td>
                            <span class="badge badge-blue">
                                {{ $project->current_stage }}
                            </span>
                        </td>

                        <td>
                            <form action="{{ route('architect.complete', $project->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-success">
                                    إنهاء وإرسال للمشتريات
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">
                            لا توجد مشاريع حالياً
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection