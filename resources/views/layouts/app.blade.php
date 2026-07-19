<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-soft: #f2f4f7;
            --bg: #f2f4f7;
            --bg-card: #f8fafc;
            --bg-input: #ffffff;

            --page-text: #111827;
            --page-text-muted: #4b5563;

            --surface-card: #f8fafc;
            --surface-card-2: #eef1f5;
            --surface-1: #f8fafc;
            --surface-2: #eef1f5;
            --surface-3: #e5e9ef;

            --text: #111827;
            --text-muted: #4b5563;

            --text-black: #111827;
            --text-blue: #1d4ed8;
            --text-red: #dc2626;
            --text-green: #15803d;
            --text-orange: #c2410c;

            --border: #000000;
            --border-strong: #000000;
            --border-page: #000000;

            --blue: #2563eb;
            --green: #16a34a;
            --purple: #7c3aed;
            --red: #dc2626;
            --amber: #d97706;

            --radius-lg: 14px;
            --radius-md: 11px;
            --radius-sm: 9px;
            --shadow-soft: 0 10px 26px rgba(0, 0, 0, 0.34);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, Tahoma, Arial, sans-serif;
            color: var(--page-text);
            background: var(--bg);
        }

        .main-layout { display: flex; min-height: 100vh; direction: ltr; position: relative; z-index: 1; }
        .main-layout.sidebar-collapsed .sidebar {
            width: 0;
            padding: 0;
            border-left: 0;
            overflow: hidden;
            box-shadow: none;
        }
        .layout-content {
            flex: 1;
            min-width: 0;
            direction: rtl;
            background: var(--bg-soft);
        }

        .sidebar {
            width: 236px;
            background: var(--bg-soft);
            border-left: 1px solid var(--border);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.06);
            padding: 14px 10px;
            flex-shrink: 0;
            z-index: 30;
        }

        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, 0.45);
            z-index: 55;
        }

        .brand-box {
            padding: 10px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            background: var(--bg-card);
            margin-bottom: 9px;
        }

        .brand-title { margin: 0; font-size: 15px; font-weight: 800; color: var(--text-black); letter-spacing: .2px; }
        .brand-subtitle { margin: 4px 0 0; color: #4b5563; font-size: 10.5px; }

        .nav-section-label {
            margin: 10px 4px 4px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: #6b7280;
        }

        .nav-links { display: grid; gap: 4px; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 9px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            color: var(--text-black);
            font-size: 12px;
            font-weight: 600;
            border: 1px solid transparent;
            transition: .16s ease;
        }

        .nav-link-icon {
            width: 13px;
            height: 13px;
            border-radius: 4px;
            background: #e5e7eb;
            box-shadow: inset 0 0 0 1px #000000;
            flex-shrink: 0;
        }

        .nav-link:hover {
            color: var(--text-blue);
            background: var(--surface-3);
            border-color: #000000;
        }

        .nav-link.active {
            color: var(--text-blue);
            background: var(--surface-3);
            border-color: #000000;
            font-weight: 700;
        }

        .sidebar select {
            color: var(--text-black) !important;
            background-color: var(--bg-input) !important;
            border: 1px solid #000000 !important;
        }

        .sidebar select option {
            color: var(--text-black) !important;
            background-color: var(--bg-input) !important;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(242, 244, 247, 0.97);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-page);
            padding: 9px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .topbar h1 { margin: 0; font-size: 16px; font-weight: 800; color: var(--page-text); }
        .topbar p { margin: 3px 0 0; color: var(--page-text-muted); font-size: 11px; }
        .topbar-left { display: flex; align-items: center; gap: 10px; }
        .topbar-title-wrap { display: grid; gap: 0; }

        .topbar-right { display: flex; align-items: center; gap: 6px; }

        .sidebar-toggle {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #000000;
            background: #f3f4f6;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 4px;
            padding: 0;
        }

        .sidebar-toggle span {
            width: 15px;
            height: 2px;
            background: #111827;
            border-radius: 999px;
            display: block;
        }

        .utility-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid var(--border-page);
            background: #f3f4f6;
            position: relative;
        }

        .utility-btn::after {
            content: "";
            width: 10px;
            height: 2px;
            border-radius: 999px;
            background: #374151;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .user-pill {
            border: 1px solid var(--border-page);
            border-radius: 9px;
            padding: 5px 9px;
            background: #f3f4f6;
            min-width: 122px;
        }

        .user-pill .name { font-size: 12px; font-weight: 700; color: var(--page-text); }
        .user-pill .meta { margin-top: 2px; font-size: 10px; color: var(--page-text-muted); }

        .language-card {
            min-width: 170px;
            border: 1px solid #000000;
            border-radius: 11px;
            background: var(--bg-card);
            padding: 8px;
        }

        .language-card-title {
            margin: 0 0 6px;
            color: var(--text-black);
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: .35px;
        }

        .language-list { display: grid; gap: 5px; }

        .language-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 6px 8px;
            border-radius: 8px;
            text-decoration: none;
            border: 1px solid #000000;
            background: var(--bg-card);
            color: var(--text-black);
            font-size: 11px;
            font-weight: 700;
            transition: .16s ease;
        }

        .language-link:hover {
            background: var(--surface-3);
            border-color: #000000;
            color: var(--text-blue);
        }

        .language-link.active {
            background: var(--surface-3);
            border-color: #000000;
            color: var(--text-blue);
            box-shadow: none;
        }

        .language-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: rgba(191, 219, 254, 0.55);
            flex-shrink: 0;
        }

        .language-link.active .language-dot {
            background: #93c5fd;
            box-shadow: 0 0 0 3px rgba(59,130,246,.20);
        }

        .page-content {
            padding: 14px;
            background: var(--bg-soft);
            color: var(--page-text);
        }

        .page-content > .page-shell > .page-title,
        .page-content .page-header .page-title {
            color: var(--page-text);
        }

        .page-content > .page-shell > .page-subtitle,
        .page-content .page-header .page-subtitle {
            color: var(--page-text-muted);
        }

        .page-card, .card {
            background: var(--bg-card);
            border: 1px solid #000000;
            border-radius: var(--radius-lg);
            box-shadow: none;
            padding: 12px;
            color: var(--text-black);
        }

        .page-header, .panel-head {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            flex-wrap: wrap;
        }

        .page-title { margin: 0; font-size: 20px; font-weight: 800; color: var(--page-text); }
        .page-subtitle, .panel-subtitle { margin: 3px 0 0; color: var(--page-text-muted); font-size: 11px; }
        .panel-title { margin: 0; font-size: 13px; font-weight: 800; color: var(--text-black); }

        .page-card .page-title,
        .card .page-title,
        .page-card h3,
        .card h3 {
            color: var(--text-black);
        }

        .page-card .page-subtitle,
        .card .page-subtitle,
        .page-card .panel-subtitle,
        .card .panel-subtitle {
            color: var(--page-text-muted);
        }

        .dashboard-stack, .stats-grid, .form-grid, .details-grid {
            display: grid;
            gap: 8px;
        }

        .dashboard-stack { grid-template-columns: 1fr; }

        .stats-grid, .form-grid, .details-grid {
            grid-template-columns: repeat(auto-fit, minmax(158px, 1fr));
        }

        .panel-grid-2 {
            display: grid;
            gap: 10px;
            margin-top: 10px;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }

        .kpi-card,
        .stat-card,
        .detail-box,
        .dashboard-panel {
            background: var(--bg-card) !important;
            border: 1px solid #000000 !important;
            backdrop-filter: none !important;
            color: var(--text-black);
        }

        .stat-card, .detail-box, .dashboard-panel {
            border-radius: var(--radius-md);
            padding: 9px;
        }

        .stat-label {
            color: #4b5563;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .55px;
        }

        .stat-value {
            margin-top: 2px;
            font-size: 20px;
            line-height: 1.15;
            font-weight: 800;
            color: var(--text-black);
        }

        .stat-note { margin-top: 3px; font-size: 10px; color: var(--page-text-muted); }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid #000000;
            border-radius: var(--radius-md);
            box-shadow: none;
            background: var(--bg-card);
        }

        .page-content table,
        .page-card table,
        .card table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            background: var(--bg-card);
        }

        .page-content th,
        .page-content td,
        .page-card th,
        .page-card td,
        .card th,
        .card td {
            padding: 8px;
            border: 1px solid #000000;
            text-align: right;
            color: var(--text-black);
            font-size: 11.5px;
        }

        .page-content th,
        .page-card th,
        .card th {
            background: var(--surface-card-2);
            color: var(--text-black);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .55px;
        }

        .page-content tr:hover td,
        .page-card tr:hover td,
        .card tr:hover td {
            background: #f3f4f6;
        }

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
            background: var(--bg-card);
            border: 1px solid #000000;
            color: var(--text-black);
        }

        .badge {
            display: inline-flex;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
        }

        .badge-green { background: #dcfce7; color: var(--text-green); border: 1px solid #86efac; }
        .badge-red { background: #fee2e2; color: var(--text-red); border: 1px solid #fca5a5; }
        .badge-orange { background: #ffedd5; color: var(--text-orange); border: 1px solid #fdba74; }
        .badge-blue { background: #dbeafe; color: var(--text-blue); border: 1px solid #93c5fd; }
        .badge-gray { background: #f3f4f6; color: #111827; border: 1px solid #000000; }

        .alert-success, .alert-danger, .alert-error {
            margin-bottom: 8px;
            border-radius: 8px;
            padding: 8px 9px;
            border: 1px solid transparent;
            font-size: 11px;
            font-weight: 700;
        }

        .alert-success { background: #dcfce7; border-color: #86efac; color: var(--text-green); }
        .alert-danger, .alert-error { background: #fee2e2; border-color: #fca5a5; color: var(--text-red); }

        .text-black { color: var(--text-black) !important; }
        .text-blue { color: var(--text-blue) !important; }
        .text-red { color: var(--text-red) !important; }
        .text-green { color: var(--text-green) !important; }
        .text-muted { color: var(--page-text-muted) !important; }

        .page-content a:not(.btn):not(.nav-link):not(.badge) {
            color: var(--text-blue);
            font-weight: 600;
        }

        .page-content a:not(.btn):not(.nav-link):not(.badge):hover {
            color: #1e40af;
        }

        .page-content small,
        .page-card small,
        .card small {
            color: var(--page-text-muted) !important;
        }

        .page-content [style*="color:#f3f8ff"],
        .page-content [style*="color:#f8fbff"],
        .page-content [style*="color:#b7c9e4"],
        .page-content [style*="color:#94a3b8"],
        .page-content [style*="color:#dbe8fb"],
        .page-content [style*="color:#e0e9fa"],
        .page-content [style*="color:#cfddf2"] {
            color: var(--page-text-muted) !important;
        }

        .page-content td[style*="color:#f3f8ff"] {
            color: var(--text-black) !important;
            font-weight: 700;
        }

        .page-content td[style*="color:#b7c9e4"] {
            color: var(--text-blue) !important;
        }

        .dashboard-stack .dash-section-title,
        .dashboard-stack .dash-section-sub {
            color: #111827 !important;
        }

        .dashboard-stack .dash-section-note {
            color: #374151 !important;
        }

        .dashboard-stack .badge {
            font-weight: 800;
        }

        .actions-row, .actions, .form-actions { display: flex; gap: 5px; flex-wrap: wrap; }
        .empty-row { text-align: center; color: var(--page-text-muted); }
        .detail-box-full, .form-group-full { grid-column: 1 / -1; }

        label { font-size: 11px; font-weight: 700; color: var(--page-text); margin-bottom: 4px; display: block; }

        .page-card label,
        .card label,
        .stat-card label,
        .detail-box label,
        .dashboard-panel label {
            color: var(--text-black);
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid #000000;
            border-radius: 8px;
            padding: 8px 9px;
            font-size: 12px;
            background: var(--bg-input);
            color: var(--text-black);
        }

        input::placeholder, textarea::placeholder { color: #6b7280; }
        textarea { min-height: 74px; resize: vertical; }

        select {
            color: var(--text-black) !important;
            background-color: var(--bg-input) !important;
            border: 1px solid #000000 !important;
        }

        select option {
            color: var(--text-black) !important;
            background-color: var(--bg-input) !important;
        }

        select:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 1px rgba(59,130,246,.4);
        }

        @media (max-width: 992px) {
            .main-layout { display: block; }
            .sidebar {
                position: fixed;
                top: 0;
                right: 0;
                width: min(86vw, 320px);
                height: 100vh;
                transform: translateX(100%);
                transition: transform .22s ease;
                border-left: 1px solid #000000;
                box-shadow: -2px 0 20px rgba(0, 0, 0, 0.18);
                overflow-y: auto;
                z-index: 60;
                padding: 14px 10px;
            }
            .main-layout.mobile-sidebar-open .sidebar {
                transform: translateX(0);
            }
            .main-layout.mobile-sidebar-open .sidebar-backdrop {
                display: block;
            }
            .page-content { padding: 9px; }
            .topbar { padding: 8px 10px; }
            .page-content table,
            .page-card table,
            .card table { min-width: 600px; }
        }
    </style>
</head>
<body dir="{{ in_array(app()->getLocale(), config('locales.rtl', ['ar', 'ur']), true) ? 'rtl' : 'ltr' }}">
    <div class="main-layout" id="mainLayout">
        @include('layouts.navigation')
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <div class="layout-content">
            <div class="topbar">
                <div class="topbar-left">
                    <div class="topbar-title-wrap">
                        <h1>@yield('page_title', __('layout.page_title_default'))</h1>
                        <p>@yield('page_subtitle', __('layout.page_subtitle_default'))</p>
                    </div>
                </div>

                <div class="topbar-right">
                    <span class="utility-btn" aria-hidden="true"></span>
                    <span class="utility-btn" aria-hidden="true"></span>
                    <span class="utility-btn" aria-hidden="true"></span>

                    @auth
                        <button type="button" class="sidebar-toggle" id="sidebarToggleBtn" aria-label="Toggle control center">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>

                        <div class="user-pill">
                            <div class="name">{{ auth()->user()->name }}</div>
                            <div class="meta">{{ auth()->user()->getRoleLabel() }}</div>
                        </div>
                    @endauth

                </div>
            </div>

            <div class="page-content">
                <div class="page-shell">
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
    </div>

    <script>
        (function () {
            const layout = document.getElementById('mainLayout');
            const toggleBtn = document.getElementById('sidebarToggleBtn');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (!layout || !toggleBtn) return;

            const storageKey = 'company_sidebar_collapsed';
            const isMobile = () => window.matchMedia('(max-width: 992px)').matches;

            const applyDesktopState = () => {
                if (isMobile()) return;
                const saved = localStorage.getItem(storageKey);
                if (saved === '1') {
                    layout.classList.add('sidebar-collapsed');
                } else {
                    layout.classList.remove('sidebar-collapsed');
                }
                layout.classList.remove('mobile-sidebar-open');
            };

            applyDesktopState();

            toggleBtn.addEventListener('click', function () {
                if (isMobile()) {
                    layout.classList.toggle('mobile-sidebar-open');
                    return;
                }
                const collapsed = layout.classList.toggle('sidebar-collapsed');
                localStorage.setItem(storageKey, collapsed ? '1' : '0');
            });

            if (backdrop) {
                backdrop.addEventListener('click', function () {
                    layout.classList.remove('mobile-sidebar-open');
                });
            }

            window.addEventListener('resize', applyDesktopState);
        })();
    </script>
</body>
</html>
