<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Clean Earth Interactive Mapping</title>
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
            font-size: 28px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 10px;
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
            border-radius: 14px;
            box-shadow: 0 10px 20px rgba(0, 121, 101, 0.08);
            padding: 12px;
            display: grid;
            gap: 10px;
            position: sticky;
            top: 102px;
        }

        .sidebar-title {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #55706a;
            padding: 4px 2px;
        }

        .sidebar .nav-link,
        .sidebar .btn {
            justify-content: flex-start;
            width: 100%;
            box-sizing: border-box;
        }

        .report-panel {
            margin-top: 4px;
            border-top: 1px solid #e2f1ed;
            padding-top: 10px;
            display: grid;
            gap: 8px;
        }

        .panel-label {
            margin: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #55706a;
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

        .field:focus,
        .field-input:focus {
            outline: none;
            border-color: #00c9a2;
            box-shadow: 0 0 0 3px rgba(0, 201, 162, 0.12);
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
        }

        .btn.alt {
            background: #ffffff;
            color: #0b6d5a;
            border-color: #8edac9;
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
            }

            #map {
                height: clamp(280px, 48vh, 420px);
            }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="brand">
            <div class="brand-logo"><img src="https://via.placeholder.com/40" alt="Logo"></div>
            <span>Clean Earth Interactive Mapping</span>
        </div>
        <span class="topbar-note">Dashboard</span>
    </header>

    <main class="main-content">
        <div class="dashboard-shell">
            <div class="dashboard-layout">
                <aside class="sidebar" aria-label="Dashboard sidebar">
                    <p class="sidebar-title">Navigation</p>
                    <a class="nav-link" href="{{ url('/') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 10.5L12 3l9 7.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 9.5V20h13V9.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        Home
                    </a>
                    <a class="nav-link nav-auth" href="{{ route('login') }}">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10 17l5-5-5-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 12H3" stroke="currentColor" stroke-linecap="round"/><path d="M21 4v16" stroke="currentColor" stroke-linecap="round"/></svg>
                        Login
                    </a>
                    <button id="track-location-btn" class="btn" type="button">Track My Location</button>

                    <section class="report-panel" aria-label="Location tagging">
                        <p class="panel-label">Leave a tag on your location</p>
                        <select id="tag-type" class="field" aria-label="Tag type">
                            <option value="High Risk">High Risk</option>
                            <option value="Dumping Site">Dumping Site</option>
                            <option value="Contaminated Water">Contaminated Water</option>
                            <option value="Illegal Burning">Illegal Burning</option>
                            <option value="Blocked Drainage">Blocked Drainage</option>
                            <option value="Other">Other</option>
                        </select>
                        <input id="tag-note" class="field-input" type="text" maxlength="90" placeholder="Optional note (ex: near creek entrance)">
                        <div class="report-actions">
                            <button id="add-location-tag" class="btn" type="button">Add Tag Here</button>
                            <button id="clear-location-tags" class="btn alt" type="button">Clear My Tags</button>
                        </div>
                        <p id="tag-status" class="tag-help">Set your location first, then add a tag.</p>
                    </section>
                </aside>

                <div class="content-stack">
                    <section class="dashboard-topbar">
                        <div>
                            <h1>Mapping Dashboard</h1>
                            <p class="meta">Live map view with your current location pin.</p>
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
    <script>
        const defaultCenter = [14.5995, 120.9842];
        const map = L.map('map').setView(defaultCenter, 12);
        const coordsEl = document.getElementById('coords');
        const trackBtn = document.getElementById('track-location-btn');
        const tagTypeEl = document.getElementById('tag-type');
        const tagNoteEl = document.getElementById('tag-note');
        const addTagBtn = document.getElementById('add-location-tag');
        const clearTagsBtn = document.getElementById('clear-location-tags');
        const tagStatusEl = document.getElementById('tag-status');
        const savedTagsKey = 'dashboard-location-tags-v1';

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        let userMarker = null;
        let accuracyCircle = null;
        let watchId = null;
        let currentPosition = null;
        const tagLayer = L.layerGroup().addTo(map);

        function getTagStyle(tagType) {
            const styleMap = {
                'High Risk': { color: '#d94848', fillColor: '#f06d6d' },
                'Dumping Site': { color: '#a35f00', fillColor: '#e2a13f' },
                'Contaminated Water': { color: '#1a6ca8', fillColor: '#4ea5dd' },
                'Illegal Burning': { color: '#7d3f98', fillColor: '#b46ad6' },
                'Blocked Drainage': { color: '#355e3b', fillColor: '#6baa6f' },
                Other: { color: '#5f5f5f', fillColor: '#909090' },
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
            const marker = L.circleMarker([lat, lng], {
                radius: 9,
                color: style.color,
                fillColor: style.fillColor,
                fillOpacity: 0.88,
                weight: 2,
                meta: { tagType, note, timestamp },
            }).addTo(tagLayer);

            const noteHtml = note ? `<br><strong>Note:</strong> ${note}` : '';
            const stamp = new Date(timestamp).toLocaleString();
            marker.bindPopup(`<strong>${tagType}</strong>${noteHtml}<br><small>${stamp}</small>`);
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

        function createLocationTag() {
            if (!currentPosition) {
                tagStatusEl.textContent = 'Current location not ready. Allow location access first.';
                return;
            }

            const selectedType = tagTypeEl.value;
            const noteValue = tagNoteEl.value.trim();

            addTagMarker(currentPosition.lat, currentPosition.lng, selectedType, noteValue);
            saveTags();

            tagStatusEl.textContent = `${selectedType} tag added at your current location.`;
            tagNoteEl.value = '';
        }

        function clearLocationTags() {
            tagLayer.clearLayers();
            localStorage.removeItem(savedTagsKey);
            tagStatusEl.textContent = 'All saved tags cleared.';
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

        function setUserLocation(lat, lng, accuracy) {
            const position = [lat, lng];
            const safeAccuracy = Math.max(accuracy || 0, 10);
            currentPosition = { lat, lng };

            if (userMarker) {
                userMarker.setLatLng(position);
            } else {
                userMarker = L.marker(position).addTo(map);
            }

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
        clearTagsBtn.addEventListener('click', clearLocationTags);

        loadSavedTags();

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
