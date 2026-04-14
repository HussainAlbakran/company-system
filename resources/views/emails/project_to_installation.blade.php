<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>مشروع جاهز للتركيب</title>
</head>
<body style="font-family: Arial, sans-serif; direction: rtl; text-align: right; background: #f5f5f5; padding: 20px;">

    <div style="background: #ffffff; padding: 24px; border-radius: 10px; border: 1px solid #e5e7eb;">
        
        <h2 style="margin-top: 0; color: #059669;">🛠 مشروع جاهز لمرحلة التركيبات</h2>

        <p>
            تم تجهيز المشروع من قبل قسم التصاميم وهو الآن جاهز لمرحلة التركيبات.
        </p>

        <hr style="margin: 20px 0;">

        <p><strong>رقم المشروع:</strong> {{ $project->project_code ?? '-' }}</p>
        <p><strong>اسم المشروع:</strong> {{ $project->name ?? '-' }}</p>
        <p><strong>اسم العميل:</strong> {{ $project->client_name ?? '-' }}</p>
        <p><strong>المقاول الرئيسي:</strong> {{ $project->main_contractor ?? '-' }}</p>

        <hr style="margin: 20px 0;">

        <p style="color: #2563eb; font-weight: bold;">
            يرجى البدء في تجهيز فريق التركيب وتنفيذ المشروع.
        </p>

    </div>

</body>
</html>