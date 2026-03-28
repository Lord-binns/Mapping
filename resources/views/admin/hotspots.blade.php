@extends('admin.layout')

@section('title', 'Barangay Heatmap')
@section('heading', 'Barangay Heatmap')
@section('subheading', 'Heat intensity of approved reports with city and barangay filtering.')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<style>
    /* ── Heatmap page layout ── */
    .heatmap-grid {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 14px;
        align-items: start;
    }

    .heatmap-card {
        background: #fff;
        border: 1px solid #d9ebe6;
        border-radius: 14px;
        box-shadow: 0 14px 28px rgba(0,121,101,.10);
        overflow: hidden;
    }

    .heatmap-card-header {
        padding: 12px 14px;
        border-bottom: 1px solid #e8f5f2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }

    .heatmap-card-header h3 {
        margin: 0;
        font-size: 13px;
        color: #0b6d5a;
        text-transform: uppercase;
        letter-spacing: .03em;
    }

    #hotspot-map {
        width: 100%;
        height: clamp(420px, 58vh, 600px);
    }

    /* ── Filter bar ── */
    .filter-bar {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .filter-btn {
        padding: 5px 12px;
        border-radius: 999px;
        border: 1px solid #c8efe4;
        background: #f8fffc;
        color: #0b6d5a;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        cursor: pointer;
        transition: all .16s;
    }

    .filter-btn.active, .filter-btn:hover {
        background: #0b6d5a;
        color: #fff;
        border-color: #0b6d5a;
    }

    .location-filters {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .location-filters select {
        height: 33px;
        min-width: 170px;
        border-radius: 10px;
        border: 1px solid #c8efe4;
        background: #f8fffc;
        color: #0b6d5a;
        font-size: 12px;
        font-weight: 700;
        padding: 0 10px;
        letter-spacing: .02em;
    }

    .location-filters select:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    .city-filter-status {
        padding: 10px 14px 0;
        font-size: 12px;
        color: #55706a;
    }

    /* ── Sidebar stat cards ── */
    .stat-stack { display: grid; gap: 10px; }

    .stat-card {
        background: #fff;
        border: 1px solid #d9ebe6;
        border-radius: 12px;
        padding: 12px 14px;
        box-shadow: 0 8px 16px rgba(0,121,101,.07);
    }

    .stat-card h4 {
        margin: 0 0 2px;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #55706a;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 800;
        color: #0b6d5a;
        line-height: 1.1;
    }

    .stat-sub {
        font-size: 12px;
        color: #7aada0;
        margin-top: 2px;
    }

    /* ── Legend ── */
    .legend-list {
        display: grid;
        gap: 6px;
        padding: 12px 14px;
        border-top: 1px solid #e8f5f2;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #1b1b1b;
    }

    .legend-dot {
        width: 13px;
        height: 13px;
        border-radius: 50%;
        border: 2px solid rgba(255,255,255,.7);
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
        flex-shrink: 0;
    }

    /* ── No-data state ── */
    .no-data {
        text-align: center;
        padding: 48px;
        color: #7aada0;
        font-size: 14px;
    }

    .no-data span { font-size: 36px; display: block; margin-bottom: 10px; }

    @media (max-width: 860px) {
        .heatmap-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

@php
    $allPins = \App\Models\Pin::where('status', 'verified')->with('user:id,name')->get();

    $typeCounts = $allPins->groupBy('type')->map->count();
    $total      = $allPins->count();
    
    $jsPins = $allPins->map(function($p) {
        return [
            'lat'  => (float) $p->latitude,
            'lng'  => (float) $p->longitude,
            'type' => $p->type,
            'barangay' => $p->barangay,
            'name' => $p->name,
            'desc' => $p->description ?? '',
            'user' => $p->user->name ?? 'Anonymous',
        ];
    })->values()->all();
@endphp

<div class="heatmap-grid">

    {{-- ── Main map panel ── --}}
    <div class="heatmap-card">
        <div class="heatmap-card-header">
            <h3>📍 Verified Pin Heatmap</h3>
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                <div class="filter-bar" id="filter-bar">
                    <button class="filter-btn active" data-type="all">All</button>
                    <button class="filter-btn" data-type="incident">Incident</button>
                    <button class="filter-btn" data-type="dumping">Dumping</button>
                    <button class="filter-btn" data-type="flood">Flood</button>
                    <button class="filter-btn" data-type="water">Water</button>
                    <button class="filter-btn" data-type="hotspot">Hotspot</button>
                </div>
                <div class="location-filters" aria-label="City and barangay filters">
                    <select id="city-select" aria-label="Select city">
                        <option value="">All Cities</option>
                        <option value="manolo-fortich">Manolo Fortich</option>
                        <option value="cagayan-de-oro">Cagayan de Oro City</option>
                    </select>
                    <select id="barangay-select" aria-label="Select barangay" disabled>
                        <option value="">All Barangays</option>
                    </select>
                </div>
                <button class="filter-btn" id="toggle-satellite">🛰 Satellite</button>
            </div>
        </div>

        <p id="city-filter-status" class="city-filter-status">Showing all verified pins across all cities and barangays.</p>

        @if($allPins->isEmpty())
            <div class="no-data">
                <span>🗺️</span>
                No verified pins yet. Approve some reports to see the heatmap.
            </div>
        @else
            <div id="hotspot-map"></div>
        @endif

        <div class="legend-list">
            <div class="legend-item"><span class="legend-dot" style="background:#d94848;"></span> Incident / Hotspot</div>
            <div class="legend-item"><span class="legend-dot" style="background:#a35f00;"></span> Illegal Dumping</div>
            <div class="legend-item"><span class="legend-dot" style="background:#1a6ca8;"></span> Water Quality</div>
            <div class="legend-item"><span class="legend-dot" style="background:#355e3b;"></span> Flood Zone</div>
            <div class="legend-item"><span class="legend-dot" style="background:#7d3f98;"></span> Hotspot Cluster</div>
        </div>
    </div>

    {{-- ── Sidebar stats ── --}}
    <div class="stat-stack">
        <div class="stat-card">
            <h4>Total Verified Pins</h4>
            <div class="stat-number">{{ $total }}</div>
            <div class="stat-sub">All approved reports</div>
        </div>

        @foreach(['incident' => ['#d94848','Incidents'], 'dumping' => ['#a35f00','Dumping Sites'], 'flood' => ['#355e3b','Flood Zones'], 'water' => ['#1a6ca8','Water Quality'], 'hotspot' => ['#7d3f98','Hotspot Clusters']] as $type => [$color, $label])
        <div class="stat-card">
            <h4 style="color:{{ $color }}">{{ $label }}</h4>
            <div class="stat-number" style="color:{{ $color }}">{{ $typeCounts[$type] ?? 0 }}</div>
            <div class="stat-sub">verified</div>
        </div>
        @endforeach

        {{-- Pending notice --}}
        @php $pendingCount = \App\Models\Pin::where('status','pending')->count(); @endphp
        @if($pendingCount > 0)
        <div class="stat-card" style="border-color:#ffc107; background:#fffdf0;">
            <h4 style="color:#b8860b;">Awaiting Approval</h4>
            <div class="stat-number" style="color:#b8860b;">{{ $pendingCount }}</div>
            <div class="stat-sub"><a href="{{ route('admin.reports') }}" style="color:#b8860b;">Review now →</a></div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
{{-- Leaflet.heat heatmap plugin --}}
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script>
(function () {
    const mapEl = document.getElementById('hotspot-map');
    if (!mapEl) return;

    // All pins passed from blade
    const pins = @json($jsPins);
    const citySelectEl = document.getElementById('city-select');
    const barangaySelectEl = document.getElementById('barangay-select');
    const cityFilterStatusEl = document.getElementById('city-filter-status');

    const cityPresets = {
        'manolo-fortich': {
            label: 'Manolo Fortich',
            center: [8.3698, 124.8645],
            zoom: 12,
        },
        'cagayan-de-oro': {
            label: 'Cagayan de Oro City',
            center: [8.4542, 124.6319],
            zoom: 12,
        },
    };

    const barangaysByCity = {
        'manolo-fortich': [
            'Agusan Canyon', 'Alae', 'Dalirig', 'Damilag', 'Dicklum',
            'Kalugmanan', 'Lindaban', 'Lingion', 'Lunocan', 'Maluko',
            'Santiago', 'Sankanan', 'Santo Nino', 'Tankulan', 'Ticala',
        ],
        'cagayan-de-oro': [
            'Balulang', 'Bugo', 'Carmen', 'Gusa', 'Kauswagan',
            'Lapasan', 'Macasandig', 'Nazareth', 'Patag', 'Puntod',
        ],
    };

    const typeColorMap = {
        incident: '#d94848',
        dumping:  '#a35f00',
        water:    '#1a6ca8',
        flood:    '#355e3b',
        hotspot:  '#7d3f98',
    };

    function normalizeBarangayName(value) {
        return String(value || '')
            .toLowerCase()
            .replace(/^barangay\s+/i, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\bsto\.?\b/g, 'santo')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function inferCityFromCoords(pin) {
        const lat = Number(pin?.lat);
        const lng = Number(pin?.lng);
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
            return '';
        }

        const nearestCity = Object.entries(cityPresets)
            .map(([cityKey, city]) => {
                const latDiff = lat - city.center[0];
                const lngDiff = lng - city.center[1];
                return { cityKey, score: (latDiff * latDiff) + (lngDiff * lngDiff) };
            })
            .sort((a, b) => a.score - b.score)[0];

        return nearestCity?.cityKey || '';
    }

    const cityByBarangayNormalized = Object.entries(barangaysByCity).reduce((acc, [cityKey, barangays]) => {
        barangays.forEach((barangay) => {
            acc[normalizeBarangayName(barangay)] = cityKey;
        });
        return acc;
    }, {});

    const pinsWithLocationMeta = pins.map((pin) => {
        const normalizedBarangay = normalizeBarangayName(pin.barangay);
        const cityKey = cityByBarangayNormalized[normalizedBarangay] || inferCityFromCoords(pin);

        return {
            ...pin,
            _barangayNormalized: normalizedBarangay,
            _cityKey: cityKey,
        };
    });

    // ── Init map ──────────────────────────────────
    const map = L.map(mapEl, { zoomControl: true }).setView([8.4, 124.7], 10);

    const streetLayer = L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        { maxZoom: 19, attribution: '© OpenStreetMap contributors' }
    ).addTo(map);

    const satelliteLayer = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        { maxZoom: 19, attribution: 'Tiles © Esri' }
    );

    let useSatellite = false;
    document.getElementById('toggle-satellite')?.addEventListener('click', function () {
        useSatellite = !useSatellite;
        if (useSatellite) {
            map.removeLayer(streetLayer);
            satelliteLayer.addTo(map);
            this.textContent = '🛰 Street View';
        } else {
            map.removeLayer(satelliteLayer);
            streetLayer.addTo(map);
            this.textContent = '🛰 Satellite';
        }
    });

    // ── Layers ───────────────────────────────────
    let markerLayer = L.layerGroup().addTo(map);
    let heatLayer   = null;
    let selectedType = 'all';

    function setCityFilterStatus(message) {
        if (cityFilterStatusEl) {
            cityFilterStatusEl.textContent = message;
        }
    }

    function resetBarangaySelect(cityKey) {
        if (!barangaySelectEl) {
            return;
        }

        const barangays = barangaysByCity[cityKey] || [];
        barangaySelectEl.innerHTML = '<option value="">All Barangays</option>';

        if (!barangays.length) {
            barangaySelectEl.disabled = true;
            return;
        }

        barangays.forEach((barangay) => {
            const option = document.createElement('option');
            option.value = normalizeBarangayName(barangay);
            option.textContent = barangay;
            barangaySelectEl.appendChild(option);
        });

        barangaySelectEl.disabled = false;
    }

    function getFilteredPins() {
        const selectedCity = citySelectEl?.value || '';
        const selectedBarangay = normalizeBarangayName(barangaySelectEl?.value || '');

        const cityTypePins = pinsWithLocationMeta.filter((pin) => {
            const typeMatch = selectedType === 'all' || pin.type === selectedType;
            if (!typeMatch) {
                return false;
            }

            if (selectedCity && pin._cityKey !== selectedCity) {
                return false;
            }

            return true;
        });

        const exactBarangayPins = cityTypePins.filter((pin) => {
            if (selectedBarangay && pin._barangayNormalized !== selectedBarangay) {
                return false;
            }

            return true;
        });

        if (selectedBarangay && !exactBarangayPins.length) {
            return {
                pins: cityTypePins,
                usedBarangayFallback: true,
            };
        }

        return {
            pins: exactBarangayPins,
            usedBarangayFallback: false,
        };
    }

    function computeHeatPoints(filteredPins) {
        const latLngs = filteredPins.map((pin) => L.latLng(pin.lat, pin.lng));
        const mergeRadiusMeters = 450;

        return filteredPins.map((pin, pinIndex) => {
            let nearbyCount = 0;

            for (let i = 0; i < latLngs.length; i += 1) {
                const distance = map.distance(latLngs[pinIndex], latLngs[i]);
                if (distance <= mergeRadiusMeters) {
                    nearbyCount += 1;
                }
            }

            const intensity = Math.min(1, 0.25 + ((nearbyCount - 1) * 0.18));
            return [pin.lat, pin.lng, intensity];
        });
    }

    function buildMarkers(filteredPins) {
        markerLayer.clearLayers();

        filteredPins.forEach(pin => {
            const color = typeColorMap[pin.type] || '#5f5f5f';

            const icon = L.divIcon({
                className: '',
                html: `<div style="
                    width:14px; height:14px; border-radius:50%;
                    background:${color};
                    border:2px solid #fff;
                    box-shadow:0 2px 6px rgba(0,0,0,.35);
                "></div>`,
                iconSize: [14, 14],
                iconAnchor: [7, 7],
                popupAnchor: [0, -8],
            });

            L.marker([pin.lat, pin.lng], { icon })
                .addTo(markerLayer)
                .bindPopup(`
                    <strong style="color:${color}">${pin.name}</strong>
                    <br><span style="font-size:11px;text-transform:uppercase;color:#888;">${pin.type}</span>
                    ${pin.barangay ? `<br><span style="font-size:11px;color:#666;">Barangay: ${pin.barangay}</span>` : ''}
                    ${pin.desc ? `<br><span style="font-size:12px;">${pin.desc}</span>` : ''}
                    <br><small style="color:#aaa;">By ${pin.user}</small>
                `);
        });
    }

    function buildHeat(filteredPins) {
        if (heatLayer) { map.removeLayer(heatLayer); heatLayer = null; }

        const pts = computeHeatPoints(filteredPins);
        if (!pts.length) return;

        heatLayer = L.heatLayer(pts, {
            radius:  36,
            blur:    30,
            minOpacity: 0.4,
            max: 1.0,
            maxZoom: 17,
            gradient: { 0.15: '#00c9a2', 0.35: '#ffc200', 0.55: '#ff9f1c', 0.75: '#ff5400', 1.0: '#c1121f' },
        }).addTo(map);
    }

    function fitToPins(filteredPins) {
        if (!filteredPins.length) {
            const selectedCity = citySelectEl?.value || '';
            const cityPreset = cityPresets[selectedCity];
            if (cityPreset) {
                map.flyTo(cityPreset.center, cityPreset.zoom, {
                    animate: true,
                    duration: 0.7,
                });
            }
            return;
        }

        const latlngs = filteredPins.map(p => [p.lat, p.lng]);
        map.fitBounds(L.latLngBounds(latlngs), { padding: [30, 30] });
    }

    function render(shouldFit = false) {
        const { pins: filteredPins, usedBarangayFallback } = getFilteredPins();
        buildMarkers(filteredPins);
        buildHeat(filteredPins);

        const selectedCity = citySelectEl?.value || '';
        const selectedBarangayOption = barangaySelectEl?.options[barangaySelectEl.selectedIndex];
        const selectedBarangayLabel = selectedBarangayOption?.value ? selectedBarangayOption.textContent : '';
        const cityLabel = cityPresets[selectedCity]?.label || 'all cities';

        if (!filteredPins.length) {
            setCityFilterStatus(`No verified pins match the current filters in ${selectedBarangayLabel || cityLabel}.`);
        } else if (usedBarangayFallback && selectedBarangayLabel) {
            setCityFilterStatus(`No exact barangay match for ${selectedBarangayLabel}; showing ${filteredPins.length} pin(s) from ${cityLabel} instead.`);
        } else if (selectedBarangayLabel) {
            setCityFilterStatus(`Showing ${filteredPins.length} verified pin(s) in ${selectedBarangayLabel}, ${cityLabel}.`);
        } else if (selectedCity) {
            setCityFilterStatus(`Showing ${filteredPins.length} verified pin(s) in ${cityLabel}.`);
        } else {
            setCityFilterStatus(`Showing ${filteredPins.length} verified pin(s) across all cities and barangays.`);
        }

        if (shouldFit) {
            fitToPins(filteredPins);
        }
    }

    // ── Filter buttons ────────────────────────────
    document.querySelectorAll('.filter-btn[data-type]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn[data-type]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedType = btn.dataset.type || 'all';
            render(true);
        });
    });

    citySelectEl?.addEventListener('change', () => {
        const cityKey = citySelectEl.value;
        resetBarangaySelect(cityKey);
        if (barangaySelectEl) {
            barangaySelectEl.value = '';
        }
        render(true);
    });

    barangaySelectEl?.addEventListener('change', () => {
        render(true);
    });

    resetBarangaySelect(citySelectEl?.value || '');

    // ── Initial render ────────────────────────────
    render(true);

    setTimeout(() => map.invalidateSize(), 100);
})();
</script>
@endpush
