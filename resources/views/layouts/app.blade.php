<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construction ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg: #0b1220;
            --surface-1: #121c31;
            --surface-2: #18243b;
            --surface-3: #1f2d47;

            --text: #e8effb;
            --text-muted: #92a6c4;

            --border: rgba(146, 166, 196, 0.20);
            --border-strong: rgba(146, 166, 196, 0.34);

            --blue: #3b82f6;
            --green: #22c55e;
            --purple: #a855f7;
            --red: #ef4444;
            --amber: #f59e0b;

            --radius-lg: 14px;
            --radius-md: 11px;
            --radius-sm: 9px;
            --shadow-soft: 0 10px 26px rgba(0, 0, 0, 0.34);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, Tahoma, Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 10% 8%, #1b2943 0%, transparent 30%),
                radial-gradient(circle at 90% 0%, #1a2945 0%, transparent 28%),
                var(--bg);
        }

        .main-layout { display: flex; min-height: 100vh; direction: ltr; }
        .layout-content { flex: 1; min-width: 0; direction: rtl; }

        .sidebar {
            width: 220px;
            background: linear-gradient(180deg, #080f1c 0%, #0d1528 100%);
            border-left: 1px solid var(--border);
            box-shadow: 5px 0 24px rgba(0, 0, 0, 0.45);
            padding: 12px 9px;
            flex-shrink: 0;
        }

        .brand-box {
            padding: 10px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            background: linear-gradient(180deg, rgba(59,130,246,.14), rgba(59,130,246,.03));
            margin-bottom: 9px;
        }

        .brand-title { margin: 0; font-size: 15px; font-weight: 800; color: #f8fbff; letter-spacing: .2px; }
        .brand-subtitle { margin: 4px 0 0; color: #9fc6ff; font-size: 10.5px; }

        .nav-links { display: grid; gap: 4px; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 9px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: #cfddf2;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid transparent;
            transition: .16s ease;
        }

        .nav-link-icon {
            width: 13px;
            height: 13px;
            border-radius: 4px;
            background: rgba(146,166,196,.30);
            box-shadow: inset 0 0 0 1px rgba(226,232,240,.08);
            flex-shrink: 0;
        }

        .nav-link:hover {
            color: #f7fbff;
            background: rgba(59,130,246,.10);
            border-color: rgba(59,130,246,.28);
        }

        .nav-link.active {
            color: #fff;
            background: linear-gradient(135deg, rgba(59,130,246,.22), rgba(37,99,235,.14));
            border-color: rgba(59,130,246,.50);
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(7, 12, 24, .90);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 9px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .topbar h1 { margin: 0; font-size: 16px; font-weight: 800; color: #f8fbff; }
        .topbar p { margin: 3px 0 0; color: var(--text-muted); font-size: 11px; }

        .topbar-right { display: flex; align-items: center; gap: 6px; }

        .utility-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: rgba(146,166,196,.10);
            position: relative;
        }

        .utility-btn::after {
            content: "";
            width: 10px;
            height: 2px;
            border-radius: 999px;
            background: #dbe8fb;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .user-pill {
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 5px 9px;
            background: rgba(146,166,196,.10);
            min-width: 122px;
        }

        .user-pill .name { font-size: 12px; font-weight: 700; color: #f2f7ff; }
        .user-pill .meta { margin-top: 2px; font-size: 10px; color: #9fc6ff; }

        .page-content { padding: 11px; }

        .page-card, .card {
            background: linear-gradient(180deg, rgba(24,36,59,.96), rgba(17,28,47,.96));
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 11px;
        }

        .page-header {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            flex-wrap: wrap;
        }

        .page-title { margin: 0; font-size: 20px; font-weight: 800; color: #f8fbff; }
        .page-subtitle { margin: 3px 0 0; color: var(--text-muted); font-size: 11px; }

        .stats-grid, .form-grid, .details-grid {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(auto-fit, minmax(158px, 1fr));
        }

        .stat-card, .detail-box {
            background: rgba(146,166,196,.06);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 9px;
        }

        .stat-label {
            color: #a9bdd8;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .55px;
        }

        .stat-value {
            margin-top: 2px;
            font-size: 20px;
            line-height: 1.15;
            font-weight: 800;
            color: #f8fbff;
        }

        .stat-note { margin-top: 3px; font-size: 10px; color: var(--text-muted); }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.03);
        }

        table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            background: rgba(8,13,27,.56);
        }

        th, td {
            padding: 8px;
            border-bottom: 1px solid var(--border);
            text-align: right;
            color: #dce8fb;
            font-size: 11.5px;
        }

        th {
            background: rgba(30,41,59,.72);
            color: #bdd2f0;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .55px;
        }

        tr:hover td { background: rgba(59,130,246,.07); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            border: 0;
            border-radius: 8px;
            padding: 7px 9px;
            text-decoration: none;
            cursor: pointer;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
        }

        .btn-sm { padding: 5px 7px; font-size: 10px; }
        .btn-primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .btn-success { background: linear-gradient(135deg, #22c55e, #15803d); }
        .btn-warning { background: linear-gradient(135deg, #f59e0b, #b45309); }
        .btn-danger { background: linear-gradient(135deg, #ef4444, #b91c1c); }
        .btn-secondary {
            background: rgba(146,166,196,.18);
            border: 1px solid var(--border-strong);
            color: #e0e9fa;
        }

        .badge {
            display: inline-flex;
            padding: 3px 7px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }

        .badge-green { background: rgba(34,197,94,.20); color: #8bf0b0; }
        .badge-red { background: rgba(239,68,68,.20); color: #f9a3a3; }
        .badge-orange { background: rgba(245,158,11,.20); color: #f6cf7a; }
        .badge-blue { background: rgba(59,130,246,.20); color: #a4c9ff; }
        .badge-gray { background: rgba(146,166,196,.20); color: #cdd9ee; }

        .alert-success, .alert-danger, .alert-error {
            margin-bottom: 8px;
            border-radius: 8px;
            padding: 8px 9px;
            border: 1px solid transparent;
            font-size: 11px;
        }

        .alert-success { background: rgba(34,197,94,.16); border-color: rgba(34,197,94,.34); color: #8bf0b0; }
        .alert-danger, .alert-error { background: rgba(239,68,68,.16); border-color: rgba(239,68,68,.34); color: #f9a3a3; }

        .actions-row, .actions, .form-actions { display: flex; gap: 5px; flex-wrap: wrap; }
        .empty-row { text-align: center; color: var(--text-muted); }
        .detail-box-full, .form-group-full { grid-column: 1 / -1; }

        label { font-size: 11px; font-weight: 700; color: #c9d8ee; margin-bottom: 4px; display: block; }

        input, select, textarea {
            width: 100%;
            border: 1px solid rgba(146,166,196,.30);
            border-radius: 8px;
            padding: 8px 9px;
            font-size: 12px;
            background: rgba(15,23,42,.72);
            color: #eef4ff;
        }

        input::placeholder, textarea::placeholder { color: #91a3c0; }
        textarea { min-height: 74px; resize: vertical; }

        @media (max-width: 992px) {
            .main-layout { flex-direction: column; }
            .sidebar { width: 100%; }
            .page-content { padding: 9px; }
            .topbar { padding: 8px 10px; }
            table { min-width: 600px; }
        }
    </style>
</head>
<body>
    <div class="main-layout">
        @include('layouts.navigation')

        <div class="layout-content">
            <div class="topbar">
                <div>
                    <h1>@yield('page_title', 'Construction ERP Dashboard')</h1>
                    <p>@yield('page_subtitle', 'Advanced Concrete Company')</p>
                </div>

                <div class="topbar-right">
                    <span class="utility-btn" aria-hidden="true"></span>
                    <span class="utility-btn" aria-hidden="true"></span>
                    <span class="utility-btn" aria-hidden="true"></span>

                    @auth
                        <div class="user-pill">
                            <div class="name">{{ auth()->user()->name }}</div>
                            <div class="meta">{{ auth()->user()->role }}</div>
                        </div>
                    @endauth
                </div>
            </div>

            <div class="page-content">
                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert-danger">{{ session('error') }}</div>
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
