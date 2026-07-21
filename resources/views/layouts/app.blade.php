<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-soft: #f5f3ff;
            --bg: #f5f3ff;
            --bg-card: #ffffff;
            --bg-input: #ffffff;

            --page-text: #000000;
            --page-text-muted: #374151;

            --surface-card: #ffffff;
            --surface-card-2: #ffffff;
            --surface-1: #ffffff;
            --surface-2: #f9fafb;
            --surface-3: #f3f4f6;

            --text: #000000;
            --text-muted: #374151;

            --text-black: #000000;
            --text-blue: #5b21b6;
            --text-red: #dc2626;
            --text-green: #15803d;
            --text-orange: #c2410c;

            --border: #e5e7eb;
            --border-strong: #d1d5db;
            --border-page: #e5e7eb;

            --blue: #2563eb;
            --green: #16a34a;
            --purple: #7c3aed;
            --purple-soft: rgba(124, 58, 237, 0.18);
            --red: #dc2626;
            --amber: #d97706;

            --radius-lg: 14px;
            --radius-md: 11px;
            --radius-sm: 9px;
            --shadow-soft: 0 4px 14px rgba(15, 23, 42, 0.06);
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
            background: #ffffff;
            border-left: 1px solid var(--border);
            box-shadow: 2px 0 12px rgba(15, 23, 42, 0.05);
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
            box-shadow: var(--shadow-soft);
            margin-bottom: 9px;
        }

        .brand-title { margin: 0; font-size: 15px; font-weight: 800; color: var(--text-black); letter-spacing: .2px; }
        .brand-subtitle { margin: 4px 0 0; color: var(--text-muted); font-size: 10.5px; line-height: 1.5; }

        .nav-section-label {
            margin: 10px 4px 4px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .45px;
            color: var(--text-muted);
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
            background: #ede9fe;
            box-shadow: inset 0 0 0 1px #c4b5fd;
            flex-shrink: 0;
        }

        .nav-link:hover {
            color: var(--purple);
            background: #f5f3ff;
            border-color: #ddd6fe;
        }

        .nav-link.active {
            color: var(--purple);
            background: #f5f3ff;
            border-color: #c4b5fd;
            font-weight: 700;
        }

        .sidebar select {
            color: var(--text-black);
            background-color: var(--bg-input);
            border: 1px solid var(--border-strong);
        }

        .sidebar select option {
            color: var(--text-black);
            background-color: var(--bg-input);
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-page);
            padding: 9px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .topbar h1 { margin: 0; font-size: 16px; font-weight: 800; color: var(--page-text); }
        .topbar p { margin: 3px 0 0; color: var(--page-text-muted); font-size: 11px; line-height: 1.5; }
        .topbar-left { display: flex; align-items: center; gap: 10px; }
        .topbar-title-wrap { display: grid; gap: 0; }

        .topbar-right { display: flex; align-items: center; gap: 6px; }

        .sidebar-toggle {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid var(--border-strong);
            background: #ffffff;
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
            background: #000000;
            border-radius: 999px;
            display: block;
        }

        .utility-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid var(--border-page);
            background: #ffffff;
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
            background: #ffffff;
            min-width: 122px;
            box-shadow: var(--shadow-soft);
        }

        .user-pill .name { font-size: 12px; font-weight: 700; color: var(--page-text); }
        .user-pill .meta { margin-top: 2px; font-size: 10px; color: var(--page-text-muted); }

        .language-card {
            min-width: 170px;
            border: 1px solid var(--border);
            border-radius: 11px;
            background: var(--bg-card);
            padding: 8px;
            box-shadow: var(--shadow-soft);
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
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text-black);
            font-size: 11px;
            font-weight: 700;
            transition: .16s ease;
        }

        .language-link:hover {
            background: #f5f3ff;
            border-color: #c4b5fd;
            color: var(--purple);
        }

        .language-link.active {
            background: #f5f3ff;
            border-color: #c4b5fd;
            color: var(--purple);
            box-shadow: none;
        }

        .language-dot {
            width: 7px;
            height: 7px;
            border-radius: 999px;
            background: #ddd6fe;
            flex-shrink: 0;
        }

        .language-link.active .language-dot {
            background: var(--purple);
            box-shadow: 0 0 0 3px var(--purple-soft);
        }

        .page-content {
            padding: 14px;
            background: var(--bg-soft);
            color: var(--page-text);
        }

        .page-content > .page-shell > .page-title,
        .page-content .page-header .page-title {
            color: var(--page-text);
            font-weight: 800;
        }

        .page-content > .page-shell > .page-subtitle,
        .page-content .page-header .page-subtitle {
            color: var(--page-text-muted);
            line-height: 1.55;
        }

        .page-card, .card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            padding: 16px;
            color: #000000;
        }

        .page-header, .panel-head {
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            flex-wrap: wrap;
        }

        .page-title { margin: 0; font-size: 20px; font-weight: 800; color: #000000; }
        .page-subtitle, .panel-subtitle { margin: 4px 0 0; color: var(--page-text-muted); font-size: 12px; line-height: 1.55; }
        .panel-title { margin: 0; font-size: 13px; font-weight: 800; color: #000000; }

        .page-card .page-title,
        .card .page-title,
        .page-card h1,
        .page-card h2,
        .page-card h3,
        .card h1,
        .card h2,
        .card h3 {
            color: #000000;
            font-weight: 800;
        }

        .page-card .page-subtitle,
        .card .page-subtitle,
        .page-card .panel-subtitle,
        .card .panel-subtitle,
        .page-card p,
        .card p {
            color: #000000;
            line-height: 1.6;
        }

        .dashboard-stack, .stats-grid, .form-grid, .details-grid {
            display: grid;
            gap: 12px;
        }

        .dashboard-stack { grid-template-columns: 1fr; }

        .stats-grid, .form-grid, .details-grid {
            grid-template-columns: repeat(auto-fit, minmax(158px, 1fr));
        }

        .panel-grid-2 {
            display: grid;
            gap: 12px;
            margin-top: 10px;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        }

        .kpi-card,
        .stat-card,
        .detail-box,
        .dashboard-panel {
            background: #ffffff;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-soft);
            backdrop-filter: none;
            color: #000000;
        }

        .stat-card, .detail-box, .dashboard-panel {
            border-radius: var(--radius-md);
            padding: 12px;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .55px;
        }

        .stat-value {
            margin-top: 2px;
            font-size: 20px;
            line-height: 1.15;
            font-weight: 800;
            color: #000000;
        }

        .stat-note { margin-top: 3px; font-size: 10px; color: var(--page-text-muted); line-height: 1.5; }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-soft);
            background: #ffffff;
        }

        .page-content table,
        .page-card table,
        .card table {
            width: 100%;
            min-width: 760px;
            border-collapse: collapse;
            background: #ffffff;
        }

        .page-content th,
        .page-content td,
        .page-card th,
        .page-card td,
        .card th,
        .card td {
            padding: 10px 8px;
            border: 1px solid var(--border);
            text-align: right;
            color: #000000;
            font-size: 12px;
            line-height: 1.5;
        }

        .page-content th,
        .page-card th,
        .card th {
            background: #ffffff;
            color: #000000;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .45px;
        }

        .page-content tbody tr,
        .page-card tbody tr,
        .card tbody tr {
            background: #ffffff;
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
        .btn-primary { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .btn-success { background: linear-gradient(135deg, #22c55e, #15803d); }
        .btn-warning { background: linear-gradient(135deg, #f59e0b, #b45309); }
        .btn-danger { background: linear-gradient(135deg, #ef4444, #b91c1c); }
        .btn-secondary {
            background: #ffffff;
            border: 1px solid var(--border-strong);
            color: #000000;
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
        .badge-blue { background: #ede9fe; color: #5b21b6; border: 1px solid #c4b5fd; }
        .badge-gray { background: #f3f4f6; color: #000000; border: 1px solid var(--border); }

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

        .text-black { color: #000000; }
        .text-blue { color: var(--purple); }
        .text-red { color: var(--text-red); }
        .text-green { color: var(--text-green); }
        .text-muted { color: var(--page-text-muted); }

        .page-content a:not(.btn):not(.nav-link):not(.badge) {
            color: var(--purple);
            font-weight: 600;
        }

        .page-content a:not(.btn):not(.nav-link):not(.badge):hover {
            color: #5b21b6;
        }

        .page-content small,
        .page-card small,
        .card small {
            color: #000000;
            line-height: 1.55;
        }

        .page-content [style*="color:#f3f8ff"],
        .page-content [style*="color:#f8fbff"],
        .page-content [style*="color:#b7c9e4"],
        .page-content [style*="color:#94a3b8"],
        .page-content [style*="color:#dbe8fb"],
        .page-content [style*="color:#e0e9fa"],
        .page-content [style*="color:#cfddf2"],
        .page-content [style*="color:#cbd5e1"],
        .page-content [style*="color:#e2e8f0"] {
            color: #000000;
        }

        .page-content td[style*="color:#f3f8ff"],
        .page-content td[style*="color:#b7c9e4"] {
            color: #000000;
            font-weight: 700;
        }

        .dashboard-stack .dash-section-title,
        .dashboard-stack .dash-section-sub {
            color: #000000;
        }

        .dashboard-stack .dash-section-note {
            color: #000000;
            line-height: 1.55;
        }

        .dashboard-stack .badge {
            font-weight: 800;
        }

        .actions-row, .actions, .form-actions { display: flex; gap: 5px; flex-wrap: wrap; }
        .empty-row { text-align: center; color: var(--page-text-muted); }
        .detail-box-full, .form-group-full { grid-column: 1 / -1; }

        label { font-size: 12px; font-weight: 700; color: #000000; margin-bottom: 6px; display: block; }

        .page-card label,
        .card label,
        .stat-card label,
        .detail-box label,
        .dashboard-panel label {
            color: #000000;
        }

        input, select, textarea {
            width: 100%;
            border: 1px solid var(--border-strong);
            border-radius: 8px;
            padding: 8px 9px;
            font-size: 12px;
            background: #ffffff;
            color: #000000;
            line-height: 1.45;
        }

        input::placeholder, textarea::placeholder { color: #6b7280; opacity: 1; }
        textarea { min-height: 74px; resize: vertical; }

        select {
            color: #000000;
            background-color: #ffffff;
            border: 1px solid var(--border-strong);
        }

        select option {
            color: #000000;
            background-color: #ffffff;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--purple);
            box-shadow: 0 0 0 3px var(--purple-soft);
        }

        /* Modals / dialogs */
        .modal-content,
        [x-show].bg-white,
        .fixed .bg-white.rounded-lg {
            background: #ffffff;
            color: #000000;
        }

        .modal-content label,
        .modal-content p,
        .modal-content h1,
        .modal-content h2,
        .modal-content h3 {
            color: #000000;
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
                border-left: 1px solid var(--border);
                box-shadow: -2px 0 20px rgba(0, 0, 0, 0.12);
                overflow-y: auto;
                z-index: 60;
                padding: 14px 10px;
                background: #ffffff;
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
