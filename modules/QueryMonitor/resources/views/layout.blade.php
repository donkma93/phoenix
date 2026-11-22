<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Query Monitor') — DB Performance Module</title>
    <style>
        :root {
            --bg: #0f172a;
            --surface: #1e293b;
            --surface-2: #0b1220;
            --border: #334155;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --accent: #38bdf8;
            --accent-hover: #0ea5e9;
            --danger: #f87171;
            --warning: #fbbf24;
            --success: #34d399;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
        }
        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 20;
        }
        .brand {
            font-weight: 700;
            font-size: 16px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .dot {
            width: 10px; height: 10px; border-radius: 50%;
            display: inline-block;
        }
        .dot.on { background: var(--success); box-shadow: 0 0 8px var(--success); }
        .dot.off { background: var(--danger); }
        .nav { display: flex; gap: 6px; align-items: center; }
        .nav a {
            color: var(--muted);
            padding: 7px 12px;
            border-radius: 6px;
            font-weight: 500;
        }
        .nav a:hover, .nav a.active {
            background: rgba(56, 189, 248, 0.14);
            color: #fff;
            text-decoration: none;
        }
        .user-box { display: flex; align-items: center; gap: 12px; color: var(--muted); font-size: 13px; }
        .container { max-width: 1440px; margin: 0 auto; padding: 24px; }
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 20px;
        }
        .card h2, .card h3 { margin: 0 0 14px 0; font-size: 16px; color: #fff; }
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }
        .kpi {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px 18px;
        }
        .kpi .label { color: var(--muted); font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; }
        .kpi .value { font-size: 24px; font-weight: 700; margin-top: 6px; color: #fff; }
        .kpi .sub { color: var(--muted); font-size: 12px; margin-top: 4px; }
        .kpi.danger .value { color: var(--danger); }
        .kpi.warning .value { color: var(--warning); }
        .kpi.success .value { color: var(--success); }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }
        th { color: var(--muted); font-weight: 600; font-size: 12px; text-transform: uppercase; white-space: nowrap; }
        tr:hover td { background: rgba(255,255,255,0.025); }
        .sql-code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12px;
            background: var(--surface-2);
            border: 1px solid #263449;
            border-radius: 6px;
            padding: 6px 8px;
            color: #cbd5e1;
            word-break: break-word;
            white-space: pre-wrap;
            max-width: 680px;
        }
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-select { background: rgba(56,189,248,0.16); color: #7dd3fc; }
        .badge-insert { background: rgba(52,211,153,0.16); color: #6ee7b7; }
        .badge-update { background: rgba(251,191,36,0.16); color: #fde68a; }
        .badge-delete { background: rgba(248,113,113,0.18); color: #fca5a5; }
        .badge-other { background: rgba(148,163,184,0.18); color: #cbd5e1; }
        .badge-slow { background: rgba(248,113,113,0.2); color: #fca5a5; }
        .badge-n1 { background: rgba(251,191,36,0.2); color: #fde68a; }
        .badge-ok { background: rgba(52,211,153,0.2); color: #6ee7b7; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 13px; border-radius: 6px; border: 1px solid var(--border);
            background: var(--surface-2); color: var(--text); cursor: pointer;
            font-size: 13px; font-weight: 500;
        }
        .btn:hover { border-color: var(--accent); color: #fff; text-decoration: none; }
        .btn-primary { background: var(--accent-hover); border-color: var(--accent-hover); color: #fff; }
        .btn-primary:hover { background: var(--accent); }
        .btn-danger { background: rgba(248,113,113,0.15); border-color: rgba(248,113,113,0.4); color: #fca5a5; }
        .btn-sm { padding: 4px 9px; font-size: 12px; }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], select {
            background: var(--surface-2);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 7px 10px;
            border-radius: 6px;
            font-size: 13px;
        }
        .filters { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 16px; }
        .alert {
            padding: 12px 15px; border-radius: 8px; margin-bottom: 16px; font-size: 13px;
        }
        .alert-success { background: rgba(52,211,153,0.14); border: 1px solid rgba(52,211,153,0.35); color: #a7f3d0; }
        .alert-danger { background: rgba(248,113,113,0.14); border: 1px solid rgba(248,113,113,0.35); color: #fecaca; }
        .alert-warning { background: rgba(251,191,36,0.14); border: 1px solid rgba(251,191,36,0.35); color: #fde68a; }
        .alert-info { background: rgba(56,189,248,0.14); border: 1px solid rgba(56,189,248,0.35); color: #bae6fd; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .muted { color: var(--muted); }
        .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
        .table-wrap { overflow-x: auto; }
    </style>
</head>
<body>
<header class="topbar">
    <div class="brand">
        <span class="dot {{ !empty($recordingEnabled) ? 'on' : 'off' }}" title="{{ !empty($recordingEnabled) ? 'Đang ghi log' : 'Đã tạm dừng ghi log' }}"></span>
        <span>Query Monitor</span>
        <span class="badge badge-other">Standalone Module</span>
    </div>
    <nav class="nav">
        <a href="{{ route('query-monitor.live') }}" class="{{ request()->routeIs('query-monitor.live') ? 'active' : '' }}" style="display:inline-flex;align-items:center;gap:6px;">
            <span class="dot on" style="width:7px;height:7px;"></span> Real-time &amp; Biểu đồ
        </a>
        <a href="{{ route('query-monitor.tuning') }}" class="{{ request()->routeIs('query-monitor.tuning*') ? 'active' : '' }}">SQL Tuning</a>
        <a href="{{ route('query-monitor.dashboard') }}" class="{{ request()->routeIs('query-monitor.dashboard') ? 'active' : '' }}">Tổng quan</a>
        <a href="{{ route('query-monitor.queries') }}" class="{{ request()->routeIs('query-monitor.queries') ? 'active' : '' }}">Nhật ký truy vấn</a>
        <a href="{{ route('query-monitor.tables') }}" class="{{ request()->routeIs('query-monitor.tables') ? 'active' : '' }}">Cấu trúc &amp; Index DB</a>
        @if(($monitorUser['role'] ?? '') === 'admin')
            <a href="{{ route('query-monitor.settings') }}" class="{{ request()->routeIs('query-monitor.settings') ? 'active' : '' }}">Cấu hình</a>
            <a href="{{ route('query-monitor.users') }}" class="{{ request()->routeIs('query-monitor.users') ? 'active' : '' }}">Tài khoản</a>
        @endif
    </nav>
    <div class="user-box">
        <span>{{ $monitorUser['name'] ?? '' }} <small>({{ $monitorUser['role'] ?? '' }})</small></span>
        <form method="POST" action="{{ route('query-monitor.logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="btn btn-sm">Đăng xuất</button>
        </form>
    </div>
</header>

<main class="container">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    @yield('content')
</main>
</body>
</html>
