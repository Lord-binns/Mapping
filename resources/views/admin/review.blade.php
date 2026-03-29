@extends('admin.layout')

@section('title', 'Review Report')
@section('heading', 'Report Map Review')
@section('subheading', 'Inspect the exact report location before approving or rejecting.')

@push('head')
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
>
<style>
    .review-grid {
        display: grid;
        gap: 14px;
        grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr);
    }

    .review-map {
        width: 100%;
        min-height: 520px;
        border-radius: 12px;
        border: 1px solid #d9ebe6;
        overflow: hidden;
    }

    .meta-grid {
        display: grid;
        gap: 10px;
    }

    .meta-item {
        border: 1px solid #d9ebe6;
        border-radius: 10px;
        padding: 10px 12px;
        background: #fbfffd;
    }

    .meta-label {
        margin: 0 0 4px;
        font-size: 11px;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #7aada0;
        font-weight: 700;
    }

    .meta-value {
        margin: 0;
        color: #1b1b1b;
        font-size: 14px;
        line-height: 1.45;
    }

    .review-photo {
        display: block;
        width: 100%;
        max-height: 220px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #d9ebe6;
    }

    .review-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-approve {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        background: #0b6d5a;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-approve:hover {
        background: #085248;
    }

    .btn-reject {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #f5b7b1;
        background: #fff;
        color: #c0392b;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn-reject:hover {
        background: #fdf0ee;
        border-color: #c0392b;
    }

    @media (max-width: 1000px) {
        .review-grid {
            grid-template-columns: 1fr;
        }

        .review-map {
            min-height: 420px;
        }
    }
</style>
@endpush

@section('toolbar')
<a class="btn alt" href="{{ route('admin.reports') }}">Back to Reports</a>
@endsection

@section('content')
<section class="review-grid" aria-label="Report map review">
    <article class="panel">
        <h3 style="margin-bottom:10px;">Map Location</h3>
        <div id="review-map" class="review-map" aria-label="Map showing report location"></div>
    </article>

    <article class="panel">
        <h3 style="margin-bottom:10px;">Report Details</h3>

        <div class="meta-grid">
            <div class="meta-item">
                <p class="meta-label">Title</p>
                <p class="meta-value">{{ $pin->name }}</p>
            </div>

            <div class="meta-item">
                <p class="meta-label">Type</p>
                <p class="meta-value">{{ strtoupper($pin->type) }}</p>
            </div>

            <div class="meta-item">
                <p class="meta-label">Submitted by</p>
                <p class="meta-value">{{ $pin->user->name ?? 'Anonymous' }}</p>
            </div>

            <div class="meta-item">
                <p class="meta-label">Status</p>
                <p class="meta-value">{{ strtoupper($pin->status) }}</p>
            </div>

            @if($pin->status === 'rejected')
            <div class="meta-item">
                <p class="meta-label">Rejection Comment</p>
                <p class="meta-value">{{ $pin->rejection_comment ?: 'No comment' }}</p>
            </div>
            @endif

            <div class="meta-item">
                <p class="meta-label">Coordinates</p>
                <p class="meta-value">{{ number_format($pin->latitude, 6) }}, {{ number_format($pin->longitude, 6) }}</p>
            </div>

            @if($pin->barangay)
            <div class="meta-item">
                <p class="meta-label">Barangay</p>
                <p class="meta-value">{{ $pin->barangay }}</p>
            </div>
            @endif

            @if($pin->description)
            <div class="meta-item">
                <p class="meta-label">Description</p>
                <p class="meta-value">{{ $pin->description }}</p>
            </div>
            @endif

            @if($pin->image)
            <div class="meta-item">
                <p class="meta-label">Attached Photo</p>
                <a href="{{ $pin->image }}" target="_blank" rel="noopener noreferrer">
                    <img class="review-photo" src="{{ $pin->image }}" alt="Photo attached to report {{ $pin->id }}">
                </a>
            </div>
            @endif

            @if($pin->status === 'pending')
            <div class="meta-item">
                <p class="meta-label">Admin Actions</p>
                <div class="review-actions">
                    <form action="{{ route('admin.pins.verify', $pin) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-approve">✔ Approve</button>
                    </form>
                    <form action="{{ route('admin.pins.reject', $pin) }}" method="POST" class="js-reject-form" data-reject-name="{{ $pin->name }}" onsubmit="return confirmRejectWithOptionalComment(event)">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="rejection_comment" value="">
                        <button type="submit" class="btn-reject">✘ Reject</button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </article>
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
        const mapEl = document.getElementById('review-map');
        if (!mapEl || typeof L === 'undefined') {
            return;
        }

        const lat = {{ (float) $pin->latitude }};
        const lng = {{ (float) $pin->longitude }};

        const map = L.map(mapEl, { zoomControl: true }).setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        L.circleMarker([lat, lng], {
            radius: 9,
            color: '#ffffff',
            fillColor: '#0b6d5a',
            fillOpacity: 1,
            weight: 2,
        })
            .addTo(map)
            .bindPopup(`<strong>{{ addslashes($pin->name) }}</strong><br>{{ addslashes($pin->type) }}<br>${lat.toFixed(6)}, ${lng.toFixed(6)}`)
            .openPopup();

        setTimeout(() => map.invalidateSize(), 0);
    })();

    function confirmRejectWithOptionalComment(event) {
        const form = event.currentTarget;
        const reportName = form.dataset.rejectName || 'this report';
        const confirmed = window.confirm(`Reject ${reportName}?`);
        if (!confirmed) {
            return false;
        }

        const comment = window.prompt('Optional rejection comment for this report (leave blank to skip):', '');
        const hiddenComment = form.querySelector('input[name="rejection_comment"]');
        if (hiddenComment) {
            hiddenComment.value = comment ? comment.trim() : '';
        }

        return true;
    }
</script>
@endpush
