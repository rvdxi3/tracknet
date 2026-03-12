@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%); margin-top: -1rem;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;">
                        <i class="fas fa-shield-alt me-1"></i> Security
                    </span>
                </div>
                <h2 class="fw-bold text-white mb-1" style="font-size: 2rem;">Activity Log</h2>
                <p class="text-white-50 mb-0" style="font-size: 0.9rem;">Audit trail of user actions and security events</p>
            </div>
        </div>
    </div>

    @include('partials.alerts')

    {{-- Filters --}}
    <div class="card border-0 mb-4" style="border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.activity-log') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing:.05em;">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted text-uppercase" style="letter-spacing:.05em;">Action</label>
                    <select name="action" class="form-select">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>
                                {{ ucwords(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.activity-log') }}" class="btn btn-outline-secondary ms-1">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Log Table --}}
    <div class="card border-0" style="border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th class="px-4 py-3 text-muted fw-semibold" style="font-size:.75rem; text-transform:uppercase; letter-spacing:.06em; border-bottom: 2px solid #e9ecef;">Timestamp</th>
                            <th class="py-3 text-muted fw-semibold" style="font-size:.75rem; text-transform:uppercase; letter-spacing:.06em; border-bottom: 2px solid #e9ecef;">Event</th>
                            <th class="py-3 text-muted fw-semibold" style="font-size:.75rem; text-transform:uppercase; letter-spacing:.06em; border-bottom: 2px solid #e9ecef;">User</th>
                            <th class="py-3 text-muted fw-semibold" style="font-size:.75rem; text-transform:uppercase; letter-spacing:.06em; border-bottom: 2px solid #e9ecef;">Description</th>
                            <th class="py-3 text-muted fw-semibold" style="font-size:.75rem; text-transform:uppercase; letter-spacing:.06em; border-bottom: 2px solid #e9ecef;">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-nowrap">
                                <span class="fw-semibold" style="font-size:.85rem; color:#0f172a;">{{ $log->created_at->format('M d, Y') }}</span><br>
                                <span class="text-muted" style="font-size:.75rem;">{{ $log->created_at->format('h:i:s A') }}</span>
                            </td>
                            <td class="py-3">
                                @php
                                    $badgeMap = [
                                        'login_success'    => ['success',  'sign-in-alt'],
                                        'login_failed'     => ['danger',   'exclamation-triangle'],
                                        'logout'           => ['secondary','sign-out-alt'],
                                        'registered'       => ['info',     'user-plus'],
                                        'mfa_verified'     => ['primary',  'shield-alt'],
                                        'account_approved' => ['success',  'check-circle'],
                                        'account_rejected' => ['danger',   'times-circle'],
                                        'order_placed'     => ['warning',  'shopping-cart'],
                                        'order_cancelled'  => ['danger',   'ban'],
                                        'password_changed' => ['info',     'key'],
                                    ];
                                    [$color, $icon] = $badgeMap[$log->action] ?? ['secondary', 'circle'];
                                @endphp
                                <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} border-opacity-25" style="font-size:.72rem; padding:.35rem .65rem; border-radius:20px;">
                                    <i class="fas fa-{{ $icon }} me-1"></i>
                                    {{ ucwords(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($log->user)
                                    <span class="fw-semibold" style="font-size:.85rem;">{{ $log->user->name }}</span><br>
                                    <span class="text-muted" style="font-size:.75rem;">{{ $log->user->email }}</span>
                                @else
                                    <span class="text-muted" style="font-size:.82rem;">—</span>
                                @endif
                            </td>
                            <td class="py-3" style="font-size:.85rem; max-width:300px;">
                                {{ $log->description }}
                                @if($log->metadata)
                                    <button class="btn btn-link btn-sm p-0 ms-1 text-muted" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#meta-{{ $log->id }}">
                                        <i class="fas fa-info-circle" style="font-size:.75rem;"></i>
                                    </button>
                                    <div class="collapse mt-1" id="meta-{{ $log->id }}">
                                        <pre class="bg-light rounded p-2 mb-0" style="font-size:.7rem;">{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 text-muted" style="font-size:.82rem; font-family: monospace;">
                                {{ $log->ip_address ?? '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-list fa-2x mb-2 d-block opacity-25"></i>
                                No activity records found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
            <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} entries</span>
                {{ $logs->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
