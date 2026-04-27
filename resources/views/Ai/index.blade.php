@extends('layouts.app')

@section('page_title', __('ai.page_title'))
@section('page_subtitle', __('ai.page_subtitle'))

@section('content')
<style>
    .ai-page {
        color: #f8fbff;
    }

    .ai-title {
        margin: 0;
        font-size: 26px;
        font-weight: 900;
        line-height: 1.35;
        letter-spacing: 0;
    }

    .ai-subtitle {
        margin: 8px 0 0;
        font-size: 14px;
        line-height: 1.9;
        font-weight: 600;
        color: #c7d8f3;
    }

    .ai-quick-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin: 16px 0 18px;
    }

    .ai-input-label {
        display: block;
        margin-bottom: 8px;
        font-size: 14px;
        font-weight: 800;
        color: #e8effb;
    }

    .ai-question-input {
        width: 100%;
        padding: 14px;
        font-size: 16px;
        line-height: 1.95;
        border-radius: 10px;
        min-height: 120px;
        background: #0b1426;
        color: #f8fbff;
        border: 1px solid rgba(180, 198, 224, 0.4);
    }

    .ai-question-input::placeholder {
        color: #9bb0cf;
    }

    .ai-answer-box,
    .ai-chat-card {
        background: #ffffff;
        border: 1px solid #cfd8e3;
        border-radius: 12px;
        padding: 18px;
        color: #111827;
    }

    .ai-answer-label,
    .ai-chat-label {
        font-size: 15px;
        font-weight: 900;
        margin-bottom: 9px;
        color: #0b0f19;
    }

    .ai-answer-text,
    .ai-chat-question,
    .ai-chat-answer {
        white-space: pre-wrap;
        font-size: 15px;
        line-height: 2;
        font-weight: 600;
        color: #111827;
    }

    .ai-chat-question {
        margin-bottom: 12px;
        border-bottom: 1px dashed #d1d5db;
        padding-bottom: 12px;
    }

    .ai-chat-time {
        margin-top: 12px;
        font-size: 12px;
        color: #374151;
        font-weight: 600;
    }

    .ai-history-title {
        margin: 0;
        font-size: 20px;
        font-weight: 900;
        line-height: 1.5;
    }

    .ai-history-subtitle {
        margin-top: 8px;
        color: #b8cae6;
        font-size: 13px;
        line-height: 1.8;
        font-weight: 600;
    }
</style>

<div class="page-card ai-page">

    <div class="page-header" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 class="ai-title">{{ __('ai.page_title') }}</h1>
            <p class="ai-subtitle">{{ __('ai.page_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('ai.clear') }}" onsubmit="return confirm(@json(__('ai.clear_confirm')))">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                 {{ __('ai.clear_chats') }}
            </button>
        </form>
    </div>

    <div class="ai-quick-actions">
        <button type="button" onclick="setQuestion(@json(__('ai.quick_prompt_report')))" class="btn btn-blue">{{ __('ai.quick_projects_report') }}</button>
        <button type="button" onclick="setQuestion(@json(__('ai.quick_prompt_summary')))" class="btn btn-green">{{ __('ai.quick_projects_summary') }}</button>
        <button type="button" onclick="setQuestion(@json(__('ai.quick_prompt_analysis')))" class="btn btn-orange">{{ __('ai.quick_projects_analysis') }}</button>
        <button type="button" onclick="setQuestion(@json(__('ai.quick_prompt_residency')))" class="btn btn-red">{{ __('ai.quick_residency') }}</button>
    </div>

    <form method="POST" action="{{ route('ai.ask') }}" style="margin-bottom:20px;">
        @csrf

        <label for="questionBox" class="ai-input-label">{{ __('ai.label_question') }}</label>
        <textarea name="question" id="questionBox"
            placeholder="{{ __('ai.placeholder_question') }}"
            class="ai-question-input">{{ old('question') }}</textarea>

        <button type="submit" class="btn btn-primary" style="margin-top:10px;">
            {{ __('ai.submit_ask') }}
        </button>
    </form>

    @if(session('ai_answer'))
        <div class="ai-answer-box" style="margin-top:20px;">
            <div class="ai-answer-label">{{ __('ai.answer_label') }}</div>
            <div class="ai-answer-text">
                {{ session('ai_answer') }}
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="alert-success" style="margin-top:16px;">{{ session('success') }}</div>
    @endif

    @if(isset($chats) && $chats->count() > 0)
        <div class="page-card" style="margin-top:24px;">
            <div class="page-header">
                <h2 class="ai-history-title">{{ __('ai.history_title') }}</h2>
                <p class="ai-history-subtitle">{{ __('ai.history_subtitle') }}</p>
            </div>

            <div style="display:flex; flex-direction:column; gap:14px;">
                @foreach($chats as $chat)
                    <div class="ai-chat-card">
                        <div class="ai-chat-label">{{ __('ai.chat_question_label') }}</div>
                        <div class="ai-chat-question">{{ $chat->question }}</div>

                        <div class="ai-chat-label">{{ __('ai.chat_answer_label') }}</div>
                        <div class="ai-chat-answer">{{ $chat->answer }}</div>

                        <div class="ai-chat-time">
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
