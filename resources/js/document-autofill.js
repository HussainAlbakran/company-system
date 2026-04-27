function bootstrapDocumentAutoFill() {
    const forms = document.querySelectorAll('form[data-autofill-form-key]');

    forms.forEach((form) => {
        const fileInput = form.querySelector('[data-autofill-document-input]');
        const statusEl = form.querySelector('[data-autofill-status]');
        const endpoint = form.dataset.autofillEndpoint;
        const formKey = form.dataset.autofillFormKey;

        if (!fileInput || !endpoint || !formKey) {
            return;
        }

        const panel = createPreviewPanel();
        if (statusEl) {
            statusEl.insertAdjacentElement('afterend', panel.wrapper);
        } else {
            fileInput.insertAdjacentElement('afterend', panel.wrapper);
        }

        fileInput.addEventListener('change', async () => {
            const file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
            if (!file) {
                return;
            }

            setStatus(statusEl, 'info', 'جاري تحليل الملف...');
            panel.wrapper.style.display = 'none';
            panel.error.textContent = '';
            clearHighlightedFields(form);

            const formData = new FormData();
            formData.append('document', file);
            formData.append('form_key', formKey);

            const csrf = form.querySelector('input[name="_token"]');
            if (csrf) {
                formData.append('_token', csrf.value);
            }

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { Accept: 'application/json' },
                    body: formData,
                });

                const payload = await response.json();
                if (!response.ok) {
                    setStatus(statusEl, 'error', payload.message || 'فشل تحليل الملف.');
                    return;
                }

                const fields = payload.fields || {};
                const keys = Object.keys(fields);
                if (keys.length === 0) {
                    setStatus(statusEl, 'error', 'لم يتم العثور على بيانات قابلة للتعبئة من الملف.');
                    return;
                }

                renderPreviewRows(form, panel.rows, fields);
                panel.wrapper.style.display = 'block';
                setStatus(statusEl, 'success', 'تم استخراج البيانات. راجع المعاينة ثم اختر طريقة التطبيق.');

                panel.applyButton.onclick = () => {
                    const mode = panel.overwrite.checked ? 'overwrite' : 'empty-only';
                    const result = applyFields(form, fields, mode);

                    if (result.applied === 0) {
                        setStatus(statusEl, 'error', 'لم يتم تطبيق أي حقل. قد تكون الحقول ممتلئة مسبقًا.');
                    } else {
                        setStatus(
                            statusEl,
                            'success',
                            `تم تطبيق ${result.applied} حقل(حقول) بنجاح. راجع القيم يدويًا قبل الحفظ.`
                        );
                    }

                    panel.error.textContent = '';
                };

                panel.cancelButton.onclick = () => {
                    panel.wrapper.style.display = 'none';
                    panel.error.textContent = '';
                    setStatus(statusEl, 'info', 'تم إلغاء التطبيق. يمكنك تعديل البيانات يدويًا.');
                    clearHighlightedFields(form);
                };

                panel.closeButton.onclick = () => {
                    panel.wrapper.style.display = 'none';
                    panel.error.textContent = '';
                };
            } catch (error) {
                setStatus(statusEl, 'error', 'حدث خطأ أثناء تحليل الملف.');
            }
        });
    });
}

