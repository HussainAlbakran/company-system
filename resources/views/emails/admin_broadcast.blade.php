<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>{{ $emailTitle }}</title>
</head>
<body style="font-family: Arial; direction: rtl; text-align: right; background:#f5f5f5; padding:20px;">

    <div style="background:#ffffff; padding:24px; border-radius:10px; max-width:640px; margin:0 auto; border-top:4px solid {{ $accentColor }};">

        <h2 style="color:{{ $accentColor }}; margin-top:0;">{{ $emailTitle }}</h2>

        <hr style="border:none; border-top:1px solid #e5e7eb;">

        <div style="color:#111827; font-size:15px; line-height:1.9; white-space:pre-line;">{{ $emailBody }}</div>

        @if(!empty($details))
            <table style="width:100%; margin-top:20px; border-collapse:collapse; background:#f9fafb; border-radius:8px;">
                @foreach($details as $label => $value)
                    <tr>
                        <td style="padding:10px 14px; border-bottom:1px solid #e5e7eb; color:#374151; font-weight:bold; width:40%;">{{ $label }}</td>
                        <td style="padding:10px 14px; border-bottom:1px solid #e5e7eb; color:#111827;">{{ $value }}</td>
                    </tr>
                @endforeach
            </table>
        @endif

        <hr style="border:none; border-top:1px solid #e5e7eb; margin-top:24px;">

        <p style="color:#6b7280; font-size:12px; margin-bottom:0;">
            {{ config('app.name') }}
        </p>

    </div>

</body>
</html>
