@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="dashboard-header p-4 rounded-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%); margin-top: -1rem; position: relative; overflow: hidden;">
                <div style="position: absolute; inset: 0; background: url('https://images.unsplash.com/photo-1518770660439-4636190af475?w=1600&q=80') center/cover no-repeat; opacity: 0.05; pointer-events: none;"></div>
                <div class="position-relative" style="z-index: 1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;">
                            <i class="fas fa-crown me-1"></i> Administrator Access
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold text-white mb-1" style="font-size: 2rem;">Pending Approvals</h2>
                            <p class="text-white-50 mb-0" style="font-size: 0.9rem;">
                                {{ $pending->total() }} account(s) awaiting admin review
                            </p>
                        </div>
                        @if($pending->total() > 0)
                        <span style="background: rgba(251,191,36,0.2); border: 1px solid rgba(251,191,36,0.4); color: #fbbf24; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <i class="fas fa-clock"></i> {{ $pending->total() }} Pending
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert border-0 mb-4" style="background: #dcfce7; color: #166534; border-radius: 12px; padding: 1rem 1.25rem;">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert border-0 mb-4" style="background: #fee2e2; color: #991b1b; border-radius: 12px; padding: 1rem 1.25rem;">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0" style="border-radius: 20px; box-shadow: 0 10px 40px -5px rgba(13, 20, 40, 0.15); overflow: hidden;">

                <!-- Card Header -->
                <div class="card-header bg-transparent py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #eef2f6;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #f59e0b15, #d9770615); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user-clock" style="color: #d97706;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="color: #0f172a; font-size: 1rem;">Awaiting Review</h6>
                            <small class="text-muted">Accounts that completed MFA and need approval</small>
                        </div>
                    </div>
                    @if($pending->total() > 0)
                    <span style="background: #fef3c7; color: #d97706; padding: 0.35rem 0.9rem; border-radius: 30px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;">
                        <i class="fas fa-hourglass-half" style="font-size: 0.65rem;"></i> {{ $pending->total() }} pending
                    </span>
                    @endif
                </div>

                @if($pending->isEmpty())
                <!-- Empty State -->
                <div class="card-body py-5 text-center">
                    <div style="width: 72px; height: 72px; border-radius: 20px; background: linear-gradient(135deg, #dcfce7, #bbf7d0); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i class="fas fa-user-check" style="font-size: 1.8rem; color: #16a34a;"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="color: #0f172a;">All caught up!</h6>
                    <p class="text-muted mb-0" style="font-size: 0.88rem;">No pending accounts. All registrations have been reviewed.</p>
                </div>

                @else
                <!-- Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="min-width: 700px;">
                            <thead style="background: #f8fafd;">
                                <tr>
                                    <th class="px-4 py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">User</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Email</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">MFA Method</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Verified At</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pending as $user)
                                <tr style="border-bottom: 1px solid #eef2f6;">
                                    <!-- User -->
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.8rem; flex-shrink: 0;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <span style="color: #0f172a; font-weight: 600;">{{ $user->name }}</span>
                                        </div>
                                    </td>

                                    <!-- Email -->
                                    <td class="py-3" style="color: #475569;">{{ $user->email }}</td>

                                    <!-- MFA Method -->
                                    <td class="py-3">
                                        @if($user->mfa_method === 'totp')
                                            <span style="background: #e0f2fe; color: #0369a1; padding: 0.3rem 0.8rem; border-radius: 30px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                                                <i class="fas fa-mobile-alt"></i> TOTP App
                                            </span>
                                        @else
                                            <span style="background: #f3e8ff; color: #7c3aed; padding: 0.3rem 0.8rem; border-radius: 30px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                                                <i class="fas fa-envelope"></i> Email OTP
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Verified At -->
                                    <td class="py-3">
                                        <div style="color: #0f172a; font-size: 0.85rem; font-weight: 500;">
                                            {{ $user->mfa_verified_at?->format('M d, Y') }}
                                        </div>
                                        <div style="color: #94a3b8; font-size: 0.75rem;">
                                            {{ $user->mfa_verified_at?->format('g:i A') }}
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            {{-- Approve --}}
                                            <form method="POST" action="{{ route('admin.pending-users.approve', $user) }}" class="d-inline"
                                                  onsubmit="return confirm('Approve account for {{ addslashes($user->name) }}?')">
                                                @csrf
                                                <button type="submit" class="action-btn approve-btn" title="Approve">
                                                    <i class="fas fa-check me-1"></i> Approve
                                                </button>
                                            </form>

                                            {{-- Reject --}}
                                            <button type="button" class="action-btn reject-btn" title="Reject"
                                                    onclick="openModal('rejectOverlay-{{ $user->id }}')">
                                                <i class="fas fa-times me-1"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card Footer with Pagination -->
                <div class="card-footer bg-transparent py-3 px-4" style="border-top: 1px solid #eef2f6;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <small class="text-muted">Showing {{ $pending->firstItem() }}–{{ $pending->lastItem() }} of {{ $pending->total() }} pending accounts</small>

                        @if($pending->hasPages())
                        <nav class="custom-pagination">
                            @if($pending->onFirstPage())
                                <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                            @else
                                <a href="{{ $pending->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                            @endif

                            @php
                                $current = $pending->currentPage();
                                $last    = $pending->lastPage();
                                $start   = max(1, $current - 2);
                                $end     = min($last, $current + 2);
                            @endphp

                            @if($start > 1)
                                <a href="{{ $pending->url(1) }}" class="page-btn">1</a>
                                @if($start > 2)<span class="page-btn dots">…</span>@endif
                            @endif

                            @for($p = $start; $p <= $end; $p++)
                                @if($p === $current)
                                    <span class="page-btn active">{{ $p }}</span>
                                @else
                                    <a href="{{ $pending->url($p) }}" class="page-btn">{{ $p }}</a>
                                @endif
                            @endfor

                            @if($end < $last)
                                @if($end < $last - 1)<span class="page-btn dots">…</span>@endif
                                <a href="{{ $pending->url($last) }}" class="page-btn">{{ $last }}</a>
                            @endif

                            @if($pending->hasMorePages())
                                <a href="{{ $pending->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                            @else
                                <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                            @endif
                        </nav>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- ==================== PER-USER REJECT MODALS ==================== --}}
