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
		<div class="kpi-value">{{ \App\Models\Pin::count() }}</div>
		<span class="kpi-trend">{{ \App\Models\Pin::where('status','pending')->count() }} pending</span>
	</article>
	<article class="panel">
		<h3>Open Incidents</h3>
		<p>Reports requiring immediate validation.</p>
		<div class="kpi-value" style="color:#e67e00">{{ \App\Models\Pin::where('status','pending')->count() }}</div>
		<span class="kpi-trend"><a href="{{ route('admin.reports') }}">Review now →</a></span>
	</article>
	<article class="panel">
		<h3>Active Users</h3>
		<p>Contributors active in the last 7 days.</p>
		<div class="kpi-value">{{ \App\Models\User::count() }}</div>
		<span class="kpi-trend">{{ \App\Models\Pin::where('status','verified')->count() }} verified pins</span>
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
		<p>Latest pending pin requests awaiting your review. <a href="{{ route('admin.reports') }}">View all →</a></p>
		<div id="hotspot-queue" class="list-stack"></div>
	</article>
</section>

<section class="panel" aria-label="Recent activity">
	<h3>Recent Activity</h3>
	<p>Latest validation and moderation actions from admins.</p>
	
	@php
		$recentActivity = \App\Models\Pin::where('status', '!=', 'pending')
			->orderBy('updated_at', 'desc')
			->take(5)
			->get();
	@endphp

	@if($recentActivity->isEmpty())
		<div style="text-align:center; padding: 24px; color:#7aada0; font-size:14px;">
			No recent moderation actions recorded.
		</div>
	@else
		<table class="page-table">
			<thead>
				<tr>
					<th>Time</th>
					<th>Action</th>
					<th>Pin Name / Type</th>
					<th>Submitted By</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				@foreach($recentActivity as $pin)
				<tr>
					<td style="color:#7aada0; font-size:12px; white-space:nowrap;">{{ $pin->updated_at->diffForHumans() }}</td>
					<td>
						@if($pin->status === 'verified')
							✔ Report Approved
						@else
							✘ Report Rejected
						@endif
					</td>
					<td>
						<strong style="color:#1b1b1b;">{{ $pin->name }}</strong><br>
						<span style="font-size:12px; color:#55706a; text-transform:uppercase;">{{ $pin->type }}</span>
					</td>
					<td style="font-size:13px;">{{ $pin->user->name ?? 'Anonymous' }}</td>
					<td>
						@if($pin->status === 'verified')
							<span style="display:inline-block; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:700; background:#e7fbf5; color:#0b6d5a;">VERIFIED</span>
						@else
							<span style="display:inline-block; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:700; background:#fdf0ee; color:#c0392b;">REJECTED</span>
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	@endif
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

		const map = L.map(mapEl, { zoomControl: true }).setView([8.3698, 124.8645], 12);
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; OpenStreetMap contributors',
		}).addTo(map);

		const bounds = [];

		// Load only real verified pins from the server map endpoint
		fetch('/api/pins')
			.then(res => res.json())
			.then(pins => {
				pins.forEach(pin => {
					const dbTypeColorMap = {
						'incident': '#d94848',
						'dumping':  '#a35f00',
						'water':    '#1a6ca8',
						'flood':    '#355e3b',
						'hotspot':  '#7d3f98',
					};
					const color = dbTypeColorMap[pin.type] || '#0b6d5a';
					const marker = L.circleMarker([pin.latitude, pin.longitude], {
						radius: 7, color: '#fff', fillColor: color, fillOpacity: 1, weight: 2
					}).addTo(map);
					
					marker.bindPopup(`<b>${pin.name}</b><br>${pin.description || ''}<br><small>By: ${pin.user?.name || 'Anonymous'}</small>`);
					bounds.push([pin.latitude, pin.longitude]);
				});

				if (bounds.length) {
					map.fitBounds(bounds, { padding: [24, 24] });
				}

				if (statusEl) {
					statusEl.textContent = `Showing ${pins.length} active verified point(s).`;
				}
			})
			.catch(() => {
				if (statusEl) {
					statusEl.textContent = 'Failed to load hotspot data from server.';
				}
			});

		// Load pending from server and put them in the priority queue
		fetch('/api/pins/pending')
			.then(res => res.json())
			.then(pending => {
				if (!queueEl) return;
				if (!pending.length) {
					queueEl.innerHTML = '<div class="list-item"><strong>No pending reports</strong><span>All caught up!</span></div>';
					return;
				}
				queueEl.innerHTML = pending.slice(0, 5).map(pin => `
					<div class="list-item" style="border-left:3px solid #ffc107;padding-left:8px;">
						<strong>⏳ ${pin.name}</strong>
						<span>${pin.type} &mdash; by ${pin.user?.name || 'Anonymous'} &mdash; <a href="/admin/reports">Review</a></span>
					</div>
				`).join('');
			})
			.catch(() => {});

		setTimeout(() => map.invalidateSize(), 0);
	})();
</script>
@endpush
