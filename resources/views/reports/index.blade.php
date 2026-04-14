@extends('layouts.app')

@section('content')
<div class="page-card">

    <h1> التقارير</h1>

    <h2> الموظفين</h2>
    <table border="1" width="100%">
        <tr>
            <th>الاسم</th>
            <th>القسم</th>
            <th>تاريخ انتهاء الإقامة</th>
        </tr>

        @foreach($employees as $emp)
        <tr>
            <td>{{ $emp->name }}</td>
            <td>{{ $emp->department->name ?? '-' }}</td>
            <td>{{ $emp->residency_expiry_date ?? '-' }}</td>
        </tr>
        @endforeach
    </table>

    <br><br>

    <h2> المشاريع</h2>
    <table border="1" width="100%">
        <tr>
            <th>المشروع</th>
            <th>القسم</th>
            <th>النهاية</th>
        </tr>

        @foreach($projects as $project)
        <tr>
            <td>{{ $project->name }}</td>
            <td>{{ $project->department->name ?? '-' }}</td>
            <td>{{ $project->end_date ?? '-' }}</td>
        </tr>
        @endforeach
    </table>

</div>
@endsection