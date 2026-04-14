<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>طلب مشتريات جديد</title>
</head>
<body style="font-family: Arial, sans-serif; direction: rtl; text-align: right; background: #f5f5f5; padding: 20px;">

    <div style="background: #ffffff; padding: 24px; border-radius: 10px; border: 1px solid #e5e7eb;">
        
        <h2 style="margin-top: 0; color: #dc2626;">🧾 طلب مشتريات جديد</h2>

        <p>
            يوجد مشروع جديد يحتاج إلى تجهيز المواد والمشتريات اللازمة لبدء التنفيذ.
        </p>

        <hr style="margin: 20px 0;">

        <p><strong>رقم المشروع:</strong> {{ $project->project_code ?? '-' }}</p>
        <p><strong>اسم المشروع:</strong> {{ $project->name ?? '-' }}</p>
        <p><strong>اسم العميل:</strong> {{ $project->client_name ?? '-' }}</p>
        <p><strong>المقاول الرئيسي:</strong> {{ $project->main_contractor ?? '-' }}</p>

        <hr style="margin: 20px 0;">

        <p style="color: #b91c1c; font-weight: bold;">
            يرجى مراجعة المشروع وتوفير جميع المواد المطلوبة في أقرب وقت ممكن.
        </p>

    </div>

</body>
</html>