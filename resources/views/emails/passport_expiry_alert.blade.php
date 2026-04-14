<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تنبيه انتهاء الجواز</title>
</head>
<body style="font-family: Arial, sans-serif; direction: rtl; text-align: right; background: #f5f5f5; padding: 20px;">

    <div style="background: #ffffff; padding: 24px; border-radius: 10px; border: 1px solid #e5e7eb;">
        
        <h2 style="margin-top: 0; color: #dc2626;">
            تنبيه انتهاء الجواز
        </h2>

        <p>
            {{ $messageText }}
        </p>

        <hr style="margin: 20px 0;">

        <p><strong>اسم الموظف:</strong> {{ $employee->name ?? '-' }}</p>
        <p><strong>رقم الموظف:</strong> {{ $employee->employee_number ?? '-' }}</p>
        <p><strong>رقم الجواز:</strong> {{ $employee->passport_number ?? '-' }}</p>
        <p><strong>تاريخ انتهاء الجواز:</strong> {{ $employee->passport_expiry_date ?? '-' }}</p>

        <hr style="margin: 20px 0;">

        <p style="color: #b91c1c; font-weight: bold;">
            يرجى تجديد الجواز قبل انتهاء صلاحيته 
        </p>

    </div>

</body>
</html>