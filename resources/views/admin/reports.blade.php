@extends('admin.layout')

@section('title', 'Incident Reports')
@section('heading', 'Incident Reports')
@section('subheading', 'Review user-submitted pin requests and approve or reject them.')

@section('content')
<style>
    .report-tabs { display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap; }
    .report-tab {
        padding:8px 18px; border-radius:999px; border:1px solid #ccc;
        background:#fff; cursor:pointer; font-size:13px; font-weight:600;
        text-transform:uppercase; letter-spacing:.04em; color:#555;
        transition:all .2s;
    }
    .report-tab.active { background:#0b6d5a; color:#fff; border-color:#0b6d5a; }
    .report-tab .count {
        display:inline-block; margin-left:6px; padding:1px 7px;
        border-radius:999px; font-size:11px; font-weight:700;
    }
    .report-tab.active .count { background:rgba(255,255,255,.25); }
    .report-tab:not(.active) .count { background:#eee; color:#333; }

    .tab-panel { display:none; }
    .tab-panel.active { display:block; }

    .badge-pending  { background:#ffc107; color:#000; padding:2px 10px; border-radius:999px; font-size:12px; font-weight:700; }
    .badge-verified { background:#198754; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px; font-weight:700; }
    .badge-resolved { background:#6c757d; color:#fff; padding:2px 10px; border-radius:999px; font-size:12px; font-weight:700; }

    .pending-highlight { background:#fffbea; }

    .action-row { display:flex; gap:6px; flex-wrap:wrap; }
    .btn-approve { background:#198754; color:#fff; border:none; padding:5px 12px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; }
    .btn-approve:hover { background:#145c38; }
    .btn-reject  { background:#dc3545; color:#fff; border:none; padding:5px 12px; border-radius:6px; cursor:pointer; font-size:12px; font-weight:600; }
    .btn-reject:hover  { background:#a31025; }

    .empty-state { text-align:center; padding:40px; color:#888; font-size:14px; }
    .empty-state span { font-size:36px; display:block; margin-bottom:10px; }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Tab nav --}}
<div class="report-tabs">
    <button class="report-tab active" data-tab="pending" id="tab-pending">
        ⏳ Pending
        <span class="count">{{ $pending->count() }}</span>
    </button>
    <button class="report-tab" data-tab="verified" id="tab-verified">
        ✅ Verified / Live
        <span class="count">{{ $verified->count() }}</span>
    </button>
    <button class="report-tab" data-tab="resolved" id="tab-resolved">
        🗄️ Rejected / Resolved
        <span class="count">{{ $resolved->count() }}</span>
    </button>
</div>

{{-- PENDING --}}
<div class="tab-panel active" id="panel-pending">
    <div class="card">
        <div class="card-header"><h5 class="mb-0">⏳ Pending Approval — these are NOT visible on the map yet</h5></div>
        <div class="card-body p-0">
            @if($pending->isEmpty())
                <div class="empty-state"><span>🎉</span>No pending reports. You're all caught up!</div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-warning">
                        <tr>
                            <th>#</th>
                            <th>Name / Description</th>
                            <th>Type</th>
                            <th>Coordinates</th>
                            <th>Submitted By</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $pin)
                        <tr class="pending-highlight">
                            <td>{{ $pin->id }}</td>
                            <td>
                                <strong>{{ $pin->name }}</strong>
                                @if($pin->description)
                                    <br><small class="text-muted">{{ Str::limit($pin->description, 60) }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($pin->type) }}</span></td>
                            <td><small>{{ number_format($pin->latitude,5) }},<br>{{ number_format($pin->longitude,5) }}</small></td>
                            <td>{{ $pin->user->name ?? 'Anonymous' }}</td>
                            <td><small>{{ $pin->created_at->diffForHumans() }}</small></td>
                            <td>
                                <div class="action-row">
                                    <form action="{{ route('admin.pins.verify', $pin) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button class="btn-approve">✔ Approve</button>
                                    </form>
                                    <form action="{{ route('admin.pins.reject', $pin) }}" method="POST" onsubmit="return confirm('Reject this report?')">
                                        @csrf @method('PATCH')
                                        <button class="btn-reject">✘ Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- VERIFIED --}}
<div class="tab-panel" id="panel-verified">
    <div class="card">
        <div class="card-header"><h5 class="mb-0">✅ Verified — live on the public map</h5></div>
        <div class="card-body p-0">
            @if($verified->isEmpty())
                <div class="empty-state"><span>🗺️</span>No verified pins yet.</div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>#</th><th>Name</th><th>Type</th><th>Coordinates</th><th>Submitted By</th><th>Approved</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($verified as $pin)
                        <tr>
                            <td>{{ $pin->id }}</td>
                            <td><strong>{{ $pin->name }}</strong></td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($pin->type) }}</span></td>
                            <td><small>{{ number_format($pin->latitude,5) }}, {{ number_format($pin->longitude,5) }}</small></td>
                            <td>{{ $pin->user->name ?? 'Anonymous' }}</td>
                            <td><small>{{ $pin->updated_at->diffForHumans() }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- RESOLVED --}}
<div class="tab-panel" id="panel-resolved">
    <div class="card">
        <div class="card-header"><h5 class="mb-0">🗄️ Rejected / Resolved — hidden from the map</h5></div>
        <div class="card-body p-0">
            @if($resolved->isEmpty())
                <div class="empty-state"><span>📭</span>No rejected or resolved reports.</div>
            @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th><th>Name</th><th>Type</th><th>Coordinates</th><th>Submitted By</th><th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resolved as $pin)
                        <tr>
                            <td>{{ $pin->id }}</td>
                            <td><strong>{{ $pin->name }}</strong></td>
                            <td><span class="badge bg-info text-dark">{{ ucfirst($pin->type) }}</span></td>
                            <td><small>{{ number_format($pin->latitude,5) }}, {{ number_format($pin->longitude,5) }}</small></td>
                            <td>{{ $pin->user->name ?? 'Anonymous' }}</td>
                            <td><small>{{ $pin->updated_at->diffForHumans() }}</small></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.report-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.report-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
        });
    });
</script>
@endsection
