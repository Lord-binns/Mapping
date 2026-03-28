<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >
    <style>
        body {
            padding: 0;
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(160deg, #f2fffb 0%, #e7fbf5 52%, #ffffff 100%);
            font-family: sans-serif;
            color: #1b1b1b;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .main-content {
            padding-top: 120px;
            padding-bottom: 24px;
        }

        .dashboard-shell {
            width: min(1200px, 96vw);
            margin: 0 auto;
            display: grid;
            gap: 14px;
        }

        .dashboard-layout {
            display: grid;
            grid-template-columns: 240px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            min-height: 80px;
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 3px solid #00c9a2;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 16px 24px;
            z-index: 20;
            backdrop-filter: blur(6px);
        }

        .topbar-note {
            border: 1px solid #cbcbcb;
            background: #ffffff;
            color: #3a3a3a;
            border-radius: 999px;
            padding: 10px 14px;
            font-size: 12px;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .brand {
            font-weight: 900;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #0b6d5a;
            font-size: clamp(11px, 1vw, 14px);
            white-space: normal;
            line-height: 1.35;
            max-width: min(760px, 60vw);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .brand span {
            display: inline-block;
        }

        .brand-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #00c9a2 0%, #0b6d5a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900;
            font-size: 16px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .nav-link {
            text-decoration: none;
            color: #1c1c1c;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 15px;
            line-height: 1;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 12px 15px;
            border-radius: 999px;
            border: 1px solid transparent;
        }

        .nav-link svg {
            width: 18px;
            height: 18px;
            stroke-width: 2;
            stroke: currentColor;
            color: #0b6d5a;
            flex-shrink: 0;
        }

        .nav-link:hover {
            border-color: #d4d4d4;
            background: #ffffff;
        }

        .nav-auth {
            border: 1px solid #cbcbcb;
            background: #ffffff;
        }

        .nav-auth:hover {
            border-color: #00c9a2;
            color: #0b6d5a;
            background: #ecfffb;
        }

        .sidebar {
            background: #ffffff;
            border: 1px solid #d9ebe6;
            border-radius: 18px;
            box-shadow: 0 16px 30px rgba(0, 121, 101, 0.1);
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            position: sticky;
            top: 102px;
            min-height: calc(100vh - 132px);
            color: #1f2b28;
        }

        .sidebar-title {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #55706a;
            padding: 4px 2px;
        }

        .sidebar-title.with-icon {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .sidebar-title.with-icon svg {
            width: 14px;
            height: 14px;
            stroke: #0b6d5a;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .sidebar .nav-link,
        .sidebar .btn {
            justify-content: flex-start;
            width: 100%;
            box-sizing: border-box;
        }

        .sidebar .nav-link {
            color: #1f2b28;
            border: 1px solid #dbeee8;
            border-radius: 10px;
            padding: 10px 12px;
            text-transform: none;
            letter-spacing: 0.02em;
            font-size: 14px;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            background: #f8fffc;
        }

        .sidebar .nav-link svg {
            color: #0b6d5a;
        }

        .sidebar .nav-link:hover {
            background: #ecfffb;
            border-color: #9bdcca;
            color: #0b6d5a;
        }

        .report-panel {
            margin-top: 4px;
            border-top: 1px solid #e2f1ed;
            padding-top: 10px;
            display: grid;
            gap: 8px;
        }

        .city-panel {
            margin-top: 4px;
            border-top: 1px solid #e2f1ed;
            padding-top: 10px;
            display: grid;
            gap: 8px;
        }

        .barangay-select-wrap[hidden] {
            display: none;
        }

        .panel-label {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #55706a;
        }

        .panel-label.with-icon {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .panel-label.with-icon svg {
            width: 14px;
            height: 14px;
            stroke: #0b6d5a;
            stroke-width: 2;
            flex-shrink: 0;
        }

        .field,
        .field-input {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #c9dfd8;
            border-radius: 10px;
            padding: 9px 10px;
            font-size: 13px;
            background: #ffffff;
            color: #1f2b28;
        }

        .field {
            border: 1px solid #dbeee8;
            background: #f8fffc;
            padding: 10px 12px;
            font-size: 14px;
            letter-spacing: 0.02em;
            cursor: pointer;
            appearance: none;
            background-image:
                linear-gradient(45deg, transparent 50%, #0b6d5a 50%),
                linear-gradient(135deg, #0b6d5a 50%, transparent 50%);
            background-position:
                calc(100% - 16px) calc(50% - 2px),
                calc(100% - 10px) calc(50% - 2px);
            background-size: 6px 6px, 6px 6px;
            background-repeat: no-repeat;
            padding-right: 34px;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        .field:hover {
            background-color: #ecfffb;
            border-color: #9bdcca;
            color: #0b6d5a;
        }

        .select-with-icon {
            position: relative;
        }

        .select-with-icon .select-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            stroke: #0b6d5a;
            stroke-width: 2;
            fill: none;
            pointer-events: none;
        }

        .select-with-icon .field {
            padding-left: 34px;
        }

        .field:focus,
        .field-input:focus {
            outline: none;
            border-color: #00c9a2;
            box-shadow: 0 0 0 3px rgba(0, 201, 162, 0.12);
        }

        .field option {
            background: #ffffff;
            color: #1f2b28;
        }

        .report-actions {
            display: grid;
            gap: 8px;
        }

        .tag-help {
            margin: 0;
            font-size: 12px;
            color: #55706a;
            line-height: 1.4;
        }

        .tag-popup-remove {
            margin-top: 8px;
            border: 1px solid #8edac9;
            background: #ffffff;
            color: #0b6d5a;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 12px;
            cursor: pointer;
        }

        .tag-popup-remove:hover {
            background: #ecfffb;
        }

        .sidebar .btn {
            border: 1px solid #00c9a2;
            background: #00c9a2;
            color: #ffffff;
            border-radius: 10px;
            text-transform: none;
            letter-spacing: 0.02em;
            font-size: 13px;
            padding: 10px 12px;
        }

        .sidebar .btn:hover {
            filter: brightness(0.96);
        }

        .sidebar .btn.alt {
            background: #ffffff;
            color: #0b6d5a;
            border-color: #8edac9;
        }

        .sidebar .btn.alt:hover {
            background: #ecfffb;
            color: #0b6d5a;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 10px;
            border-top: 1px solid #e2f1ed;
        }

        .sidebar-logout {
            text-decoration: none;
            width: 100%;
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            justify-content: flex-start;
            border: 1px solid #8edac9;
            background: #ffffff;
            color: #0b6d5a;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 13px;
            letter-spacing: 0.02em;
        }

        .sidebar-logout svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            flex-shrink: 0;
        }

        .sidebar-logout:hover {
            background: #ecfffb;
            color: #0b6d5a;
        }

        .tag-pin {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #ffffff;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.22);
            font-size: 15px;
            line-height: 1;
        }

        .tag-pin svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            pointer-events: none;
        }

        .tag-high-risk {
            background: #d94848;
        }

        .tag-dumping-site {
            background: #a35f00;
        }

        .tag-contaminated-water {
            background: #1a6ca8;
        }

        .tag-illegal-burning {
            background: #7d3f98;
        }

        .tag-blocked-drainage {
            background: #355e3b;
        }

        .tag-other {
            background: #5f5f5f;
        }

        .tag-pin-location {
            background: #0b6d5a;
        }

        .content-stack {
            display: grid;
            gap: 14px;
        }

        .dashboard-topbar {
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

        .dashboard-top-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-wrap: wrap;
        }

        .dashboard-top-actions .btn {
            padding: 9px 12px;
            font-size: 11px;
        }

        .dashboard-top-actions .btn.notification {
            position: relative;
            padding-right: 34px;
        }

        .notification-dot {
            position: absolute;
            top: 6px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ff3b30;
            box-shadow: 0 0 0 2px #ffffff;
        }

        h1 {
            margin: 0;
            font-size: clamp(20px, 2.8vw, 32px);
            color: #0b6d5a;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .meta {
            margin: 8px 0 0;
            color: #55706a;
            font-size: 13px;
            line-height: 1.45;
        }

        .map-card {
            background: #ffffff;
            border: 1px solid #d9ebe6;
            border-radius: 14px;
            box-shadow: 0 14px 28px rgba(0, 121, 101, 0.1);
            overflow: hidden;
            width: min(980px, 100%);
            margin: 0 auto;
        }

        #map {
            width: 100%;
            height: clamp(320px, 54vh, 520px);
        }

        .coords {
            padding: 12px 14px;
            font-size: 13px;
            color: #325f55;
            border-top: 1px solid #e2f1ed;
            background: #f7fffc;
        }

        .links {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            border: 1px solid #00c9a2;
            background: #00c9a2;
            color: #ffffff;
            border-radius: 999px;
            padding: 10px 14px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-decoration: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .btn svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
            flex-shrink: 0;
        }

        .btn.alt {
            background: #ffffff;
            color: #0b6d5a;
            border-color: #8edac9;
        }

        .temp-switch-fab {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 35;
            box-shadow: 0 12px 24px rgba(0, 121, 101, 0.2);
        }

        @media (max-width: 700px) {
            .navbar {
                height: auto;
                min-height: 100px;
                padding: 16px 12px;
                gap: 8px;
            }

            .main-content {
                padding-top: 140px;
            }

            .dashboard-layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
                min-height: 0;
            }

            #map {
                height: clamp(280px, 48vh, 420px);
            }

            .dashboard-top-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .temp-switch-fab {
                right: 12px;
                bottom: 12px;
            }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="brand">
            <div class="brand-logo"><img src="https://via.placeholder.com/40" alt="Logo"></div>
            <span>{{ config('app.name') }}</span>
        </div>
        <span class="topbar-note">Dashboard</span>
    </header>

    <main class="main-content">
        <div class="dashboard-shell">
            <div class="dashboard-layout">
                <aside class="sidebar" aria-label="Dashboard sidebar">
                    <p class="sidebar-title with-icon">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16" stroke-linecap="round"/><path d="M4 12h16" stroke-linecap="round"/><path d="M4 17h16" stroke-linecap="round"/></svg>
                        Actions
                    </p>
                    <button id="track-location-btn" class="btn" type="button">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v4" stroke-linecap="round"/><path d="M12 17v4" stroke-linecap="round"/><path d="M3 12h4" stroke-linecap="round"/><path d="M17 12h4" stroke-linecap="round"/><circle cx="12" cy="12" r="4"/></svg>
                        Track
                    </button>

                    <section class="city-panel" aria-label="City selector">
                        <div class="select-with-icon">
                            <svg class="select-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v18" stroke-linecap="round"/><path d="M5 8h6" stroke-linecap="round"/><path d="M5 16h6" stroke-linecap="round"/><path d="M12 6c2.8 0 7 1.3 7 4.5S14.8 15 12 15" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <select id="city-select" class="field" aria-label="Select city">
                                <option value="">Choose City</option>
                                <option value="manolo-fortich">Manolo Fortich</option>
                                <option value="cagayan-de-oro">Cagayan de Oro City</option>
                            </select>
                        </div>
                        <div id="barangay-select-wrap" class="select-with-icon barangay-select-wrap" hidden>
                            <svg class="select-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8h16" stroke-linecap="round"/><path d="M4 12h16" stroke-linecap="round"/><path d="M4 16h16" stroke-linecap="round"/></svg>
                            <select id="barangay-select" class="field" aria-label="Select barangay" disabled>
                                <option value="">Choose Barangay</option>
                            </select>
                        </div>
                        <p id="city-status" class="tag-help">Select a city to zoom and highlight.</p>
                    </section>

                    <section class="report-panel" aria-label="Location tagging">
                        <div class="select-with-icon">
                            <svg class="select-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.5"/></svg>
                            <select id="tag-type" class="field" aria-label="Tag type">
                                <option value="">Tag Location</option>
                                <option value="Pin Location">Pin Location</option>
                                <option value="High Risk">High Risk</option>
                                <option value="Dumping Site">Dumping Site</option>
                                <option value="Contaminated Water">Contaminated Water</option>
                                <option value="Illegal Burning">Illegal Burning</option>
                                <option value="Blocked Drainage">Blocked Drainage</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <input id="tag-note" class="field-input" type="text" maxlength="90" placeholder="Optional note (ex: near creek entrance)">
                        <input id="tag-image" class="field-input" type="file" accept="image/*" style="padding:6px 10px; font-size:12px; cursor:pointer;" placeholder="Upload image">
                        <div class="report-actions">
                            <button id="add-location-tag" class="btn" type="button">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14" stroke-linecap="round"/><path d="M5 12h14" stroke-linecap="round"/></svg>
                                Add Tag
                            </button>
                            <button id="toggle-satellite-mode" class="btn alt" type="button">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M9 19l2-4h4l2 4" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 9h6" stroke-linecap="round"/><path d="M8 12h8" stroke-linecap="round"/></svg>
                                <span id="satellite-mode-label">Satellite: Off</span>
                            </button>
                        </div>
                        <p id="tag-status" class="tag-help">Drag the pin, then add a tag.</p>
                    </section>

                    <div class="sidebar-footer">
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button class="sidebar-logout" type="submit">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 17l5-5-5-5" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12H3" stroke-linecap="round"/><path d="M21 4v16" stroke-linecap="round"/></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </aside>

                <div class="content-stack">
                    <section class="dashboard-topbar">
                        <div>
                            <h1>Mapping Dashboard</h1>
                            <p class="meta">Live map view with your current location pin.</p>
                        </div>

                        <div class="dashboard-top-actions" aria-label="Dashboard quick actions">
                            <a class="btn alt" href="{{ route('dashboard') }}">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 9.5V20h13V9.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Home
                            </a>
                            <button type="button" class="btn alt">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16" stroke-linecap="round"/><path d="M4 12h16" stroke-linecap="round"/><path d="M4 19h10" stroke-linecap="round"/></svg>
                                Reports
                            </button>
                            <button type="button" class="btn alt notification">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 8-3 8h18s-3-1-3-8"/><path d="M10 20a2 2 0 0 0 4 0"/></svg>
                                Notifications
                                <span class="notification-dot" aria-hidden="true"></span>
                            </button>
                            <button type="button" class="btn">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v12" stroke-linecap="round"/><path d="M7 11l5 5 5-5" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 20h14" stroke-linecap="round"/></svg>
                                Export
                            </button>
                        </div>
                    </section>

                    <section class="map-card" aria-label="Live map">
                        <div id="map"></div>
                        <div id="coords" class="coords">Locating your current coordinates...</div>
                    </section>
                </div>
            </div>
        </div>
    </main>

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>
    <script src="https://unpkg.com/osmtogeojson@3.0.0-beta.5/osmtogeojson.js"></script>
    <script>
        const defaultCenter = [14.5995, 120.9842];
        const map = L.map('map').setView(defaultCenter, 12);
        const coordsEl = document.getElementById('coords');
        const trackBtn = document.getElementById('track-location-btn');
        const tagTypeEl = document.getElementById('tag-type');
        const tagNoteEl = document.getElementById('tag-note');
        const addTagBtn = document.getElementById('add-location-tag');
        const satelliteModeBtn = document.getElementById('toggle-satellite-mode');
        const satelliteModeLabelEl = document.getElementById('satellite-mode-label');
        const tagStatusEl = document.getElementById('tag-status');
        const citySelectEl = document.getElementById('city-select');
        const barangaySelectWrapEl = document.getElementById('barangay-select-wrap');
        const barangaySelectEl = document.getElementById('barangay-select');
        const cityStatusEl = document.getElementById('city-status');
        const savedTagsKey = 'dashboard-location-tags-v1';

        const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri',
        });

        let userMarker = null;
        let accuracyCircle = null;
        let watchId = null;
        let currentPosition = null;
        let isSatelliteMode = false;
        const tagLayer = L.layerGroup().addTo(map);
        const cityLayer = L.layerGroup().addTo(map);
        const barangayLayer = L.layerGroup().addTo(map);

        const cityPresets = {
            'manolo-fortich': {
                label: 'Manolo Fortich',
                osmId: 13452750,
                osmType: 'relation',
                center: [8.3698, 124.8645],
                zoom: 12,
                radius: 4500,
            },
            'cagayan-de-oro': {
                label: 'Cagayan de Oro City',
                osmId: 2387482,
                osmType: 'relation',
                center: [8.4542, 124.6319],
                zoom: 12,
                radius: 7000,
            },
        };

        const cityBoundaryCache = new Map();
        const barangayBoundaryCache = new Map();
        const barangaysByCity = {
            'manolo-fortich': [
                'Agusan Canyon',
                'Alae',
                'Dalirig',
                'Damilag',
                'Dicklum',
                'Kalugmanan',
                'Lindaban',
                'Lingion',
                'Lunocan',
                'Maluko',
                'Santiago',
                'Sankanan',
                'Santo Nino',
                'Tankulan',
                'Ticala',
            ],
            'cagayan-de-oro': [
                'Balulang',
                'Bugo',
                'Carmen',
                'Gusa',
                'Kauswagan',
                'Lapasan',
                'Macasandig',
                'Nazareth',
                'Patag',
                'Puntod',
            ],
        };

        const barangayPinOverrides = {
            'manolo-fortich': {
                'agusan canyon': [8.322930, 124.809880],
                damilag: [8.352661, 124.813459],
                dicklum: [8.372673, 124.849266],
                kalugmanan: [8.277496, 124.860900],
                lindaban: [8.289446, 124.846830],
                lingion: [8.403086, 124.888713],
                lunocan: [8.431609, 124.839909],
                maluko: [8.374209, 124.955686],
                santiago: [8.436350, 124.995514],
                sankanan: [8.316145, 124.858090],
                'santo nino': [8.430081, 124.864617],
                ticala: [8.340169, 124.892535],
                alae: [8.422600, 124.814258],
            },
        };

        function setCityStatus(message) {
            if (cityStatusEl) {
                cityStatusEl.textContent = message;
            }
        }

        function getSelectedBarangayLabel() {
            if (!barangaySelectEl) {
                return '';
            }

            const selectedOption = barangaySelectEl.options[barangaySelectEl.selectedIndex];
            return selectedOption ? selectedOption.textContent : '';
        }

        function hasAreaGeometry(geojson) {
            if (!geojson) {
                return false;
            }

            const type = String(geojson.type || '').toLowerCase();
            if (type === 'polygon' || type === 'multipolygon') {
                return true;
            }

            if (type === 'feature') {
                return hasAreaGeometry(geojson.geometry);
            }

            if (type === 'featurecollection' && Array.isArray(geojson.features)) {
                return geojson.features.some((feature) => hasAreaGeometry(feature));
            }

            return false;
        }

        function escapeOverpassRegex(input) {
            return String(input).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        function getBarangayPinOverride(cityKey, barangayLabel) {
            const cityOverrides = barangayPinOverrides[cityKey];
            if (!cityOverrides) {
                return null;
            }

            const normalized = String(barangayLabel || '').trim().toLowerCase();
            return cityOverrides[normalized] || null;
        }

        function getCenterFromGeoJson(geojson) {
            if (!hasAreaGeometry(geojson)) {
                return null;
            }

            try {
                const layer = L.geoJSON(geojson);
                const bounds = layer.getBounds();
                if (!bounds.isValid()) {
                    return null;
                }

                const center = bounds.getCenter();
                return [center.lat, center.lng];
            } catch {
                return null;
            }
        }

        async function fetchBarangayBoundaryFromOverpass(cityKey, barangayLabel) {
            if (typeof osmtogeojson !== 'function') {
                return null;
            }

            const city = cityPresets[cityKey];
            if (!city?.osmId) {
                return null;
            }

            const escapedBarangayRegex = escapeOverpassRegex(barangayLabel);
            const overpassQuery = `[out:json][timeout:35];\nrel(${city.osmId})->.city_rel;\nmap_to_area.city_rel->.city_area;\n(\n  relation(area.city_area)["boundary"="administrative"]["name"~"^(Barangay )?${escapedBarangayRegex}$",i];\n  way(area.city_area)["boundary"="administrative"]["name"~"^(Barangay )?${escapedBarangayRegex}$",i];\n);\nout body geom;`;

            try {
                const response = await fetch('https://overpass-api.de/api/interpreter', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'text/plain;charset=UTF-8',
                        Accept: 'application/json',
                    },
                    body: overpassQuery,
                });

                if (!response.ok) {
                    console.error(`Overpass barangay lookup failed for ${barangayLabel}:`, response.status);
                    return null;
                }

                const overpassJson = await response.json();
                const geojson = osmtogeojson(overpassJson);
                if (!hasAreaGeometry(geojson)) {
                    return null;
                }

                return geojson;
            } catch (err) {
                console.error(`Error fetching Overpass boundary for ${barangayLabel}:`, err);
                return null;
            }
        }

        function resetBarangaySelect(cityKey) {
            if (!barangaySelectEl || !barangaySelectWrapEl) {
                return;
            }

            const barangays = barangaysByCity[cityKey] || [];
            barangaySelectEl.innerHTML = '<option value="">Choose Barangay</option>';

            if (!barangays.length) {
                barangaySelectWrapEl.hidden = true;
                barangaySelectEl.disabled = true;
                return;
            }

            barangays.forEach((barangay) => {
                const option = document.createElement('option');
                option.value = barangay.toLowerCase().replace(/\s+/g, '-');
                option.textContent = barangay;
                barangaySelectEl.appendChild(option);
            });

            barangaySelectWrapEl.hidden = false;
            barangaySelectEl.disabled = false;
        }

        function getTagStyle(tagType) {
            const styleMap = {
                'Pin Location': {
                    className: 'tag-pin-location',
                    icon: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7-5.4-7-11a7 7 0 1 1 14 0c0 5.6-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>',
                },
                'High Risk': {
                    className: 'tag-high-risk',
                    icon: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l9 16H3z"/><path d="M12 9v5"/><circle cx="12" cy="17" r="1"/></svg>',
                },
                'Dumping Site': {
                    className: 'tag-dumping-site',
                    icon: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"/><path d="M9 7V5h6v2"/><path d="M7 7l1 12h8l1-12"/><path d="M10 11v5"/><path d="M14 11v5"/></svg>',
                },
                'Contaminated Water': {
                    className: 'tag-contaminated-water',
                    icon: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3C9 8 6 10.5 6 14a6 6 0 0 0 12 0c0-3.5-3-6-6-11z"/><path d="M9.5 14.5a2.5 2.5 0 0 0 5 0"/></svg>',
                },
                'Illegal Burning': {
                    className: 'tag-illegal-burning',
                    icon: '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3c2 3 1 4.5.2 6.1C11.4 10.8 11 12 12 13.7c.8-1.1 2.2-1.9 3.9-1.7 2.6.3 4.1 2.4 4.1 4.8A8 8 0 1 1 8.2 8.5C9.5 7 10.4 5.3 12 3z"/></svg>',
                },
                'Blocked Drainage': {
                    className: 'tag-blocked-drainage',
                    icon: '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="8" width="16" height="10" rx="1"/><path d="M8 8v10"/><path d="M12 8v10"/><path d="M16 8v10"/><path d="M4 12h16"/><path d="M4 15h16"/></svg>',
                },
                Other: {
                    className: 'tag-other',
                    icon: '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="2"/></svg>',
                },
            };

            return styleMap[tagType] || styleMap.Other;
        }

        function saveTags() {
            const allTags = [];

            tagLayer.eachLayer((layer) => {
                const { tagType, note, timestamp } = layer.options.meta || {};
                const { lat, lng } = layer.getLatLng();
                allTags.push({ lat, lng, tagType, note, timestamp });
            });

            localStorage.setItem(savedTagsKey, JSON.stringify(allTags));
        }

        function addTagMarker(lat, lng, tagType, note, timestamp = Date.now()) {
            const style = getTagStyle(tagType);
            const marker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'tag-icon-wrapper',
                    html: `<span class="tag-pin ${style.className}" aria-hidden="true">${style.icon}</span>`,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15],
                    popupAnchor: [0, -12],
                }),
                meta: { tagType, note, timestamp },
            }).addTo(tagLayer);

            const noteHtml = note ? `<br><strong>Note:</strong> ${note}` : '';
            const stamp = new Date(timestamp).toLocaleString();
            marker.bindPopup(`
                <strong>${tagType}</strong>${noteHtml}<br><small>${stamp}</small><br>
                <button type="button" class="tag-popup-remove">Remove Tag</button>
            `);

            marker.on('popupopen', (event) => {
                const popupEl = event.popup.getElement();
                const removeBtn = popupEl?.querySelector('.tag-popup-remove');
                if (!removeBtn) {
                    return;
                }

                removeBtn.addEventListener('click', (clickEvent) => {
                    clickEvent.preventDefault();
                    clickEvent.stopPropagation();

                    const confirmed = window.confirm('Are you sure you want to remove this tag?');
                    if (!confirmed) {
                        tagStatusEl.textContent = 'Tag removal cancelled.';
                        return;
                    }

                    const removedType = marker?.options?.meta?.tagType || 'Tag';
                    tagLayer.removeLayer(marker);
                    saveTags();
                    tagStatusEl.textContent = `${removedType} tag removed.`;
                    map.closePopup();
                }, { once: true });
            });

            return marker;
        }

        function loadSavedTags() {
            try {
                const parsed = JSON.parse(localStorage.getItem(savedTagsKey) || '[]');
                parsed.forEach((entry) => {
                    if (
                        typeof entry.lat === 'number'
                        && typeof entry.lng === 'number'
                        && typeof entry.tagType === 'string'
                    ) {
                        addTagMarker(entry.lat, entry.lng, entry.tagType, entry.note || '', entry.timestamp || Date.now());
                    }
                });
            } catch {
                localStorage.removeItem(savedTagsKey);
            }
        }

        // Map tag-type labels to the DB enum values accepted by the backend
        const tagTypeToDbType = {
            'Pin Location': 'incident',
            'High Risk': 'hotspot',
            'Dumping Site': 'dumping',
            'Contaminated Water': 'water',
            'Illegal Burning': 'incident',
            'Blocked Drainage': 'flood',
            'Other': 'incident',
        };

        async function createLocationTag() {
            if (!currentPosition) {
                tagStatusEl.textContent = 'Current location not ready. Allow location access first.';
                return;
            }

            const selectedType = tagTypeEl.value;
            const noteValue = tagNoteEl.value.trim();

            if (!selectedType) {
                tagStatusEl.textContent = 'Select a tag type first.';
                return;
            }

            const dbType = tagTypeToDbType[selectedType] || 'incident';

            tagStatusEl.textContent = 'Submitting tag for admin approval...';
            addTagBtn.disabled = true;

            try {
                // Get CSRF token
                let csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrf) {
                    const meta = document.createElement('meta');
                    meta.name = 'csrf-token';
                    meta.content = '{{ csrf_token() }}';
                    document.head.appendChild(meta);
                    csrf = meta.content;
                }

                // Reverse geocode to get barangay
                let detectedBarangay = '';
                try {
                    tagStatusEl.textContent = 'Detecting barangay location...';
                    const geoRes = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${currentPosition.lat}&lon=${currentPosition.lng}&zoom=14`);
                    if (geoRes.ok) {
                        const geoData = await geoRes.json();
                        const address = geoData.address || {};
                        detectedBarangay = address.village || address.suburb || address.neighbourhood || address.quarter || '';
                        
                        // Clean up "Barangay " prefix if nominatim returned it
                        detectedBarangay = detectedBarangay.replace(/^Barangay\s+/i, '').trim();
                    }
                } catch (e) {
                    console.warn('Could not reverse geocode barangay', e);
                }

                // Fallback to the dropdown if reverse geocode failed
                if (!detectedBarangay) {
                    const bSelect = document.getElementById('barangay-select');
                    if (bSelect && !bSelect.disabled && bSelect.value) {
                        const text = bSelect.options[bSelect.selectedIndex].text;
                        if (text && text !== 'Choose Barangay') {
                            detectedBarangay = text;
                        }
                    }
                }

                tagStatusEl.textContent = 'Submitting tag for admin approval...';

                const formData = new FormData();
                formData.append('name', selectedType + (noteValue ? ': ' + noteValue : ''));
                formData.append('description', noteValue || selectedType);
                formData.append('latitude', currentPosition.lat);
                formData.append('longitude', currentPosition.lng);
                formData.append('type', dbType);
                if (detectedBarangay) {
                    formData.append('barangay', detectedBarangay);
                }
                
                const imageInput = document.getElementById('tag-image');
                if (imageInput && imageInput.files[0]) {
                    formData.append('image', imageInput.files[0]);
                }

                const response = await fetch('/pins', {
                    method: 'POST',
                    body: formData,
                    headers: { 
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                });

                if (response.status === 413) {
                    tagStatusEl.textContent = '❌ Error: The image file is way too big (server rejected).';
                    addTagBtn.disabled = false;
                    return;
                }

                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    tagStatusEl.textContent = '❌ Server returned an invalid response (might be a file size issue).';
                    addTagBtn.disabled = false;
                    return;
                }

                if (response.ok && data.success) {
                    tagStatusEl.textContent = `✅ "${selectedType}" tag submitted! It will appear on the map once an admin approves it.`;
                    tagNoteEl.value = '';
                    tagTypeEl.value = '';
                    const imageInput = document.getElementById('tag-image');
                    if (imageInput) imageInput.value = '';

                    // Show a temporary pending marker so user knows where they tagged
                    const pendingIcon = L.divIcon({
                        className: 'tag-icon-wrapper',
                        html: `<span class="tag-pin" style="background:#ffc107;border-color:#888;" title="Pending approval"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l9 16H3z"/><path d="M12 9v5"/><circle cx="12" cy="17" r="1"/></svg></span>`,
                        iconSize: [30, 30],
                        iconAnchor: [15, 15],
                        popupAnchor: [0, -12],
                    });
                    L.marker([currentPosition.lat, currentPosition.lng], { icon: pendingIcon })
                        .addTo(map)
                        .bindPopup(`<b>${selectedType}</b><br><small style="color:#e67e00">⏳ Pending admin approval</small>`)
                        .openPopup();
                } else {
                    let errorMessage = data.message || 'Could not submit. Try again.';
                    if (data.errors) {
                        errorMessage = Object.values(data.errors).flat().join(' ');
                    }
                    tagStatusEl.textContent = '❌ Error: ' + errorMessage;
                }
            } catch (err) {
                tagStatusEl.textContent = '❌ Network error. Check your connection or the file size.';
                console.error(err);
            } finally {
                addTagBtn.disabled = false;
            }
        }

        function clearLocationTags() {
            const layers = [];
            tagLayer.eachLayer((layer) => {
                layers.push(layer);
            });

            if (!layers.length) {
                tagStatusEl.textContent = 'No tags to remove yet.';
                return;
            }

            const confirmed = window.confirm('Remove the latest tag?');
            if (!confirmed) {
                tagStatusEl.textContent = 'Tag removal cancelled.';
                return;
            }

            const latestLayer = layers.reduce((latest, layer) => {
                const latestStamp = Number(latest?.options?.meta?.timestamp || 0);
                const layerStamp = Number(layer?.options?.meta?.timestamp || 0);
                return layerStamp >= latestStamp ? layer : latest;
            }, layers[0]);

            const removedType = latestLayer?.options?.meta?.tagType || 'Latest';
            tagLayer.removeLayer(latestLayer);
            saveTags();
            tagStatusEl.textContent = `${removedType} tag removed.`;
        }

        function toggleSatelliteMode() {
            if (isSatelliteMode) {
                map.removeLayer(satelliteLayer);
                streetLayer.addTo(map);
                satelliteModeLabelEl.textContent = 'Satellite: Off';
                isSatelliteMode = false;
                return;
            }

            map.removeLayer(streetLayer);
            satelliteLayer.addTo(map);
            satelliteModeLabelEl.textContent = 'Satellite: On';
            isSatelliteMode = true;
        }

        function ensureUserMarker(position, forceRecreate = false) {
            if (userMarker && !forceRecreate) {
                userMarker.setLatLng(position);
                return;
            }

            // Remove old marker if forcing recreation
            if (userMarker) {
                map.removeLayer(userMarker);
                userMarker = null;
            }

            userMarker = L.marker(position, { draggable: true }).addTo(map);
            userMarker.on('dragstart', () => {
                if (watchId !== null) {
                    stopTracking();
                }
            });
            userMarker.on('dragend', (event) => {
                const draggedLatLng = event.target.getLatLng();
                updatePositionFromMarker(draggedLatLng.lat, draggedLatLng.lng);
            });
        }

        async function fetchCityBoundary(cityKey) {
            if (cityBoundaryCache.has(cityKey)) {
                return cityBoundaryCache.get(cityKey);
            }

            const city = cityPresets[cityKey];
            if (!city?.osmId) {
                return null;
            }

            const osmIdString = `R${city.osmId}`;
            const endpoint = `https://nominatim.openstreetmap.org/lookup?osm_ids=${osmIdString}&format=json&polygon_geojson=1`;
            try {
                const response = await fetch(endpoint, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!response.ok) {
                    console.error(`Boundary lookup failed for OSM R${city.osmId}:`, response.status);
                    return null;
                }

                const result = await response.json();
                const resultArray = Array.isArray(result) ? result : [result];
                const boundary = resultArray[0]?.geojson || null;
                cityBoundaryCache.set(cityKey, boundary);
                return boundary;
            } catch (err) {
                console.error(`Error fetching boundary for ${cityKey}:`, err);
                return null;
            }
        }

        async function fetchOsmLookupGeometry(osmType, osmId) {
            const typeMap = {
                relation: 'R',
                way: 'W',
                node: 'N',
            };

            const prefix = typeMap[String(osmType || '').toLowerCase()];
            if (!prefix || !osmId) {
                return null;
            }

            const endpoint = `https://nominatim.openstreetmap.org/lookup?osm_ids=${prefix}${osmId}&format=jsonv2&polygon_geojson=1`;
            try {
                const response = await fetch(endpoint, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!response.ok) {
                    return null;
                }

                const result = await response.json();
                const first = Array.isArray(result) ? result[0] : result;
                const geojson = first?.geojson || null;
                return hasAreaGeometry(geojson) ? geojson : null;
            } catch {
                return null;
            }
        }

        async function fetchBarangayBoundary(cityKey, barangayLabel) {
            const cacheKey = `${cityKey}:${barangayLabel.toLowerCase()}`;
            if (barangayBoundaryCache.has(cacheKey)) {
                return barangayBoundaryCache.get(cacheKey);
            }

            const city = cityPresets[cityKey];
            if (!city || !barangayLabel) {
                return null;
            }

            const query = `Barangay ${barangayLabel}, ${city.label}, Philippines`;
            const endpoint = `https://nominatim.openstreetmap.org/search?format=jsonv2&polygon_geojson=1&addressdetails=1&limit=8&countrycodes=ph&q=${encodeURIComponent(query)}`;

            try {
                // First try city-scoped Overpass so we stay inside the selected city.
                const overpassGeoJson = await fetchBarangayBoundaryFromOverpass(cityKey, barangayLabel);
                if (overpassGeoJson) {
                    const overpassCenter = getCenterFromGeoJson(overpassGeoJson);
                    const overpassPayload = {
                        geojson: overpassGeoJson,
                        lat: Number(overpassCenter?.[0] ?? city.center[0]),
                        lon: Number(overpassCenter?.[1] ?? city.center[1]),
                    };
                    barangayBoundaryCache.set(cacheKey, overpassPayload);
                    return overpassPayload;
                }

                // Fallback to Nominatim only if Overpass has no match.
                const response = await fetch(endpoint, {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                if (!response.ok) {
                    console.error(`Barangay lookup failed for ${query}:`, response.status);
                    return null;
                }

                const result = await response.json();
                const resultList = Array.isArray(result) ? result : [];
                const targetBarangay = barangayLabel.trim().toLowerCase();
                const targetCity = city.label.toLowerCase();

                const first = resultList.find((item) => {
                    const address = item?.address || {};
                    const nameCandidates = [
                        address.suburb,
                        address.neighbourhood,
                        address.village,
                        address.quarter,
                        item?.name,
                    ]
                        .filter(Boolean)
                        .map((value) => String(value).toLowerCase());

                    const displayName = String(item?.display_name || '').toLowerCase();
                    const isBarangayMatch = nameCandidates.some((value) => value.includes(targetBarangay));
                    const isCityMatch = displayName.includes(targetCity);
                    return isBarangayMatch && isCityMatch;
                }) || null;

                const payload = first
                    ? {
                        geojson: first.geojson || null,
                        lat: Number(first.lat),
                        lon: Number(first.lon),
                        boundingbox: Array.isArray(first.boundingbox)
                            ? first.boundingbox.map((value) => Number(value))
                            : null,
                    }
                    : null;

                if (first && !hasAreaGeometry(payload?.geojson)) {
                    const lookupGeoJson = await fetchOsmLookupGeometry(first.osm_type, first.osm_id);
                    if (lookupGeoJson) {
                        payload.geojson = lookupGeoJson;
                        const lookupCenter = getCenterFromGeoJson(lookupGeoJson);
                        if (lookupCenter) {
                            payload.lat = Number(lookupCenter[0]);
                            payload.lon = Number(lookupCenter[1]);
                        }
                    }
                }

                barangayBoundaryCache.set(cacheKey, payload);
                return payload;
            } catch (err) {
                console.error(`Error fetching barangay boundary for ${query}:`, err);
                return null;
            }
        }

        async function highlightCity(cityKey) {
            cityLayer.clearLayers();

            if (!cityKey || !cityPresets[cityKey]) {
                setCityStatus('Select a city to zoom and highlight.');
                return;
            }

            const city = cityPresets[cityKey];
            setCityStatus(`Loading ${city.label} boundary...`);

            let boundaryLoaded = false;
            try {
                const boundaryGeoJson = await fetchCityBoundary(cityKey);
                if (boundaryGeoJson) {
                    const boundaryLayer = L.geoJSON(boundaryGeoJson, {
                        style: {
                            color: '#0b6d5a',
                            fillColor: '#00c9a2',
                            fillOpacity: 0.18,
                            weight: 2,
                        },
                    }).addTo(cityLayer);

                    const bounds = boundaryLayer.getBounds();
                    if (bounds.isValid()) {
                        if (watchId !== null) {
                            stopTracking();
                        }
                        setCityStatus(`✓ ${city.label} boundary highlighted. Locator remains draggable at current position.`);

                        map.fitBounds(bounds, {
                            padding: [20, 20],
                            animate: true,
                        });

                        boundaryLoaded = true;
                    }
                }
            } catch {
                boundaryLoaded = false;
            }

            if (!boundaryLoaded) {
                L.circle(city.center, {
                    radius: city.radius,
                    color: '#0b6d5a',
                    fillColor: '#00c9a2',
                    fillOpacity: 0.2,
                    weight: 2,
                }).addTo(cityLayer);

                if (watchId !== null) {
                    stopTracking();
                }
                
                map.flyTo(city.center, city.zoom, {
                    animate: true,
                    duration: 0.8,
                });

                setCityStatus(`${city.label} approximate highlight shown. Locator remains draggable at current position.`);
                return;
            }

            setCityStatus(`${city.label} boundary highlighted. Locator remains draggable at current position.`);
        }

        async function highlightBarangay(cityKey, barangayValue) {
            barangayLayer.clearLayers();

            if (!cityKey || !barangayValue) {
                return;
            }

            const city = cityPresets[cityKey];
            const barangayLabel = getSelectedBarangayLabel();
            if (!city || !barangayLabel) {
                return;
            }

            setCityStatus(`Loading ${barangayLabel} highlight...`);

            let barangayData = null;
            try {
                barangayData = await fetchBarangayBoundary(cityKey, barangayLabel);
                const overridePoint = getBarangayPinOverride(cityKey, barangayLabel);
                const point = Array.isArray(overridePoint)
                    ? overridePoint
                    : (Number.isFinite(barangayData?.lat) && Number.isFinite(barangayData?.lon)
                        ? [barangayData.lat, barangayData.lon]
                        : city.center);

                L.circle(point, {
                    radius: 400,
                    color: '#1565c0',
                    fillColor: '#42a5f5',
                    fillOpacity: 0.2,
                    weight: 2,
                }).addTo(barangayLayer);

                L.circleMarker(point, {
                    radius: 7,
                    color: '#ffffff',
                    fillColor: '#1565c0',
                    fillOpacity: 1,
                    weight: 2,
                })
                    .bindPopup(`${barangayLabel} (pin highlight)`)
                    .addTo(barangayLayer);

                map.flyTo(point, 15, {
                    animate: true,
                    duration: 0.8,
                });

                if (Array.isArray(overridePoint)) {
                    setCityStatus(`${barangayLabel} highlighted by circle (manual pinned location).`);
                } else {
                    setCityStatus(`${barangayLabel} highlighted by circle.`);
                }
                return;
            } catch {
                // Keep fallback below.
            }

            L.circle(city.center, {
                radius: 400,
                color: '#1565c0',
                fillColor: '#42a5f5',
                fillOpacity: 0.2,
                weight: 2,
            }).addTo(barangayLayer);

            map.flyTo(city.center, city.zoom, {
                animate: true,
                duration: 0.8,
            });

            setCityStatus(`${barangayLabel} highlighted by circle.`);
        }

        function refreshMapSize() {
            requestAnimationFrame(() => {
                map.invalidateSize({ pan: false, animate: false });
            });
        }

        function getZoomFromAccuracy(accuracy) {
            if (accuracy <= 30) return 17;
            if (accuracy <= 100) return 16;
            if (accuracy <= 500) return 15;
            return 14;
        }

        function updatePositionFromMarker(lat, lng) {
            currentPosition = { lat, lng };
            coordsEl.textContent = `Pinned location: ${lat.toFixed(6)}, ${lng.toFixed(6)} (manual)`;
            tagStatusEl.textContent = 'Locator pin moved. You can now add a tag at this position.';

            if (accuracyCircle) {
                accuracyCircle.setLatLng([lat, lng]);
                accuracyCircle.setRadius(10);
            }
        }

        function setUserLocation(lat, lng, accuracy) {
            const position = [lat, lng];
            const safeAccuracy = Math.max(accuracy || 0, 10);
            currentPosition = { lat, lng };

            ensureUserMarker(position);

            if (accuracyCircle) {
                accuracyCircle.setLatLng(position);
                accuracyCircle.setRadius(safeAccuracy);
            } else {
                accuracyCircle = L.circle(position, {
                    radius: safeAccuracy,
                    color: '#00a887',
                    fillColor: '#00c9a2',
                    fillOpacity: 0.18,
                    weight: 1,
                }).addTo(map);
            }

            userMarker.bindPopup('You are here').openPopup();
            map.setView(position, getZoomFromAccuracy(safeAccuracy));

            coordsEl.textContent = `Latitude: ${lat.toFixed(6)} | Longitude: ${lng.toFixed(6)} | Accuracy: ${Math.round(safeAccuracy)} m`;
        }

        function stopTracking() {
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            trackBtn.textContent = 'Track My Location';
            trackBtn.classList.remove('alt');
        }

        function startTracking() {
            if (!('geolocation' in navigator)) {
                coordsEl.textContent = 'Geolocation is not supported in this browser.';
                return;
            }

            if (watchId !== null) {
                stopTracking();
                return;
            }

            trackBtn.textContent = 'Stop Tracking';
            trackBtn.classList.add('alt');
            coordsEl.textContent = 'Tracking your location...';

            watchId = navigator.geolocation.watchPosition(
                (pos) => {
                    setUserLocation(pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy || 0);
                },
                (err) => {
                    coordsEl.textContent = `Tracking error (${err.message}).`;
                    stopTracking();
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                }
            );
        }

        trackBtn.addEventListener('click', startTracking);
        addTagBtn.addEventListener('click', createLocationTag);
        satelliteModeBtn.addEventListener('click', toggleSatelliteMode);
        citySelectEl.addEventListener('change', async () => {
            barangayLayer.clearLayers();
            resetBarangaySelect(citySelectEl.value);
            await highlightCity(citySelectEl.value);
        });
        barangaySelectEl.addEventListener('change', async () => {
            await highlightBarangay(citySelectEl.value, barangaySelectEl.value);
        });

        resetBarangaySelect(citySelectEl.value);

        loadSavedTags();

        // Load admin-approved pins from the server and show them on the map
        fetch('/api/pins')
            .then(res => res.json())
            .then(pins => {
                pins.forEach(pin => {
                    const style = getTagStyle(
                        Object.keys(tagTypeToDbType).find(k => tagTypeToDbType[k] === pin.type) || 'Other'
                    );
                    const icon = L.divIcon({
                        className: 'tag-icon-wrapper',
                        html: `<span class="tag-pin ${style.className}" title="${pin.type}">${style.icon}</span>`,
                        iconSize: [30, 30],
                        iconAnchor: [15, 15],
                        popupAnchor: [0, -12],
                    });
                    L.marker([pin.latitude, pin.longitude], { icon })
                        .addTo(map)
                        .bindPopup(`<b>${pin.name}</b><br>${pin.description || ''}<br><small>By: ${pin.user?.name || 'Anonymous'}</small>`);
                });
            })
            .catch(err => console.warn('Could not load approved pins:', err));

        map.whenReady(refreshMapSize);
        window.addEventListener('load', refreshMapSize);
        window.addEventListener('resize', refreshMapSize);
        window.addEventListener('orientationchange', refreshMapSize);
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                refreshMapSize();
            }
        });

        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    setUserLocation(pos.coords.latitude, pos.coords.longitude, pos.coords.accuracy || 0);
                },
                (err) => {
                    coordsEl.textContent = `Location unavailable (${err.message}). Showing default map center.`;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0,
                }
            );
        } else {
            coordsEl.textContent = 'Geolocation is not supported in this browser.';
        }
    </script>
</body>
</html>
