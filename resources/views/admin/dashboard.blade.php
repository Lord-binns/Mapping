@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('heading', 'Admin Dashboard')
@section('subheading', 'Monitor reports, users, and operations in one responsive view.')

@section('toolbar')
<a class="btn alt" href="{{ route('admin.reports') }}">
	<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16" stroke-linecap="round"/><path d="M4 12h16" stroke-linecap="round"/><path d="M4 19h10" stroke-linecap="round"/></svg>
	Reports
</a>
<a class="btn" href="{{ route('admin.heatmap') }}">
	<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v12" stroke-linecap="round"/><path d="M7 11l5 5 5-5" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 20h14" stroke-linecap="round"/></svg>
	Heatmap
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
<script>
	(function () {
		const queueEl = document.getElementById('hotspot-queue');

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
	})();
</script>
@endpush
