<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | Clean Earth Interactive Mapping</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            min-height: 100vh;
            background: linear-gradient(160deg, #f2fffb 0%, #e7fbf5 52%, #ffffff 100%);
            color: #1b1b1b;
            overflow-x: hidden;
        }

        .admin-shell {
            width: min(1220px, 96vw);
            margin: 16px auto;
            display: grid;
            grid-template-columns: 240px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
        }

        .admin-sidebar {
            background: #ffffff;
            border: 1px solid #d9ebe6;
            border-radius: 18px;
            box-shadow: 0 16px 30px rgba(0, 121, 101, 0.1);
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: sticky;
            top: 14px;
            min-height: calc(100vh - 60px);
            color: #1f2b28;
        }

        .admin-brand {
            margin: 0;
            padding: 6px 4px 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #55706a;
        }

        .admin-nav {
            display: grid;
            gap: 8px;
        }

        .admin-nav a {
            color: #1f2b28;
            text-decoration: none;
            border: 1px solid #dbeee8;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            letter-spacing: 0.02em;
            background: #f8fffc;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-nav a svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            flex-shrink: 0;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .admin-nav a.is-active svg,
        .admin-nav a:hover svg {
            opacity: 1;
        }

        .admin-nav a.is-active,
        .admin-nav a:hover {
            background: #ecfffb;
            border-color: #9bdcca;
            color: #0b6d5a;
        }

        .admin-nav .back-link {
            margin-top: 6px;
            border-color: #8edac9;
            color: #0b6d5a;
            background: #ffffff;
        }

        .admin-nav .back-link-button {
            margin-top: 6px;
            border: 1px solid #8edac9;
            border-radius: 10px;
            color: #0b6d5a;
            background: #ffffff;
            padding: 10px 12px;
            font-size: 14px;
            letter-spacing: 0.02em;
            text-align: left;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-nav .back-link-button svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            flex-shrink: 0;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }

        .admin-nav .back-link-button:hover svg {
            opacity: 1;
        }

        .admin-nav .back-link-button:hover {
            background: #ecfffb;
            border-color: #9bdcca;
            color: #0b6d5a;
        }

        .admin-main {
            display: grid;
            gap: 14px;
        }

        .admin-header {
            background: #ffffff;
            border: 1px solid #d9ebe6;
            border-radius: 14px;
            box-shadow: 0 10px 20px rgba(0, 121, 101, 0.08);
            padding: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .admin-header h1 {
            margin: 0;
            color: #0b6d5a;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-size: clamp(20px, 2.8vw, 32px);
        }

        .admin-header p {
            margin: 8px 0 0;
            color: #55706a;
            font-size: 13px;
            line-height: 1.45;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #d9ebe6;
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 14px 28px rgba(0, 121, 101, 0.1);
        }

        .panel h3 {
            margin: 0 0 6px;
            color: #0b6d5a;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-size: 14px;
        }

        .panel p {
            margin: 0;
            color: #55706a;
            line-height: 1.5;
            font-size: 13px;
        }

        .admin-content {
            display: grid;
            gap: 14px;
        }

        .admin-toolbar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            border: 1px solid #00c9a2;
            background: #00c9a2;
            color: #ffffff;
            border-radius: 999px;
            padding: 9px 13px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-decoration: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .btn.alt {
            background: #ffffff;
            color: #0b6d5a;
            border-color: #8edac9;
        }

        .btn:hover {
            filter: brightness(0.96);
        }

        .btn svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            flex-shrink: 0;
        }


        .page-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #d9ebe6;
            border-radius: 14px;
            overflow: hidden;
        }

        .page-table th,
        .page-table td {
            text-align: left;
            padding: 10px 12px;
            border-bottom: 1px solid #e8f5f2;
            font-size: 13px;
        }

        .page-table th {
            background: #ecfffb;
            color: #0b6d5a;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            font-size: 12px;
        }

        .kpi-value {
            margin-top: 6px;
            font-size: clamp(26px, 4vw, 34px);
            line-height: 1;
            letter-spacing: -0.02em;
            color: #0b6d5a;
            font-weight: 800;
        }

        .kpi-trend {
            margin-top: 8px;
            display: inline-block;
            font-size: 12px;
            color: #2f6f5f;
            background: #ecfffb;
            border: 1px solid #c8efe4;
            border-radius: 999px;
            padding: 4px 8px;
        }

        .split-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 14px;
        }

        .fake-map {
            min-height: 300px;
            border-radius: 12px;
            border: 1px solid #d9ebe6;
            background:
                radial-gradient(circle at 22% 24%, rgba(0, 201, 162, 0.22), transparent 32%),
                radial-gradient(circle at 76% 68%, rgba(11, 109, 90, 0.18), transparent 34%),
                linear-gradient(160deg, #f8fffc 0%, #effbf7 55%, #ffffff 100%);
            position: relative;
            overflow: hidden;
        }

        .fake-map::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(11, 109, 90, 0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(11, 109, 90, 0.08) 1px, transparent 1px);
            background-size: 34px 34px;
            pointer-events: none;
        }

        .fake-map-badge {
            position: absolute;
            left: 12px;
            bottom: 12px;
            background: #ffffff;
            border: 1px solid #d9ebe6;
            color: #0b6d5a;
            border-radius: 999px;
            padding: 7px 10px;
            font-size: 12px;
            z-index: 1;
        }

        .list-stack {
            display: grid;
            gap: 10px;
        }

        .list-item {
            border: 1px solid #d9ebe6;
            border-radius: 10px;
            padding: 10px;
            background: #f8fffc;
        }

        .list-item strong {
            display: block;
            color: #0b6d5a;
            margin-bottom: 2px;
            font-size: 13px;
        }

        .list-item span {
            color: #55706a;
            font-size: 12px;
        }

        @media (max-width: 980px) {
            .admin-shell {
                grid-template-columns: 1fr;
                margin: 10px auto;
            }

            .admin-sidebar {
                padding: 14px;
                position: static;
                min-height: 0;
            }

            .panel-grid {
                grid-template-columns: 1fr;
            }

            .split-grid {
                grid-template-columns: 1fr;
            }

            .admin-toolbar {
                width: 100%;
                justify-content: flex-start;
            }

        }
    </style>
    @stack('head')
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <h2 class="admin-brand">Admin Panel</h2>
            <nav class="admin-nav" aria-label="Admin navigation">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a class="{{ request()->routeIs('admin.reports') ? 'is-active' : '' }}" href="{{ route('admin.reports') }}">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Incident Reports
                </a>
                <a class="{{ request()->routeIs('admin.hotspots') ? 'is-active' : '' }}" href="{{ route('admin.hotspots') }}">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Hotspots
                </a>
                <a class="{{ request()->routeIs('admin.barangays') ? 'is-active' : '' }}" href="{{ route('admin.barangays') }}">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Barangay Tally
                </a>
                <a class="{{ request()->routeIs('admin.users') ? 'is-active' : '' }}" href="{{ route('admin.users') }}">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Users
                </a>
                <a class="{{ request()->routeIs('admin.settings') ? 'is-active' : '' }}" href="{{ route('admin.settings') }}">
                    <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Settings
                </a>
              
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="back-link-button" type="submit">
                        <svg viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Log Out
                    </button>
                </form>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <h1>@yield('heading')</h1>
                    <p>@yield('subheading')</p>
                </div>
                @hasSection('toolbar')
                    <div class="admin-toolbar">
                        @yield('toolbar')
                    </div>
                @endif
            </header>

            <div class="admin-content">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
