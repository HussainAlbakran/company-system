<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تنبيه انتهاء الإقامة</title>
</head>
<body style="font-family: Arial, sans-serif; direction: rtl; text-align: right; background: #f5f5f5; padding: 20px;">

    <div style="background: #ffffff; padding: 24px; border-radius: 10px; border: 1px solid #e5e7eb;">
        <h2 style="margin-top: 0; color: #dc2626;">تنبيه انتهاء الإقامة</h2>

        <p>{{ $messageText }}</p>

        <hr style="margin: 20px 0;">

        <p><strong>اسم الموظف:</strong> {{ $employee->name ?? '-' }}</p>
        <p><strong>رقم الموظف:</strong> {{ $employee->employee_number ?? '-' }}</p>
        <p><strong>رقم الإقامة:</strong> {{ $employee->residency_number ?? '-' }}</p>
        <p><strong>تاريخ انتهاء الإقامة:</strong> {{ $employee->residency_expiry_date ?? '-' }}</p>

        <hr style="margin: 20px 0;">

        <p style="color: #b91c1c; font-weight: bold;">
            يرجى مراجعة حالة الإقامة واتخاذ الإجراء اللازم.
        </p>
    </div>

</body>
</html>