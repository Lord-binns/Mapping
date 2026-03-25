@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('heading', 'Admin Dashboard')
@section('subheading', 'Monitor reports, hotspots, users, and operations in one responsive view.')

@push('head')
<link
	rel="stylesheet"
	href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
	integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
	crossorigin=""
>
<style>
	.hotspot-map {
		width: 100%;
		min-height: 300px;
		border-radius: 12px;
		border: 1px solid #d9ebe6;
		overflow: hidden;
		margin-top: 10px;
	}

	.hotspot-status {
		margin-top: 10px;
		font-size: 12px;
		color: #55706a;
	}
</style>
@endpush

@section('toolbar')
<a class="btn alt" href="{{ route('admin.reports') }}">
	<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16" stroke-linecap="round"/><path d="M4 12h16" stroke-linecap="round"/><path d="M4 19h10" stroke-linecap="round"/></svg>
	Reports
</a>
<a class="btn" href="{{ route('admin.hotspots') }}">
	<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v12" stroke-linecap="round"/><path d="M7 11l5 5 5-5" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 20h14" stroke-linecap="round"/></svg>
	Export
</a>
@endsection

@section('content')
<section class="panel-grid" aria-label="Admin key metrics">
	<article class="panel">
		<h3>Total Reports</h3>
		<p>All submissions in the system.</p>
		<div class="kpi-value">164</div>
		<span class="kpi-trend">+12 this week</span>
	</article>
	<article class="panel">
		<h3>Open Incidents</h3>
		<p>Reports requiring immediate validation.</p>
		<div class="kpi-value">27</div>
		<span class="kpi-trend">9 high-priority</span>
	</article>
	<article class="panel">
		<h3>Active Users</h3>
		<p>Contributors active in the last 7 days.</p>
		<div class="kpi-value">58</div>
		<span class="kpi-trend">+7 this week</span>
	</article>
</section>

<section class="split-grid" aria-label="Operations overview">
	<article class="panel">
		<h3>Hotspot Overview</h3>
		<p>Current concentration of environmental reports by area.</p>
		<div id="admin-hotspot-map" class="hotspot-map" aria-label="Hotspot overview map"></div>
		<p id="hotspot-status" class="hotspot-status">Loading hotspot overview...</p>
	</article>

	<article class="panel">
		<h3>Priority Queue</h3>
		<p>Latest items that need admin review.</p>
		<div id="hotspot-queue" class="list-stack">
			<div class="list-item">
				<strong>Preparing queue...</strong>
				<span>Hotspots will be listed once map data is loaded.</span>
			</div>
		</div>
	</article>
</section>

<section class="panel" aria-label="Recent activity">
	<h3>Recent Activity</h3>
	<p>Latest validation and moderation actions from admins.</p>
	<table class="page-table">
		<thead>
			<tr>
				<th>Time</th>
				<th>Action</th>
				<th>Location</th>
				<th>Owner</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>09:42</td>
				<td>Report validated</td>
				<td>Sankanan</td>
				<td>Admin A</td>
				<td>Verified</td>
			</tr>
			<tr>
				<td>08:10</td>
				<td>Hotspot updated</td>
				<td>Maluko</td>
				<td>Admin B</td>
				<td>Updated</td>
			</tr>
			<tr>
				<td>Yesterday</td>
				<td>User role changed</td>
				<td>System</td>
				<td>Super Admin</td>
				<td>Completed</td>
			</tr>
		</tbody>
	</table>
</section>
@endsection

@push('scripts')
<script
	src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
	integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
	crossorigin=""
