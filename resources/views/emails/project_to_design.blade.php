<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>مشروع جديد للتصاميم</title>
</head>
<body style="font-family: Arial; direction: rtl; text-align: right; background:#f5f5f5; padding:20px;">

    <div style="background:#ffffff; padding:20px; border-radius:10px;">

        <h2 style="color:#2563eb;">📌 تم تحويل مشروع جديد إلى قسم التصاميم</h2>

        <p>تم تسجيل دفعة للعقد، وتم تحويل المشروع إلى قسم التصاميم.</p>

        <hr>

        <p><strong>رقم العقد:</strong> {{ $contract->contract_no }}</p>

        <p><strong>اسم المشروع:</strong> {{ $contract->project_name }}</p>

        <p><strong>اسم العميل:</strong> {{ $contract->client_name }}</p>

        <p><strong>قيمة المشروع:</strong> {{ number_format($contract->project_value ?? 0, 2) }}</p>

        <hr>

        <p style="color:#16a34a; font-weight:bold;">
            الرجاء البدء في العمل على المشروع.
        </p>

    </div>

</body>
</html>