<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شركة التقدم للخرسانة الجاهزة</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
    --bg: #f8fafc;          /* خلفية خفيفة */
    --card: #ffffff;        /* الكروت أبيض */
    --text: #111827;        /* نص أسود مريح */
    --muted: #6b7280;       /* رمادي للنص الثانوي */
    --border: #e5e7eb;      /* حدود خفيفة */

    --primary: #111827;     /* أسود */
    --success: #374151;     /* رمادي غامق */
    --warning: #1f1d1d;     /* رمادي */
    --danger: #1f2937;      /* أسود غامق */

    --purple: #374151;
    --teal: #4b5563;
    --orange: #000000;
    --gray: #000000;
}
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.7;
        }

        .main-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #000000 0%, #313131 100%);
            color: white;
            padding: 24px 18px;
            box-shadow: 0 0 18px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }

        .brand-box {
            margin-bottom: 28px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.12);
        }

        .brand-title {
            font-size: 22px;
            font-weight: 800;
            line-height: 1.7;
            margin: 0 0 8px;
        }

        .brand-subtitle {
            font-size: 13px;
            color: #cbd5e1;
            margin: 0;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .nav-link {
            display: block;
            padding: 12px 14px;
            border-radius: 12px;
            color: #e5e7eb;
            text-decoration: none;
            font-weight: 700;
            transition: 0.25s ease;
            background: transparent;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }

        .nav-link.active {
            background: var(--primary);
            color: #ffffff;
        }

        .layout-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 18px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .topbar-title h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: var(--text);
        }

        .topbar-title p {
            margin: 6px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .topbar-user {
            text-align: left;
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 12px 16px;
            min-width: 240px;
        }

        .topbar-user .name {
            font-weight: 800;
            color: var(--text);
            font-size: 15px;
        }

        .topbar-user .role {
            color: var(--muted);
            font-size: 13px;
            margin-top: 4px;
        }

        .page-content {
            padding: 28px;
            width: 100%;
            max-width: 100%;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .alert-danger,
        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .page-card,
        .card {
            background: var(--card);
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            overflow: hidden;
        }

        .page-title {
            margin: 0 0 8px;
            font-size: 30px;
            font-weight: 800;
            color: var(--text);
        }

        .page-header {
            margin-bottom: 22px;
        }

        .page-header h2 {
            margin: 0 0 8px;
            font-size: 28px;
            font-weight: 800;
            color: var(--text);
        }

        .page-header p {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 11px 16px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 13px;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-warning { background: var(--warning); color: white; }
        .btn-danger  { background: var(--danger); color: white; }
        .btn-dark    { background: #000000; color: white; }
        .btn-purple  { background: var(--purple); color: white; }
        .btn-teal    { background: var(--teal); color: white; }
        .btn-orange  { background: var(--orange); color: white; }
        .btn-secondary { background: #000000; color: #ffffff; }
        .btn-blue { background: #000000; color: white; }
        .btn-green { background: #000000; color: white; }
        .btn-red { background: #000000; color: white; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
            margin-top: 18px;
        }

        .stat-card,
        .stat-box {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            min-height: 120px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 800;
            color: var(--text);
            line-height: 1.3;
            word-break: break-word;
        }

        .stat-note {
            margin-top: 8px;
            color: var(--muted);
            font-size: 13px;
        }

        .quick-links,
        .actions,
        .form-actions,
        .actions-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .detail-box {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
            min-height: 90px;
        }

        .detail-box strong {
            display: block;
            margin-bottom: 8px;
            color: #374151;
        }

        .detail-box-full,
        .form-group-full,
        .form-group.full {
            grid-column: 1 / -1;
        }

        .table-wrap,
        .table-wrapper {
            overflow-x: auto;
            margin-top: 18px;
            width: 100%;
        }

        table,
        .data-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            background: white;
        }

        table th,
        table td,
        .data-table th,
        .data-table td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--border);
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        table th,
        .data-table th {
            background: #f9fafb;
            color: #374151;
            font-weight: 800;
            font-size: 14px;
        }

        table tr:hover td,
        .data-table tr:hover td {
            background: #f8fafc;
        }

        .empty-row,
        .empty-state {
            text-align: center;
            color: var(--muted);
            padding: 24px;
            font-weight: 700;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #374151;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        input[type="time"],
        input[type="file"],
        input[type="password"],
        select,
        textarea {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            background: white;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-green { background: #dcfce7; color: #000000; }
        .badge-red { background: #fee2e2; color: #000000; }
        .badge-orange { background: #ffedd5; color: #000000; }
        .badge-blue { background: #dbeafe; color: #000000; }
        .badge-teal { background: #ccfbf1; color: #000000; }
        .badge-gray { background: #f3f4f6; color: #000000; }

        .employee-link {
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 700;
        }

        .employee-link:hover {
            text-decoration: underline;
        }

        .timeline {
            position: relative;
            margin-top: 20px;
            padding-right: 20px;
        }

        .timeline::before {
            content: "";
            position: absolute;
            top: 0;
            right: 12px;
            width: 2px;
            height: 100%;
            background: #e5e7eb;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 24px;
            padding-right: 35px;
        }

        .timeline-dot {
            position: absolute;
            right: 4px;
            top: 10px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #2563eb;
            border: 3px solid #dbeafe;
        }

        .timeline-content {
            background: #f9fafb;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px;
        }

        .pagination {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        @media (max-width: 1100px) {
            .sidebar {
                width: 240px;
            }

            .page-content {
                padding: 20px;
            }
        }

        @media (max-width: 900px) {
            .main-layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .topbar-user {
                text-align: right;
                width: 100%;
            }

            .page-content {
                padding: 16px;
            }

            .page-card,
            .card {
                padding: 16px;
            }

            .page-title {
                font-size: 24px;
            }

            .page-header h2 {
                font-size: 22px;
            }

            .stat-value {
                font-size: 24px;
            }

            table,
            .data-table {
                min-width: 700px;
            }
        }
    </style>
</head>
<body>
    <div class="main-layout">
        @include('layouts.navigation')

        <div class="layout-content">
            <div class="topbar">
                <div class="topbar-title">
                    <h1>شركة التقدم للخرسانة الجاهزة</h1>
                    <p>نظام إدارة الشركة</p>
                </div>

                @auth
                    <div class="topbar-user">
                        <div class="name">{{ auth()->user()->name }}</div>
                        <div class="role">
                            الدور: {{ auth()->user()->role }} |
                            الحالة: {{ auth()->user()->approval_status ?? auth()->user()->status ?? 'approved' }}
                        </div>
                    </div>
                @endauth
            </div>

            <div class="page-content">
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
                    <div class="alert-error">
                        <ul style="margin:0; padding-right:18px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (isset($header))
                    <div style="margin-bottom: 20px;">
                        {{ $header }}
                    </div>
                @endif

                @if (isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        </div>
    </div>
</body>
</html>