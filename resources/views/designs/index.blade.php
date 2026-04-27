@extends('layouts.app')

@section('page_title', 'التصاميم')
@section('page_subtitle', 'وصول سريع إلى لوحة التصاميم')

@section('content')
<div class="page-card">
    <div class="page-header">
        <h1 class="page-title">التصاميم</h1>
    </div>

    <p style="color:#cbd5e1; margin-bottom: 12px;">
        يمكنك متابعة جميع مهام التصاميم من خلال لوحة المعماري.
    </p>

    <a href="{{ route('architect-tasks.index') }}" class="btn btn-primary">
        فتح لوحة التصاميم
    </a>
</div>
@endsection
