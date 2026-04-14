 @extends('layouts.app')

@section('content')

<div class="page-card">

    <div class="page-header">
        <h2>تفاصيل العقد</h2>
        <p>عرض كامل لبيانات العقد والمشروع المرتبط به</p>
    </div>

    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-danger">
            <ul style="margin:0; padding-right:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="actions-row" style="margin-bottom: 20px;">
        <a href="{{ route('sales-contracts.index') }}" class="btn btn-secondary">رجوع</a>
        <a href="{{ route('sales-contracts.edit', $contract->id) }}" class="btn btn-warning">تعديل</a>
        <button type="button" class="btn btn-primary" onclick="togglePaymentForm()">
            ➕ إضافة دفعة
        </button>
    </div>

    <div class="details-grid">

        <div class="detail-box">
            <strong>رقم العقد</strong>
            <div>{{ $contract->contract_no }}</div>
        </div>

        <div class="detail-box">
            <strong>تاريخ العقد</strong>
            <div>{{ $contract->contract_date }}</div>
        </div>

        <div class="detail-box">
            <strong>اسم العميل</strong>
            <div>{{ $contract->client_name }}</div>
        </div>

        <div class="detail-box">
            <strong>المقاول الرئيسي</strong>
            <div>{{ $contract->main_contractor ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>اسم المشروع</strong>
            <div>{{ $contract->project_name }}</div>
        </div>

        <div class="detail-box">
            <strong>موقع المشروع</strong>
            <div>{{ $contract->project_location ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>قيمة المشروع</strong>
            <div>{{ number_format($contract->project_value ?? 0, 2) }}</div>
        </div>

        <div class="detail-box">
            <strong>مدة المشروع</strong>
            <div>{{ $contract->project_duration ? $contract->project_duration . ' يوم' : '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>تاريخ البداية المتوقع</strong>
            <div>{{ $contract->expected_start_date ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>تاريخ البداية الفعلي</strong>
            <div>{{ $contract->actual_start_date ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>تاريخ النهاية المتوقع</strong>
            <div>{{ $contract->expected_end_date ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>حالة العقد</strong>
            <div>
                <span class="badge badge-green">{{ $contract->status }}</span>
            </div>
        </div>

        <div class="detail-box">
            <strong>المرحلة الحالية</strong>
            <div>
                @if($contract->project)
                    <span class="badge badge-blue">{{ $contract->project->current_stage }}</span>
                @else
                    -
                @endif
            </div>
        </div>

        <div class="detail-box">
            <strong>طريقة الدفع</strong>
            <div>
                @if($contract->payment_type === 'full')
                    دفع كامل
                @elseif($contract->payment_type === 'installments')
                    دفعات
                @else
                    -
                @endif
            </div>
        </div>

        <div class="detail-box">
            <strong>إجمالي المدفوع</strong>
            <div>{{ number_format($contract->total_paid ?? 0, 2) }}</div>
        </div>

        <div class="detail-box">
            <strong>المتبقي</strong>
            <div>{{ number_format($contract->remaining_amount ?? 0, 2) }}</div>
        </div>

        <div class="detail-box">
            <strong>هل تم تسجيل أول دفعة؟</strong>
            <div>
                @if($contract->hasFirstPayment())
                    <span class="badge badge-green">نعم</span>
                @else
                    <span class="badge badge-gray">لا</span>
                @endif
            </div>
        </div>

        <div class="detail-box">
            <strong>رقم المشروع</strong>
            <div>{{ $contract->project->project_code ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>منشئ العقد</strong>
            <div>{{ $contract->creator->name ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>وصف المشروع</strong>
            <div>{{ $contract->description ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>ملاحظات</strong>
            <div>{{ $contract->notes ?? '-' }}</div>
        </div>

        <div class="detail-box detail-box-full">
            <strong>ملف العقد</strong>
            <div>
                @if($contract->contract_file)
                    <a href="{{ asset('storage/' . $contract->contract_file) }}" target="_blank" class="btn btn-primary">
                        فتح ملف العقد
                    </a>
                @else
                    لا يوجد ملف مرفوع
                @endif
            </div>
        </div>

    </div>
</div>

@if($contract->payment_type === 'installments')
<div class="page-card" style="margin-top:24px;">
    <div class="page-header">
        <h2>بيانات الدفعة الأولى</h2>
    </div>

    <div class="details-grid">
        <div class="detail-box">
            <strong>اسم الدفعة الأولى</strong>
            <div>{{ $contract->first_payment_title ?? '-' }}</div>
        </div>

        <div class="detail-box">
            <strong>نسبة الدفعة الأولى</strong>
            <div>{{ $contract->first_payment_percentage ?? '-' }}%</div>
        </div>

        <div class="detail-box">
            <strong>مبلغ الدفعة الأولى</strong>
            <div>{{ number_format($contract->first_payment_amount ?? 0, 2) }}</div>
        </div>

        <div class="detail-box">
            <strong>تاريخ استحقاق الدفعة الأولى</strong>
            <div>{{ $contract->first_payment_due_date ?? '-' }}</div>
        </div>
    </div>
</div>
@endif

<div class="page-card" style="margin-top:24px;">
    <div class="page-header">
        <h2>إضافة دفعة</h2>
        <p>العقد ينتقل إلى التصاميم إذا تم دفع المبلغ كامل أو تم تسجيل الدفعة الأولى</p>
    </div>

    <form id="paymentForm" action="{{ route('contract-payments.store', $contract->id) }}" method="POST" style="display:none;">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label>المبلغ</label>
                <input type="number" step="0.01" name="amount" class="form-control" required>
            </div>

            <div class="form-group">
                <label>تاريخ الدفع</label>
                <input type="date" name="payment_date" class="form-control" required>
            </div>

            <div class="form-group form-group-full">
                <label>ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">حفظ الدفعة</button>
        </div>
    </form>
</div>

<div class="page-card" style="margin-top:24px;">
    <div class="page-header">
        <h2>سجل الدفعات</h2>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>نوع الدفعة</th>
                    <th>المبلغ</th>
                    <th>تاريخ الدفع</th>
                    <th>ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contract->payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>
                            @if($payment->payment_type === 'full')
                                دفع كامل
                            @else
                                دفعة
                            @endif
                        </td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_date }}</td>
                        <td>{{ $payment->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-row">لا توجد دفعات مسجلة حتى الآن</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function togglePaymentForm() {
    const form = document.getElementById('paymentForm');
    form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}
</script>

@endsection