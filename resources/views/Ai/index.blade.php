@extends('layouts.app')

@section('content')
<div class="page-card">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1> الذكاء الذكي للنظام</h1>
            <p>اسأل عن أي شيء في النظام حسب صلاحياتك فقط</p>
        </div>

        <form method="POST" action="{{ route('ai.clear') }}" onsubmit="return confirm('هل تريد مسح جميع المحادثات؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                 مسح المحادثات
            </button>
        </form>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:15px;">
        <button type="button" onclick="setQuestion('اعطني تقرير كامل عن المشاريع')" class="btn btn-blue"> تقرير المشاريع</button>
        <button type="button" onclick="setQuestion('اعطني ملخص سريع عن المشاريع')" class="btn btn-green"> ملخص</button>
        <button type="button" onclick="setQuestion('حلل المشاريع الحالية')" class="btn btn-orange"> تحليل</button>
        <button type="button" onclick="setQuestion('من تنتهي إقامته قريب')" class="btn btn-red"> الإقامات</button>
    </div>

    <form method="POST" action="{{ route('ai.ask') }}" style="margin-bottom:20px;">
        @csrf

        <textarea name="question" id="questionBox"
            placeholder="اكتب سؤالك هنا..."
            style="width:100%; padding:14px; font-size:16px; border-radius:10px; min-height:100px;">{{ old('question') }}</textarea>

        <button type="submit" class="btn btn-primary" style="margin-top:10px;">
            اسأل الذكاء
        </button>
    </form>

    @if(session('ai_answer'))
        <div style="margin-top:20px; background:#f9fafb; padding:18px; border-radius:12px; border:1px solid #e5e7eb;">
            <div style="font-weight:800; margin-bottom:10px;"> الرد:</div>
            <div style="line-height:1.9; white-space:pre-wrap;">
                {{ session('ai_answer') }}
            </div>
        </div>
    @endif

    @if(isset($chats) && $chats->count() > 0)
        <div class="page-card" style="margin-top:24px;">
            <div class="page-header">
                <h2 style="margin:0;">🕘 المحادثات السابقة</h2>
                <p style="margin-top:8px; color:#6b7280;">آخر الأسئلة والأجوبة الخاصة بك</p>
            </div>

            <div style="display:flex; flex-direction:column; gap:14px;">
                @foreach($chats as $chat)
                    <div style="border:1px solid #e5e7eb; border-radius:12px; padding:16px; background:#fff;">
                        <div style="font-weight:800; margin-bottom:8px;">👤 السؤال:</div>
                        <div style="margin-bottom:12px; white-space:pre-wrap;">{{ $chat->question }}</div>

                        <div style="font-weight:800; margin-bottom:8px;"> الإجابة:</div>
                        <div style="white-space:pre-wrap; color:#374151;">{{ $chat->answer }}</div>

                        <div style="margin-top:10px; font-size:12px; color:#6b7280;">
                            {{ $chat->created_at }}
                        </div>
                    </div>
                @endforeach
            </div>

            @if(method_exists($chats, 'links'))
                <div style="margin-top:14px;">
                    {{ $chats->links() }}
                </div>
            @endif
        </div>
    @endif

</div>

<script>
function setQuestion(text) {
    document.getElementById('questionBox').value = text;
}
</script>
@endsection