></script>
<script>
	(function () {
		const mapEl = document.getElementById('admin-hotspot-map');
		const statusEl = document.getElementById('hotspot-status');
		const queueEl = document.getElementById('hotspot-queue');

		if (!mapEl || typeof L === 'undefined') {
			if (statusEl) {
				statusEl.textContent = 'Hotspot map could not be initialized.';
			}
			return;
		}

		const savedTagsKey = 'dashboard-location-tags-v1';
		const fallbackHotspots = [
			{ lat: 8.372673, lng: 124.849266, tagType: 'Dumping Site', note: 'Dicklum', timestamp: Date.now() - 3600000 },
			{ lat: 8.352661, lng: 124.813459, tagType: 'Contaminated Water', note: 'Damilag', timestamp: Date.now() - 7200000 },
			{ lat: 8.316145, lng: 124.858090, tagType: 'Blocked Drainage', note: 'Sankanan', timestamp: Date.now() - 10800000 },
			{ lat: 8.374209, lng: 124.955686, tagType: 'Illegal Burning', note: 'Maluko', timestamp: Date.now() - 14400000 },
		];

		function getCategoryColor(tagType) {
			const map = {
				'High Risk': '#d94848',
				'Dumping Site': '#a35f00',
				'Contaminated Water': '#1a6ca8',
				'Illegal Burning': '#7d3f98',
				'Blocked Drainage': '#355e3b',
				'Pin Location': '#0b6d5a',
			};
			return map[tagType] || '#5f5f5f';
		}

		function loadHotspotData() {
			try {
				const parsed = JSON.parse(localStorage.getItem(savedTagsKey) || '[]');
				const valid = Array.isArray(parsed)
					? parsed.filter((entry) => Number.isFinite(entry.lat) && Number.isFinite(entry.lng))
					: [];
				return valid.length ? valid : fallbackHotspots;
			} catch {
				return fallbackHotspots;
			}
		}

		function buildQueue(items) {
			if (!queueEl) {
				return;
			}

			const sorted = [...items]
				.sort((a, b) => Number(b.timestamp || 0) - Number(a.timestamp || 0))
				.slice(0, 5);

			if (!sorted.length) {
				queueEl.innerHTML = '<div class="list-item"><strong>No hotspots yet</strong><span>Tagged locations will appear here.</span></div>';
				return;
			}

			queueEl.innerHTML = sorted.map((item) => {
				const when = new Date(Number(item.timestamp || Date.now())).toLocaleString();
				const note = item.note ? String(item.note) : 'No note';
				const type = String(item.tagType || 'Other');
				return `
					<div class="list-item">
						<strong>${type} - ${note}</strong>
						<span>${when}</span>
					</div>
				`;
			}).join('');
		}

		const map = L.map(mapEl, { zoomControl: true }).setView([8.3698, 124.8645], 12);
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; OpenStreetMap contributors',
		}).addTo(map);

		const hotspots = loadHotspotData();
		const bounds = [];

		hotspots.forEach((item) => {
			const lat = Number(item.lat);
			const lng = Number(item.lng);
			const type = String(item.tagType || 'Other');
			const note = item.note ? String(item.note) : 'No note';
			const color = getCategoryColor(type);

			const pulse = L.circle([lat, lng], {
				radius: 250,
				color,
				fillColor: color,
				fillOpacity: 0.22,
				weight: 2,
			}).addTo(map);

			const marker = L.circleMarker([lat, lng], {
				radius: 6,
				color: '#ffffff',
				fillColor: color,
				fillOpacity: 1,
				weight: 2,
			}).addTo(map);

			const popupHtml = `<strong>${type}</strong><br>${note}<br><small>${lat.toFixed(6)}, ${lng.toFixed(6)}</small>`;
			pulse.bindPopup(popupHtml);
			marker.bindPopup(popupHtml);

			bounds.push([lat, lng]);
		});

		if (bounds.length) {
			map.fitBounds(bounds, { padding: [24, 24] });
		}

		buildQueue(hotspots);

		if (statusEl) {
			const source = hotspots === fallbackHotspots ? 'demo fallback data' : 'saved user tags';
			statusEl.textContent = `Showing ${hotspots.length} hotspot point(s) from ${source}.`;
		}

		setTimeout(() => map.invalidateSize(), 0);
	})();
</script>
@endpush