@foreach($pending as $user)
<div class="modal-overlay" id="rejectOverlay-{{ $user->id }}" onclick="handleOverlayClick(event, 'rejectOverlay-{{ $user->id }}')">
    <div class="modal-box" style="max-width: 500px;">
        <div class="modal-box-header">
            <div class="d-flex align-items-center gap-3">
                <div class="modal-icon-box" style="background: linear-gradient(135deg, #e11d48, #9f1239);">
                    <i class="fas fa-user-times text-white"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color: #0f172a;">Reject Account</h5>
                    <small class="text-muted">This action will notify the user by email</small>
                </div>
            </div>
            <button class="modal-close-btn" onclick="closeModal('rejectOverlay-{{ $user->id }}')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-box-body">

            {{-- User Preview --}}
            <div style="display: flex; align-items: center; gap: 0.85rem; padding: 0.85rem 1rem; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; margin-bottom: 1.25rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1rem; flex-shrink: 0;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem;">{{ $user->name }}</div>
                    <div style="font-size: 0.8rem; color: #92400e;">{{ $user->email }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.pending-users.reject', $user) }}">
                @csrf
                <div class="mb-1">
                    <label class="modal-label">Rejection Reason <span style="color:#94a3b8; font-weight:400; text-transform:none;">(optional)</span></label>
                    <textarea name="rejection_reason" class="modal-input modal-textarea"
                              placeholder="Explain why this account is being rejected… The user will receive this in their notification email."></textarea>
                </div>

                <div class="modal-box-footer">
                    <button type="button" onclick="closeModal('rejectOverlay-{{ $user->id }}')" class="modal-btn-cancel">
                        <i class="fas fa-arrow-left me-2"></i>Go Back
                    </button>
                    <button type="submit" class="modal-btn-submit" style="background: linear-gradient(135deg, #e11d48, #9f1239); box-shadow: 0 4px 12px rgba(225,29,72,0.3);">
                        <i class="fas fa-times me-2"></i>Confirm Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
{{-- ================================================================= --}}

<style>
.container-fluid { background: #f8fafd; min-height: 100vh; }

.dashboard-header { background-size: 200% 200% !important; animation: gradientShift 15s ease infinite; }
@keyframes gradientShift {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.card:hover { transform: translateY(-2px); box-shadow: 0 20px 40px -5px rgba(13,20,40,0.2) !important; }
.table-hover tbody tr:hover { background: #f1f5f9; }

/* ---- Action Buttons ---- */
.action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    height: 32px; padding: 0 0.85rem; border-radius: 8px;
    font-size: 0.78rem; font-weight: 600; border: none; cursor: pointer;
    transition: opacity 0.15s, transform 0.15s;
}
.action-btn:hover { opacity: 0.88; transform: translateY(-1px); }
.approve-btn { background: linear-gradient(135deg, #16a34a, #14532d); color: #fff; box-shadow: 0 3px 8px rgba(22,163,74,0.25); }
.reject-btn  { background: #fff1f2; color: #e11d48; box-shadow: 0 2px 6px rgba(225,29,72,0.1); }
.reject-btn:hover { background: #ffe4e6; }

/* ---- Pagination ---- */
.custom-pagination { display: flex; align-items: center; gap: 4px; }
.page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 8px; border-radius: 10px;
    font-size: 0.82rem; font-weight: 600; text-decoration: none;
    border: 1px solid #e2e8f0; background: #ffffff; color: #475569;
    transition: background 0.18s, border-color 0.18s, color 0.18s, transform 0.18s, box-shadow 0.18s;
    cursor: pointer; user-select: none;
}
.page-btn:hover { background: #eff6ff; border-color: #2563eb; color: #2563eb; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(37,99,235,0.12); }
.page-btn.active { background: linear-gradient(135deg, #2563eb, #1e3a8a); border-color: transparent; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,0.35); transform: translateY(-1px); pointer-events: none; }
.page-btn.disabled { background: #f8fafd; border-color: #e2e8f0; color: #cbd5e1; pointer-events: none; cursor: default; }
.page-btn.dots { border-color: transparent; background: transparent; color: #94a3b8; pointer-events: none; cursor: default; letter-spacing: 0.05em; }

/* ---- Modal Overlay ---- */
.modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 1050;
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
    align-items: center; justify-content: center; padding: 1rem;
}
.modal-overlay.open { display: flex; animation: fadeIn 0.2s ease; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* ---- Modal Box ---- */
.modal-box {
    background: #ffffff; border-radius: 20px; width: 100%; max-width: 640px;
    max-height: 90vh; overflow-y: auto;
    box-shadow: 0 25px 60px -10px rgba(13, 20, 40, 0.3);
    animation: slideUp 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* ---- Modal Header ---- */
.modal-box-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.25rem 1.5rem; border-bottom: 1px solid #eef2f6;
    background: #f8fafd; position: sticky; top: 0; z-index: 1;
}
.modal-icon-box {
    width: 42px; height: 42px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.modal-close-btn {
    width: 34px; height: 34px; border-radius: 10px;
    border: 1px solid #e2e8f0; background: #fff; color: #64748b;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background 0.15s, color 0.15s, border-color 0.15s; font-size: 0.85rem;
}
.modal-close-btn:hover { background: #fff1f2; color: #e11d48; border-color: #fecdd3; }

/* ---- Modal Body ---- */
.modal-box-body { padding: 1.5rem; }
.modal-label {
    display: block; font-size: 0.78rem; font-weight: 700;
    color: #374151; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.4rem;
}
.modal-input {
    width: 100%; height: 42px; padding: 0 0.85rem;
    border-radius: 10px; border: 1.5px solid #e2e8f0;
    background: #f8fafd; color: #0f172a; font-size: 0.9rem;
    outline: none; transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
    appearance: auto;
}
.modal-textarea { height: 100px !important; padding: 0.6rem 0.85rem !important; resize: vertical; line-height: 1.5; }
.modal-input:focus { border-color: #2563eb; background: #ffffff; box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }

/* ---- Modal Footer ---- */
.modal-box-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;
    margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #eef2f6;
}
.modal-btn-cancel {
    padding: 0.55rem 1.3rem; border-radius: 10px;
    border: 1.5px solid #e2e8f0; background: #f8fafd; color: #475569;
    font-weight: 600; font-size: 0.85rem; cursor: pointer;
    transition: background 0.15s, border-color 0.15s;
}
.modal-btn-cancel:hover { background: #f1f5f9; border-color: #cbd5e1; }
.modal-btn-submit {
    padding: 0.55rem 1.5rem; border-radius: 10px; border: none;
    background: linear-gradient(135deg, #2563eb, #1e3a8a);
    color: #ffffff; font-weight: 600; font-size: 0.85rem; cursor: pointer;
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
    transition: opacity 0.15s, transform 0.15s;
}
.modal-btn-submit:hover { opacity: 0.92; transform: translateY(-1px); }
</style>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    if (!document.querySelector('.modal-overlay.open')) {
        document.body.style.overflow = '';
    }
}
function handleOverlayClick(e, id) {
    if (e.target === document.getElementById(id)) closeModal(id);
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(el => el.classList.remove('open'));
        document.body.style.overflow = '';
    }
});
</script>
@endsection