function createPreviewPanel() {
    const wrapper = document.createElement('div');
    wrapper.style.display = 'none';
    wrapper.style.marginTop = '10px';
    wrapper.style.padding = '10px';
    wrapper.style.border = '1px solid rgba(148, 163, 184, 0.35)';
    wrapper.style.borderRadius = '8px';
    wrapper.style.background = 'rgba(15, 23, 42, 0.55)';

    const title = document.createElement('div');
    title.textContent = 'معاينة القيم المستخرجة';
    title.style.fontWeight = '700';
    title.style.marginBottom = '8px';
    title.style.color = '#e2e8f0';

    const rows = document.createElement('div');
    rows.style.display = 'grid';
    rows.style.gridTemplateColumns = '1fr';
    rows.style.gap = '6px';

    const modeRow = document.createElement('div');
    modeRow.style.marginTop = '10px';
    modeRow.style.display = 'flex';
    modeRow.style.flexWrap = 'wrap';
    modeRow.style.gap = '12px';

    const emptyOnly = document.createElement('input');
    emptyOnly.type = 'radio';
    emptyOnly.name = `autofill-mode-${Math.random().toString(36).slice(2)}`;
    emptyOnly.checked = true;
    emptyOnly.value = 'empty-only';

    const emptyOnlyLabel = document.createElement('label');
    emptyOnlyLabel.style.display = 'inline-flex';
    emptyOnlyLabel.style.alignItems = 'center';
    emptyOnlyLabel.style.gap = '6px';
    emptyOnlyLabel.style.color = '#cbd5e1';
    emptyOnlyLabel.appendChild(emptyOnly);
    emptyOnlyLabel.appendChild(document.createTextNode('تعبئة الحقول الفارغة فقط'));

    const overwrite = document.createElement('input');
    overwrite.type = 'radio';
    overwrite.name = emptyOnly.name;
    overwrite.value = 'overwrite';

    const overwriteLabel = document.createElement('label');
    overwriteLabel.style.display = 'inline-flex';
    overwriteLabel.style.alignItems = 'center';
    overwriteLabel.style.gap = '6px';
    overwriteLabel.style.color = '#cbd5e1';
    overwriteLabel.appendChild(overwrite);
    overwriteLabel.appendChild(document.createTextNode('الكتابة فوق الحقول المطابقة'));

    modeRow.appendChild(emptyOnlyLabel);
    modeRow.appendChild(overwriteLabel);

    const actions = document.createElement('div');
    actions.style.marginTop = '10px';
    actions.style.display = 'flex';
    actions.style.gap = '8px';
    actions.style.flexWrap = 'wrap';

    const applyButton = document.createElement('button');
    applyButton.type = 'button';
    applyButton.textContent = 'تطبيق القيم';
    applyButton.className = 'btn btn-success btn-sm';

    const cancelButton = document.createElement('button');
    cancelButton.type = 'button';
    cancelButton.textContent = 'إلغاء';
    cancelButton.className = 'btn btn-secondary btn-sm';

    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.textContent = 'إخفاء المعاينة';
    closeButton.className = 'btn btn-secondary btn-sm';

    const error = document.createElement('div');
    error.style.marginTop = '8px';
    error.style.color = '#fca5a5';
    error.style.fontSize = '12px';

    actions.appendChild(applyButton);
    actions.appendChild(cancelButton);
    actions.appendChild(closeButton);

    wrapper.appendChild(title);
    wrapper.appendChild(rows);
    wrapper.appendChild(modeRow);
    wrapper.appendChild(actions);
    wrapper.appendChild(error);

    return { wrapper, rows, emptyOnly, overwrite, applyButton, cancelButton, closeButton, error };
}

function renderPreviewRows(form, container, fields) {
    container.innerHTML = '';
    Object.keys(fields).forEach((name) => {
        const input = form.querySelector(`[name="${name}"]`);
        const row = document.createElement('div');
        row.style.display = 'grid';
        row.style.gridTemplateColumns = '180px 1fr';
        row.style.gap = '8px';
        row.style.fontSize = '12px';

        const key = document.createElement('div');
        key.textContent = name;
        key.style.color = '#93c5fd';
        key.style.fontWeight = '700';

        const value = document.createElement('div');
        value.textContent = String(fields[name]);
        value.style.color = input ? '#e2e8f0' : '#94a3b8';
        if (!input) {
            value.textContent += ' (لا يوجد حقل مطابق في النموذج)';
        }

        row.appendChild(key);
        row.appendChild(value);
        container.appendChild(row);
    });
}

function applyFields(form, fields, mode) {
    let applied = 0;

    Object.keys(fields).forEach((name) => {
        const input = form.querySelector(`[name="${name}"]`);
        if (!input) {
            return;
        }

        const hasValue = (input.value || '').trim() !== '';
        if (mode === 'empty-only' && hasValue) {
            return;
        }

        input.value = fields[name];
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
        highlightField(input);
        applied += 1;
    });

    return { applied };
}

function highlightField(input) {
    input.classList.add('autofill-highlight');
    input.style.outline = '2px solid rgba(34, 197, 94, 0.85)';
    input.style.outlineOffset = '1px';
    input.style.backgroundColor = 'rgba(34, 197, 94, 0.08)';
}

function clearHighlightedFields(form) {
    form.querySelectorAll('.autofill-highlight').forEach((input) => {
        input.classList.remove('autofill-highlight');
        input.style.outline = '';
        input.style.outlineOffset = '';
        input.style.backgroundColor = '';
    });
}

function setStatus(statusEl, type, message) {
    if (!statusEl) {
        return;
    }

    statusEl.textContent = message;
    if (type === 'error') {
        statusEl.style.color = '#fca5a5';
        return;
    }

    if (type === 'success') {
        statusEl.style.color = '#86efac';
        return;
    }

    statusEl.style.color = '#94a3b8';
}

document.addEventListener('DOMContentLoaded', bootstrapDocumentAutoFill);
