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
            background: #f3fbf8;
            color: #17312b;
        }

        .admin-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 250px 1fr;
        }

        .admin-sidebar {
            background: #0b6d5a;
            color: #ffffff;
            padding: 20px 14px;
        }

        .admin-brand {
            margin: 0 0 18px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .admin-nav {
            display: grid;
            gap: 8px;
        }

        .admin-nav a {
            color: #def8f1;
            text-decoration: none;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 10px 10px;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .admin-nav a.is-active,
        .admin-nav a:hover {
            background: #00c9a2;
            color: #083a31;
            border-color: #8effdf;
        }

        .admin-nav .back-link {
            margin-top: 8px;
            background: rgba(255, 255, 255, 0.08);
        }

        .admin-main {
            padding: 24px;
        }

        .admin-header {
            margin-bottom: 18px;
        }

        .admin-header h1 {
            margin: 0;
            color: #0b6d5a;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: clamp(22px, 3vw, 34px);
        }

        .admin-header p {
            margin: 8px 0 0;
            color: #456860;
        }

        .panel-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .panel {
            background: #ffffff;
            border: 1px solid #d8ece6;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 10px 20px rgba(2, 70, 58, 0.06);
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
            color: #456860;
            line-height: 1.5;
            font-size: 13px;
        }

        .page-table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #d8ece6;
            border-radius: 12px;
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

        @media (max-width: 980px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                padding: 14px;
            }

            .panel-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <h2 class="admin-brand">Admin Panel</h2>
            <nav class="admin-nav" aria-label="Admin navigation">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('admin.reports') ? 'is-active' : '' }}" href="{{ route('admin.reports') }}">Incident Reports</a>
                <a class="{{ request()->routeIs('admin.hotspots') ? 'is-active' : '' }}" href="{{ route('admin.hotspots') }}">Hotspots</a>
                <a class="{{ request()->routeIs('admin.users') ? 'is-active' : '' }}" href="{{ route('admin.users') }}">Users</a>
                <a class="{{ request()->routeIs('admin.settings') ? 'is-active' : '' }}" href="{{ route('admin.settings') }}">Settings</a>
                <a class="back-link" href="{{ url('/') }}">Back to Site</a>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <h1>@yield('heading')</h1>
                <p>@yield('subheading')</p>
            </header>

            @yield('content')
        </main>
    </div>
</body>
</html>
