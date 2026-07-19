@extends('layouts.app')

@section('page_title', __('admin_emails.page_title'))
@section('page_subtitle', __('admin_emails.page_subtitle'))

@section('content')
<style>
    /* Override the global `input { width:100%; border; padding; }` rule which
       stretches checkboxes across the row and hides the name/email next to them. */
    #broadcastForm input[type="checkbox"] {
        width: 16px;
        height: 16px;
        padding: 0;
        margin: 0;
        border: 1px solid #9ca3af;
        flex-shrink: 0;
        accent-color: #2563eb;
        cursor: pointer;
    }
    .user-row { font-size: 13.5px; font-weight: 400; margin-bottom: 2px; }
    .user-row:hover { background: #f1f5f9; }
    .user-row:has(.recipient-box:checked) { background: #eff6ff; }
</style>
<div class="page-card">
    <div class="page-header" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <h1 class="page-title">{{ __('admin_emails.page_title') }}</h1>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

    @if($errors->any())
        <div class="alert-danger"><ul style="margin:0;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="post" action="{{ route('admin-emails.send') }}" enctype="multipart/form-data" id="broadcastForm">
        @csrf

        <div style="display:grid; grid-template-columns:minmax(0, 1.6fr) minmax(280px, 1fr); gap:20px; align-items:start;">

            <div>
                {{-- نوع الرسالة --}}
                <label style="display:block; font-weight:600; margin-bottom:8px;">{{ __('admin_emails.field_type') }}</label>
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(150px, 1fr)); gap:10px; margin-bottom:18px;">
                    @foreach([
                        'general' => ['icon' => '✉️', 'color' => '#2563eb'],
                        'residency_expiry' => ['icon' => '🪪', 'color' => '#dc2626'],
                        'passport_expiry' => ['icon' => '🛂', 'color' => '#d97706'],
                    ] as $typeKey => $meta)
                        <label class="type-card" data-type="{{ $typeKey }}"
                               style="border:2px solid #e5e7eb; border-radius:12px; padding:12px; cursor:pointer; text-align:center; background:#fff; transition:all .15s;">
                            <input type="radio" name="message_type" value="{{ $typeKey }}" style="display:none;"
                                   @checked(old('message_type', 'general') === $typeKey)>
                            <span style="font-size:22px; display:block;">{{ $meta['icon'] }}</span>
                            <span style="font-weight:600; display:block; margin-top:4px;">{{ __('admin_emails.type_'.$typeKey) }}</span>
                            <span style="color:#6b7280; font-size:11.5px; display:block; margin-top:2px;">{{ __('admin_emails.type_'.$typeKey.'_hint') }}</span>
                        </label>
                    @endforeach
                </div>

                {{-- معاينة القالب الجاهز --}}
                <div id="templatePreview" style="display:none; border:1px dashed #cbd5e1; background:#f8fafc; border-radius:10px; padding:14px; margin-bottom:16px;">
                    <strong style="display:block; margin-bottom:6px;">{{ __('admin_emails.template_preview') }}</strong>
                    <div id="templatePreviewText" style="color:#374151; font-size:13.5px; line-height:1.9; white-space:pre-line;"></div>
                    <small style="color:#2563eb; display:block; margin-top:8px;">{{ __('admin_emails.template_auto_note') }}</small>
                </div>

                <div class="form-group" id="titleGroup" style="margin-bottom:14px;">
                    <label>{{ __('admin_emails.field_title') }} <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="title" id="titleInput" maxlength="255" value="{{ old('title') }}" style="width:100%;">
                </div>

                <div class="form-group" style="margin-bottom:14px;">
                    <label>
                        <span id="descLabelRequired">{{ __('admin_emails.field_description') }} <span style="color:#dc2626;">*</span></span>
                        <span id="descLabelOptional" style="display:none;">{{ __('admin_emails.field_extra_note') }}</span>
                    </label>
                    <textarea name="description" id="descInput" rows="7" maxlength="10000" style="width:100%;">{{ old('description') }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label>{{ __('admin_emails.field_attachment') }}</label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.webm">
                    <small style="color:#6b7280; display:block; margin-top:4px;">{{ __('admin_emails.attachment_hint') }}</small>
                </div>

                <button type="submit" class="btn btn-primary" id="sendBtn" style="min-width:180px;">
                    {{ __('admin_emails.btn_send') }}
                </button>
            </div>

            {{-- المستخدمون --}}
            <div style="border:1px solid #e5e7eb; border-radius:12px; background:#fff; overflow:hidden;">
                <div style="background:#f8fafc; border-bottom:1px solid #e5e7eb; padding:12px 14px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:8px; margin-bottom:10px; flex-wrap:wrap;">
                        <strong>{{ __('admin_emails.recipients_title') }} ({{ $users->count() }})</strong>
                        <span id="selectedCount" style="color:#fff; background:#2563eb; font-size:12px; font-weight:600; border-radius:99px; padding:2px 10px;"></span>
                    </div>

                    <input type="text" id="userSearch" placeholder="{{ __('admin_emails.search_placeholder') }}"
                           style="width:100%; margin-bottom:8px; padding:8px 10px; border:1px solid #d1d5db; border-radius:8px; background:#fff;">

                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:600; padding:4px 2px;">
                        <input type="checkbox" id="selectAll">
                        <span>{{ __('admin_emails.select_all') }}</span>
                    </label>
                </div>

                <div style="max-height:480px; overflow-y:auto; padding:6px;" id="usersList">
                    @forelse($users as $user)
                        <label class="user-row" style="display:flex; align-items:center; gap:10px; padding:9px 10px; cursor:pointer; border-radius:8px; margin-bottom:2px;"
                               data-search="{{ mb_strtolower($user->name.' '.$user->email) }}"
                               data-has-residency="{{ $user->employee?->residency_expiry_date ? '1' : '0' }}"
                               data-has-passport="{{ $user->employee?->passport_expiry_date ? '1' : '0' }}">
                            <input type="checkbox" name="recipients[]" value="{{ $user->id }}" class="recipient-box"
                                   @checked(is_array(old('recipients')) && in_array($user->id, old('recipients')))>
                            <span style="min-width:0; flex:1;">
                                <span style="display:block; font-weight:600; white-space:normal; word-break:break-word;">{{ $user->name }}</span>
                                <span style="display:block; color:#6b7280; font-size:12px; direction:ltr; text-align:end; word-break:break-all;">{{ $user->email }}</span>
                            </span>
                        </label>
                    @empty
                        <p style="color:#6b7280; padding:8px;">{{ __('admin_emails.no_users') }}</p>
                    @endforelse
                </div>

                <p id="skipWarning" style="display:none; color:#b45309; background:#fef3c7; border-radius:8px; padding:8px 10px; font-size:12.5px; margin:10px;">
                    {{ __('admin_emails.skip_warning') }}
                </p>
            </div>

        </div>
    </form>
</div>

<script>
(function () {
    const form = document.getElementById('broadcastForm');
    const selectAll = document.getElementById('selectAll');
    const search = document.getElementById('userSearch');
    const rows = Array.from(document.querySelectorAll('.user-row'));
    const boxes = Array.from(document.querySelectorAll('.recipient-box'));
    const counter = document.getElementById('selectedCount');
    const typeCards = Array.from(document.querySelectorAll('.type-card'));
    const titleGroup = document.getElementById('titleGroup');
    const titleInput = document.getElementById('titleInput');
    const descInput = document.getElementById('descInput');
    const descLabelRequired = document.getElementById('descLabelRequired');
    const descLabelOptional = document.getElementById('descLabelOptional');
    const templatePreview = document.getElementById('templatePreview');
    const templatePreviewText = document.getElementById('templatePreviewText');
    const skipWarning = document.getElementById('skipWarning');

    const i18n = {
        countLabel: @json(__('admin_emails.selected_count')),
        selectOne: @json(__('admin_emails.select_at_least_one')),
        sending: @json(__('admin_emails.sending')),
        templates: {
            residency_expiry: @json(__('admin_emails.tpl_residency_subject')."\n\n".__('admin_emails.tpl_residency_body', ['name' => '(:name)', 'date' => '(:date)'])),
            passport_expiry: @json(__('admin_emails.tpl_passport_subject')."\n\n".__('admin_emails.tpl_passport_body', ['name' => '(:name)', 'date' => '(:date)'])),
        },
        colors: { general: '#2563eb', residency_expiry: '#dc2626', passport_expiry: '#d97706' },
    };

    function currentType() {
        const checked = document.querySelector('input[name="message_type"]:checked');
        return checked ? checked.value : 'general';
    }

    function applyType() {
        const type = currentType();
        const isGeneral = type === 'general';

        typeCards.forEach(card => {
            const active = card.dataset.type === type;
            const color = i18n.colors[card.dataset.type];
            card.style.borderColor = active ? color : '#e5e7eb';
            card.style.background = active ? color + '14' : '#fff';
            card.style.boxShadow = active ? '0 1px 6px rgba(0,0,0,0.08)' : 'none';
        });

        titleGroup.style.display = isGeneral ? '' : 'none';
        titleInput.required = isGeneral;
        descInput.required = isGeneral;
        descLabelRequired.style.display = isGeneral ? '' : 'none';
        descLabelOptional.style.display = isGeneral ? 'none' : '';

        templatePreview.style.display = isGeneral ? 'none' : '';
        if (!isGeneral) {
            templatePreviewText.textContent = i18n.templates[type];
        }

        updateSkipWarning();
    }

    function hasDataFor(row, type) {
        if (type === 'residency_expiry') return row.dataset.hasResidency === '1';
        if (type === 'passport_expiry') return row.dataset.hasPassport === '1';
        return true;
    }

    function updateSkipWarning() {
        const type = currentType();
        if (type === 'general') { skipWarning.style.display = 'none'; return; }
        const missing = boxes.filter(b => b.checked && !hasDataFor(b.closest('.user-row'), type)).length;
        skipWarning.style.display = missing > 0 ? '' : 'none';
    }

    function visibleBoxes() {
        return boxes.filter(b => b.closest('.user-row').style.display !== 'none');
    }

    function updateCount() {
        const n = boxes.filter(b => b.checked).length;
        counter.textContent = i18n.countLabel.replace(':count', String(n));
        const vis = visibleBoxes();
        selectAll.checked = vis.length > 0 && vis.every(b => b.checked);
        updateSkipWarning();
    }

    document.querySelectorAll('input[name="message_type"]').forEach(radio => {
        radio.addEventListener('change', applyType);
    });

    selectAll.addEventListener('change', function () {
        visibleBoxes().forEach(b => { b.checked = selectAll.checked; });
        updateCount();
    });

    boxes.forEach(b => b.addEventListener('change', updateCount));

    search.addEventListener('input', function () {
        const q = search.value.trim().toLowerCase();
        rows.forEach(row => {
            row.style.display = (!q || row.dataset.search.includes(q)) ? '' : 'none';
        });
        updateCount();
    });

    form.addEventListener('submit', function (e) {
        if (!boxes.some(b => b.checked)) {
            e.preventDefault();
            alert(i18n.selectOne);
            return;
        }
        const btn = document.getElementById('sendBtn');
        btn.disabled = true;
        btn.textContent = i18n.sending;
    });

    applyType();
    updateCount();
})();
</script>
@endsection
