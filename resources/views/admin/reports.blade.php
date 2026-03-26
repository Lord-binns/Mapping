@extends('admin.layout')

@section('title', 'Incident Reports')
@section('heading', 'Incident Reports')
@section('subheading', 'Review user-submitted pin requests and approve or reject them.')

@push('head')
<style>
    /* ── Tab bar ─────────────────────────────────── */
    .report-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }

    .report-tab {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        border-radius: 999px;
        border: 1px solid #c8efe4;
        background: #f8fffc;
        color: #0b6d5a;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        cursor: pointer;
        transition: all .18s;
    }

    .report-tab:hover {
        background: #ecfffb;
        border-color: #9bdcca;
    }

    .report-tab.active {
        background: #0b6d5a;
        color: #fff;
        border-color: #0b6d5a;
    }

    .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        padding: 1px 6px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        background: rgba(255,255,255,.22);
    }

    .report-tab:not(.active) .tab-count {
        background: #d4f0e8;
        color: #0b6d5a;
    }

    /* ── Tab panels ──────────────────────────────── */
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    /* ── Status badges ───────────────────────────── */
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .badge-type {
        background: #e7fbf5;
        color: #0b6d5a;
        border: 1px solid #c0eadf;
    }

    /* ── Table overrides ─────────────────────────── */
    .page-table td { vertical-align: middle; }

    .row-pending { background: #fffdf0; }

    /* ── Action buttons ──────────────────────────── */
    .action-wrap { display: flex; gap: 6px; flex-wrap: wrap; }

    .btn-approve {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 6px 12px; border-radius: 8px; border: none;
        background: #0b6d5a; color: #fff;
        font-size: 12px; font-weight: 700;
        cursor: pointer; transition: background .18s;
    }

    .btn-approve:hover { background: #085248; }

    .btn-reject {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 6px 12px; border-radius: 8px; border: none;
        background: #fff; color: #c0392b;
        border: 1px solid #f5b7b1;
        font-size: 12px; font-weight: 700;
        cursor: pointer; transition: all .18s;
    }

    .btn-reject:hover { background: #fdf0ee; border-color: #c0392b; }

    /* ── Empty state ─────────────────────────────── */
    .empty-state {
        text-align: center;
        padding: 48px 20px;
        color: #7aada0;
        font-size: 14px;
    }

    .empty-icon {
        font-size: 40px;
        display: block;
        margin-bottom: 12px;
        line-height: 1;
    }

    /* ── Success alert ───────────────────────────── */
    .alert-success-bar {
        background: #ecfffb;
        border: 1px solid #9bdcca;
        border-radius: 10px;
        color: #0b6d5a;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .alert-close {
        background: none; border: none; color: #0b6d5a;
        font-size: 16px; cursor: pointer; line-height: 1;
    }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="alert-success-bar" id="success-alert">
        ✅ {{ session('success') }}
        <button class="alert-close" onclick="document.getElementById('success-alert').remove()">✕</button>
    </div>
@endif

{{-- ── Tab navigation ── --}}
<div class="report-tabs">
    <button class="report-tab active" data-tab="pending">
        ⏳ Pending
        <span class="tab-count">{{ $pending->count() }}</span>
    </button>
    <button class="report-tab" data-tab="verified">
        ✅ Verified
        <span class="tab-count">{{ $verified->count() }}</span>
    </button>
    <button class="report-tab" data-tab="resolved">
        🗄 Rejected
        <span class="tab-count">{{ $resolved->count() }}</span>
    </button>
</div>

{{-- ── PENDING tab ── --}}
<div class="tab-panel active" id="panel-pending">
    <div class="panel" style="padding:0; overflow:hidden;">
        <div style="padding:12px 14px; border-bottom: 1px solid #e8f5f2;">
            <h3 style="margin:0; font-size:13px;">⏳ Awaiting Approval — not visible on the map yet</h3>
        </div>

        @if($pending->isEmpty())
            <div class="empty-state">
                <span class="empty-icon">🎉</span>
                No pending reports. You're all caught up!
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="page-table" style="border:none; border-radius:0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name / Note</th>
                            <th>Type</th>
                            <th>Coordinates</th>
                            <th>Submitted By</th>
                            <th>When</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pending as $pin)
                        <tr class="row-pending">
                            <td style="color:#aaa; font-size:12px;">{{ $pin->id }}</td>
                            <td>
                                <strong style="color:#1b1b1b;">{{ $pin->name }}</strong>
                                @if($pin->description)
                                    <br><span style="color:#7aada0; font-size:12px;">{{ Str::limit($pin->description, 55) }}</span>
                                @endif
                                @if($pin->image)
                                    <div style="margin-top: 6px;">
                                        <a href="{{ $pin->image }}" target="_blank" style="display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; color:#0b6d5a; background:#e7fbf5; padding:3px 8px; border-radius:6px; text-decoration:none;">
                                            📷 View Photo
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td><span class="badge badge-type">{{ ucfirst($pin->type) }}</span></td>
                            <td style="font-size:12px; color:#55706a; white-space:nowrap;">
                                {{ number_format($pin->latitude, 5) }},<br>
                                {{ number_format($pin->longitude, 5) }}
                            </td>
                            <td style="font-size:13px;">{{ $pin->user->name ?? 'Anonymous' }}</td>
                            <td style="font-size:12px; color:#7aada0; white-space:nowrap;">{{ $pin->created_at->diffForHumans() }}</td>
                            <td>
                                <div class="action-wrap">
                                    <form action="{{ route('admin.pins.verify', $pin) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-approve">✔ Approve</button>
                                    </form>
                                    <form action="{{ route('admin.pins.reject', $pin) }}" method="POST" onsubmit="return confirm('Reject this report?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-reject">✘ Reject</button>
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

{{-- ── VERIFIED tab ── --}}
<div class="tab-panel" id="panel-verified">
    <div class="panel" style="padding:0; overflow:hidden;">
        <div style="padding:12px 14px; border-bottom: 1px solid #e8f5f2;">
            <h3 style="margin:0; font-size:13px;">✅ Verified — currently live on the public map</h3>
        </div>

        @if($verified->isEmpty())
            <div class="empty-state">
                <span class="empty-icon">🗺️</span>
                No verified pins yet.
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="page-table" style="border:none; border-radius:0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Coordinates</th>
                            <th>Submitted By</th>
                            <th>Approved</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($verified as $pin)
                        <tr>
                            <td style="color:#aaa; font-size:12px;">{{ $pin->id }}</td>
                            <td><strong style="color:#1b1b1b;">{{ $pin->name }}</strong></td>
                            <td><span class="badge badge-type">{{ ucfirst($pin->type) }}</span></td>
                            <td style="font-size:12px; color:#55706a; white-space:nowrap;">
                                {{ number_format($pin->latitude, 5) }}, {{ number_format($pin->longitude, 5) }}
                            </td>
                            <td style="font-size:13px;">{{ $pin->user->name ?? 'Anonymous' }}</td>
                            <td style="font-size:12px; color:#7aada0;">{{ $pin->updated_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ── RESOLVED tab ── --}}
<div class="tab-panel" id="panel-resolved">
    <div class="panel" style="padding:0; overflow:hidden;">
        <div style="padding:12px 14px; border-bottom: 1px solid #e8f5f2;">
            <h3 style="margin:0; font-size:13px;">🗄 Rejected / Resolved — hidden from the map</h3>
        </div>

        @if($resolved->isEmpty())
            <div class="empty-state">
                <span class="empty-icon">📭</span>
                No rejected or resolved reports.
            </div>
        @else
            <div style="overflow-x:auto;">
                <table class="page-table" style="border:none; border-radius:0;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Coordinates</th>
                            <th>Submitted By</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resolved as $pin)
                        <tr style="opacity:.75;">
                            <td style="color:#aaa; font-size:12px;">{{ $pin->id }}</td>
                            <td><strong style="color:#1b1b1b;">{{ $pin->name }}</strong></td>
                            <td><span class="badge badge-type">{{ ucfirst($pin->type) }}</span></td>
                            <td style="font-size:12px; color:#55706a; white-space:nowrap;">
                                {{ number_format($pin->latitude, 5) }}, {{ number_format($pin->longitude, 5) }}
                            </td>
                            <td style="font-size:13px;">{{ $pin->user->name ?? 'Anonymous' }}</td>
                            <td style="font-size:12px; color:#7aada0;">{{ $pin->updated_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
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
