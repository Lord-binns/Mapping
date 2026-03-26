@extends('admin.layout')

@section('title', 'Hotspot Heatmap')
@section('heading', 'Hotspot Heatmap')
@section('subheading', 'Live density map generated from approved environmental reports.')

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
                <button class="filter-btn" id="toggle-satellite">🛰 Satellite</button>
            </div>
        </div>

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

    const typeColorMap = {
        incident: '#d94848',
        dumping:  '#a35f00',
        water:    '#1a6ca8',
        flood:    '#355e3b',
        hotspot:  '#7d3f98',
    };

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

    function getHeatPoints(type) {
        return pins
            .filter(p => type === 'all' || p.type === type)
            .map(p => [p.lat, p.lng, 1]);
    }

    function getFilteredPins(type) {
        return type === 'all' ? pins : pins.filter(p => p.type === type);
    }

    function buildMarkers(type) {
        markerLayer.clearLayers();

        getFilteredPins(type).forEach(pin => {
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
                    ${pin.desc ? `<br><span style="font-size:12px;">${pin.desc}</span>` : ''}
                    <br><small style="color:#aaa;">By ${pin.user}</small>
                `);
        });
    }

    function buildHeat(type) {
        if (heatLayer) { map.removeLayer(heatLayer); heatLayer = null; }

        const pts = getHeatPoints(type);
        if (!pts.length) return;

        heatLayer = L.heatLayer(pts, {
            radius:  28,
            blur:    22,
            maxZoom: 17,
            gradient: { 0.2: '#00c9a2', 0.5: '#ffc200', 0.8: '#ff6600', 1.0: '#d94848' },
        }).addTo(map);
    }

    function fitToPins(type) {
        const pts = getFilteredPins(type);
        if (!pts.length) return;

        const latlngs = pts.map(p => [p.lat, p.lng]);
        map.fitBounds(L.latLngBounds(latlngs), { padding: [30, 30] });
    }

    function render(type) {
        buildMarkers(type);
        buildHeat(type);
        fitToPins(type);
    }

    // ── Filter buttons ────────────────────────────
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            render(btn.dataset.type);
        });
    });

    // ── Initial render ────────────────────────────
    render('all');

    setTimeout(() => map.invalidateSize(), 100);
})();
</script>
@endpush
