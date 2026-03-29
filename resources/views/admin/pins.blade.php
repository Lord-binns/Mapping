@extends('admin.layout')

@section('title', 'Pins')
@section('heading', 'Pins & Hotspots')
@section('subheading', 'Review pending tags and manage all map pins.')

@section('content')
<style>
    .status-badge-pending  { background: #ffc107; color: #000; }
    .status-badge-verified { background: #198754; color: #fff; }
    .status-badge-rejected { background: #6c757d; color: #fff; }
    .pending-row { background: #fff8e1; }
    .btn-approve { background: #198754; color: #fff; border: none; }
    .btn-approve:hover { background: #145c38; color: #fff; }
    .btn-reject  { background: #dc3545; color: #fff; border: none; }
    .btn-reject:hover  { background: #a31025; color: #fff; }
</style>

<div class="row mb-3">
    <div class="col-12">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card text-center border-warning">
                    <div class="card-body py-3">
                        <h4 class="mb-0 text-warning">{{ $pins->where('status','pending')->count() }}</h4>
                        <small class="text-muted">Pending Approval</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-success">
                    <div class="card-body py-3">
                        <h4 class="mb-0 text-success">{{ $pins->where('status','verified')->count() }}</h4>
                        <small class="text-muted">Verified / Live</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-secondary">
                    <div class="card-body py-3">
                        <h4 class="mb-0 text-secondary">{{ $pins->where('status','rejected')->count() }}</h4>
                        <small class="text-muted">Rejected</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Submitted Pins</h5>
                <a href="{{ route('admin.pins.create') }}" class="btn btn-primary btn-sm">+ Add New Pin</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Submitted By</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pins as $pin)
                                <tr class="{{ $pin->status === 'pending' ? 'pending-row' : '' }}">
                                    <td>{{ $pin->id }}</td>
                                    <td>
                                        <strong>{{ $pin->name }}</strong>
                                        @if($pin->description)
                                            <br><small class="text-muted">{{ Str::limit($pin->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info text-dark">{{ ucfirst($pin->type) }}</span></td>
                                    <td>
                                        <span class="badge status-badge-{{ $pin->status }}">
                                            {{ ucfirst($pin->status) }}
                                        </span>
                                    </td>
                                    <td><small>{{ number_format($pin->latitude,4) }}, {{ number_format($pin->longitude,4) }}</small></td>
                                    <td>{{ $pin->user->name ?? 'Anonymous' }}</td>
                                    <td><small>{{ $pin->created_at->diffForHumans() }}</small></td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            {{-- Approve button — only for pending --}}
                                            @if($pin->status === 'pending')
                                                <form action="{{ route('admin.pins.verify', $pin) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-approve" title="Approve — will show on map">✔ Approve</button>
                                                </form>
                                                <form action="{{ route('admin.pins.reject', $pin) }}" method="POST" class="js-reject-form" data-reject-name="{{ $pin->name }}" onsubmit="return submitRejectWithOptionalComment(event)">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="rejection_comment" value="">
                                                    <button class="btn btn-sm btn-reject" title="Reject — will not show on map">✘ Reject</button>
                                                </form>
                                            @endif

                                            <a href="{{ route('admin.pins.edit', $pin) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                            <form action="{{ route('admin.pins.destroy', $pin) }}" method="POST" onsubmit="return confirm('Delete this pin permanently?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No pins submitted yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($pins->hasPages())
                <div class="card-footer">
                    {{ $pins->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function submitRejectWithOptionalComment(event) {
        const form = event.currentTarget;
        const reportName = form.dataset.rejectName || 'this pin';
        const confirmed = window.confirm(`Reject ${reportName}?`);
        if (!confirmed) {
            return false;
        }

        const comment = window.prompt('Optional rejection comment (leave blank to skip):', '');
        const hiddenComment = form.querySelector('input[name="rejection_comment"]');
        if (hiddenComment) {
            hiddenComment.value = comment ? comment.trim() : '';
        }

        return true;
    }
</script>
@endsection
