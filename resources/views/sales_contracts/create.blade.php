@extends('layouts.app')

@section('content')

<div class="container">
    <h2 class="mb-4">إضافة عقد جديد</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sales-contracts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">

            <div class="col-md-6 mb-3">
                <label>رقم العقد</label>
                <input type="text" name="contract_no" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>تاريخ العقد</label>
                <input type="date" name="contract_date" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>اسم العميل</label>
                <input type="text" name="client_name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>المقاول الرئيسي</label>
                <input type="text" name="main_contractor" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label>اسم المشروع</label>
                <input type="text" name="project_name" class="form-control" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>موقع المشروع</label>
                <input type="text" name="project_location" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label>قيمة المشروع</label>
                <input type="number" step="0.01" name="project_value" id="project_value" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>تاريخ البداية المتوقع</label>
                <input type="date" name="expected_start_date" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>تاريخ البداية الفعلي</label>
                <input type="date" name="actual_start_date" class="form-control">
            </div>

            <div class="col-md-4 mb-3">
                <label>تاريخ النهاية المتوقع</label>
                <input type="date" name="expected_end_date" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <label>طريقة الدفع</label>
                <select name="payment_type" id="payment_type" class="form-control" onchange="togglePaymentFields()" required>
                    <option value="full">دفع كامل</option>
                    <option value="installments">دفعات</option>
                </select>
            </div>

            <div class="col-md-6 mb-3 full-payment-field">
                <label>المبلغ المدفوع</label>
                <input type="number" step="0.01" name="full_payment_amount" class="form-control" placeholder="أدخل المبلغ المدفوع">
            </div>

            <div class="col-md-6 mb-3 installment-field" style="display:none;">
                <label>اسم الدفعة الأولى</label>
                <input type="text" name="first_payment_title" class="form-control" placeholder="مثال: دفعة أولى">
            </div>

            <div class="col-md-6 mb-3 installment-field" style="display:none;">
                <label>نسبة الدفعة الأولى %</label>
                <input type="number" step="0.01" name="first_payment_percentage" id="first_payment_percentage" class="form-control" placeholder="مثال: 30" oninput="calculateFirstPaymentAmount()">
            </div>

            <div class="col-md-6 mb-3 installment-field" style="display:none;">
                <label>مبلغ الدفعة الأولى</label>
                <input type="number" step="0.01" name="first_payment_amount" id="first_payment_amount" class="form-control" placeholder="يُحسب تلقائيًا">
            </div>

            <div class="col-md-6 mb-3 installment-field" style="display:none;">
                <label>تاريخ استحقاق الدفعة الأولى</label>
                <input type="date" name="first_payment_due_date" class="form-control">
            </div>

            <div class="col-md-12 mb-3">
                <label>وصف المشروع</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
            </div>

            <div class="col-md-12 mb-3">
                <label>ملف العقد (PDF أو صورة)</label>
                <input type="file" name="contract_file" class="form-control">
            </div>

        </div>

        <button type="submit" class="btn btn-primary">
            حفظ العقد
        </button>

        <a href="{{ route('sales-contracts.index') }}" class="btn btn-secondary">
            رجوع
        </a>

    </form>
</div>

<script>
    function togglePaymentFields() {
        const paymentType = document.getElementById('payment_type').value;
        const installmentFields = document.querySelectorAll('.installment-field');
        const fullPaymentFields = document.querySelectorAll('.full-payment-field');

        if (paymentType === 'installments') {
            installmentFields.forEach(field => field.style.display = 'block');
            fullPaymentFields.forEach(field => field.style.display = 'none');
        } else {
            installmentFields.forEach(field => field.style.display = 'none');
            fullPaymentFields.forEach(field => field.style.display = 'block');
        }
    }

    function calculateFirstPaymentAmount() {
        const projectValue = parseFloat(document.getElementById('project_value').value) || 0;
        const percentage = parseFloat(document.getElementById('first_payment_percentage').value) || 0;
        const amount = (projectValue * percentage) / 100;

        document.getElementById('first_payment_amount').value = amount.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function () {
        togglePaymentFields();
    });
</script>

@endsection