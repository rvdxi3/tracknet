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
                    <div>
                        <h2 class="fw-bold text-white mb-1" style="font-size: 2rem;">Customer Management</h2>
                        <p class="text-white-50 mb-0" style="font-size: 0.9rem;">All registered customer accounts</p>
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

    <!-- Customers Table Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0" style="border-radius: 20px; box-shadow: 0 10px 40px -5px rgba(13, 20, 40, 0.15); overflow: hidden;">

                <!-- Card Header -->
                <div class="card-header bg-transparent py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid #eef2f6;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #2563eb15, #1e3a8a15); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-users" style="color: #2563eb;"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="color: #0f172a; font-size: 1rem;">All Customers</h6>
                            <small class="text-muted">Registered customer accounts</small>
                        </div>
                    </div>
                    <span style="background: #2563eb20; color: #2563eb; padding: 0.35rem 0.9rem; border-radius: 30px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                        <i class="fas fa-users" style="font-size: 0.65rem;"></i> {{ $customers->total() }} total
                    </span>
                </div>

                <!-- Table -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="min-width: 700px;">
                            <thead style="background: #f8fafd;">
                                <tr>
                                    <th class="px-4 py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">#</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Customer</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Email</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Member Since</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Status</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Orders</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                <tr style="border-bottom: 1px solid #eef2f6;">
                                    <td class="px-4 py-3" style="color: #94a3b8; font-size: 0.8rem; font-weight: 600;">{{ $customers->firstItem() + $loop->index }}</td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #2563eb, #1e3a8a); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.8rem; flex-shrink: 0;">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </div>
                                            <span style="color: #0f172a; font-weight: 600;">{{ $customer->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3" style="color: #475569;">{{ $customer->email }}</td>
                                    <td class="py-3" style="color: #475569; font-size: 0.88rem;">{{ $customer->created_at->format('M d, Y') }}</td>
                                    <td class="py-3">
                                        @if($customer->is_active)
                                            <span style="background: #dcfce7; color: #16a34a; padding: 0.3rem 0.8rem; border-radius: 30px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                                                <i class="fas fa-circle" style="font-size: 0.4rem;"></i> Active
                                            </span>
                                        @else
                                            <span style="background: #f1f5f9; color: #64748b; padding: 0.3rem 0.8rem; border-radius: 30px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                                                <i class="fas fa-circle" style="font-size: 0.4rem;"></i> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <span style="background: #2563eb20; color: #2563eb; padding: 0.3rem 0.75rem; border-radius: 30px; font-size: 0.75rem; font-weight: 600;">
                                            {{ $customer->orders_count }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-1">
                                            {{-- View --}}
                                            <button type="button" onclick="openModal('viewCustOverlay-{{ $customer->id }}')"
                                                class="btn btn-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; background: #eff6ff; color: #2563eb; border: none;" title="View">
                                                <i class="fas fa-eye" style="font-size: 0.75rem;"></i>
                                            </button>
                                            {{-- Edit --}}
                                            <button type="button" onclick="openModal('editCustOverlay-{{ $customer->id }}')"
                                                class="btn btn-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; background: #f0fdf4; color: #16a34a; border: none;" title="Edit">
                                                <i class="fas fa-edit" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div style="width: 60px; height: 60px; border-radius: 16px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                                            <i class="fas fa-users" style="font-size: 1.5rem; color: #94a3b8;"></i>
                                        </div>
                                        <p class="text-muted mb-0" style="font-size: 0.88rem;">No customers found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card Footer with Pagination -->
                <div class="card-footer bg-transparent py-3 px-4" style="border-top: 1px solid #eef2f6;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <small class="text-muted">Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }} customers</small>

                        @if($customers->hasPages())
                        <nav class="custom-pagination">
                            @if($customers->onFirstPage())
                                <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                            @else
                                <a href="{{ $customers->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                            @endif

                            @php
                                $current = $customers->currentPage();
                                $last    = $customers->lastPage();
                                $start   = max(1, $current - 2);
                                $end     = min($last, $current + 2);
                            @endphp

                            @if($start > 1)
                                <a href="{{ $customers->url(1) }}" class="page-btn">1</a>
                                @if($start > 2)<span class="page-btn dots">…</span>@endif
                            @endif

                            @for($p = $start; $p <= $end; $p++)
                                @if($p === $current)
                                    <span class="page-btn active">{{ $p }}</span>
                                @else
                                    <a href="{{ $customers->url($p) }}" class="page-btn">{{ $p }}</a>
                                @endif
                            @endfor

                            @if($end < $last)
                                @if($end < $last - 1)<span class="page-btn dots">…</span>@endif
                                <a href="{{ $customers->url($last) }}" class="page-btn">{{ $last }}</a>
                            @endif

                            @if($customers->hasMorePages())
                                <a href="{{ $customers->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                            @else
                                <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                            @endif
                        </nav>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ==================== PER-CUSTOMER VIEW & EDIT MODALS ==================== --}}
@foreach($customers as $customer)

{{-- VIEW MODAL --}}
<div class="modal-overlay" id="viewCustOverlay-{{ $customer->id }}" onclick="handleOverlayClick(event, 'viewCustOverlay-{{ $customer->id }}')">
    <div class="modal-box" style="max-width: 540px;">
        <div class="modal-box-header">
            <div class="d-flex align-items-center gap-3">
                <div class="modal-icon-box" style="background: linear-gradient(135deg, #2563eb, #1e3a8a);">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color: #0f172a;">Customer Profile</h5>
                    <small class="text-muted">Account overview</small>
                </div>
            </div>
            <button class="modal-close-btn" onclick="closeModal('viewCustOverlay-{{ $customer->id }}')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-box-body">

            {{-- Avatar + Name + Status --}}
            <div class="text-center mb-4">
                <div style="width: 72px; height: 72px; border-radius: 18px; background: linear-gradient(135deg, #2563eb, #1e3a8a); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.8rem; margin: 0 auto 0.75rem;">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <h6 class="fw-bold mb-1" style="color: #0f172a; font-size: 1.05rem;">{{ $customer->name }}</h6>
                <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 0.5rem;">{{ $customer->email }}</p>
                @if($customer->is_active)
                    <span style="background: #dcfce7; color: #16a34a; padding: 0.3rem 0.9rem; border-radius: 30px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                        <i class="fas fa-circle" style="font-size: 0.4rem;"></i> Active Account
                    </span>
                @else
                    <span style="background: #f1f5f9; color: #64748b; padding: 0.3rem 0.9rem; border-radius: 30px; font-size: 0.75rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem;">
                        <i class="fas fa-circle" style="font-size: 0.4rem;"></i> Inactive
                    </span>
                @endif
            </div>

            {{-- Stats Row --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div style="background: #f8fafd; border-radius: 12px; padding: 0.85rem 1rem; text-align: center; border: 1px solid #eef2f6;">
                    <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">{{ $customer->orders_count }}</div>
                    <div style="font-size: 0.72rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total Orders</div>
                </div>
                <div style="background: #f8fafd; border-radius: 12px; padding: 0.85rem 1rem; text-align: center; border: 1px solid #eef2f6;">
                    <div style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">₱{{ number_format($customer->orders->sum('total'), 2) }}</div>
                    <div style="font-size: 0.72rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Total Spent</div>
                </div>
            </div>

            {{-- Account Info Grid --}}
            <div style="background: #f8fafd; border-radius: 12px; padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div class="d-flex align-items-center justify-content-between">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Member Since</span>
                    <span style="color: #0f172a; font-weight: 500; font-size: 0.88rem;">{{ $customer->created_at->format('M d, Y') }}</span>
                </div>
                <div style="height: 1px; background: #eef2f6;"></div>
                <div class="d-flex align-items-center justify-content-between">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">MFA Verified</span>
                    @if($customer->mfa_verified_at)
                        <span style="background: #dcfce7; color: #16a34a; padding: 0.2rem 0.7rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Yes</span>
                    @else
                        <span style="background: #fef3c7; color: #d97706; padding: 0.2rem 0.7rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">No</span>
                    @endif
                </div>
                <div style="height: 1px; background: #eef2f6;"></div>
                <div class="d-flex align-items-center justify-content-between">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Customer ID</span>
                    <span style="color: #94a3b8; font-weight: 600; font-size: 0.85rem;">#{{ $customer->id }}</span>
                </div>
            </div>

            {{-- Recent Orders Preview --}}
            @if($customer->orders->isNotEmpty())
            <p style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.6rem;">Recent Orders</p>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @foreach($customer->orders as $order)
                @php
                    $payStatus = $order->sale->payment_status ?? null;
                    $payColors = ['paid'=>['bg'=>'#dcfce7','text'=>'#16a34a'],'refunded'=>['bg'=>'#fee2e2','text'=>'#dc2626'],'pending'=>['bg'=>'#fef3c7','text'=>'#d97706']];
                    $pc = $payColors[$payStatus] ?? ['bg'=>'#f1f5f9','text'=>'#64748b'];
                @endphp
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.55rem 0.85rem; background: #f8fafd; border-radius: 10px; border: 1px solid #eef2f6;">
                    <div>
                        <div style="font-size: 0.83rem; font-weight: 600; color: #0f172a;">{{ $order->order_number }}</div>
                        <div style="font-size: 0.73rem; color: #94a3b8;">{{ $order->created_at->format('M d, Y') }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">₱{{ number_format($order->total, 2) }}</span>
                        @if($payStatus)
                        <span style="background: {{ $pc['bg'] }}; color: {{ $pc['text'] }}; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">{{ ucfirst($payStatus) }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
                @if($customer->orders_count > 3)
                <p style="font-size: 0.78rem; color: #94a3b8; text-align: center; margin-top: 0.25rem;">+{{ $customer->orders_count - 3 }} more order(s)</p>
                @endif
            </div>
            @endif

            <div class="modal-box-footer">
                <button type="button" onclick="closeModal('viewCustOverlay-{{ $customer->id }}')" class="modal-btn-cancel"><i class="fas fa-times me-2"></i>Close</button>
                <button type="button" onclick="closeModal('viewCustOverlay-{{ $customer->id }}'); openModal('editCustOverlay-{{ $customer->id }}')" class="modal-btn-submit">
                    <i class="fas fa-edit me-2"></i>Edit Customer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal-overlay" id="editCustOverlay-{{ $customer->id }}" onclick="handleOverlayClick(event, 'editCustOverlay-{{ $customer->id }}')">
    <div class="modal-box" style="max-width: 480px;">
        <div class="modal-box-header">
            <div class="d-flex align-items-center gap-3">
                <div class="modal-icon-box" style="background: linear-gradient(135deg, #16a34a, #14532d);">
                    <i class="fas fa-user-edit text-white"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0" style="color: #0f172a;">Edit Customer</h5>
                    <small class="text-muted">Updating: {{ $customer->name }}</small>
                </div>
            </div>
            <button class="modal-close-btn" onclick="closeModal('editCustOverlay-{{ $customer->id }}')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-box-body">
            <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="_customer_id" value="{{ $customer->id }}">

                <div class="row g-3">
                    <div class="col-12">
                        <label class="modal-label">Full Name</label>
                        <input type="text" name="name" class="modal-input @if($errors->any() && old('_customer_id') == $customer->id) @error('name') is-invalid @enderror @endif"
                               value="{{ old('_customer_id') == $customer->id ? old('name') : $customer->name }}" required>
                        @if($errors->any() && old('_customer_id') == $customer->id)
                            @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="modal-label">Email Address</label>
                        <input type="email" name="email" class="modal-input @if($errors->any() && old('_customer_id') == $customer->id) @error('email') is-invalid @enderror @endif"
                               value="{{ old('_customer_id') == $customer->id ? old('email') : $customer->email }}" required>
                        @if($errors->any() && old('_customer_id') == $customer->id)
                            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @endif
                    </div>
                </div>

                <div class="modal-box-footer">
                    <button type="button" onclick="closeModal('editCustOverlay-{{ $customer->id }}')" class="modal-btn-cancel"><i class="fas fa-times me-2"></i>Cancel</button>
                    <button type="submit" class="modal-btn-submit" style="background: linear-gradient(135deg, #16a34a, #14532d); box-shadow: 0 4px 12px rgba(22,163,74,0.3);">
                        <i class="fas fa-save me-2"></i>Save Changes
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
.btn[style*="background: #eff6ff"]:hover { background: #dbeafe !important; }
.btn[style*="background: #f0fdf4"]:hover { background: #dcfce7 !important; }

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

.modal-box-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.25rem 1.5rem; border-bottom: 1px solid #eef2f6;
    background: #f8fafd; position: sticky; top: 0; z-index: 1;
}
.modal-icon-box { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.modal-close-btn {
    width: 34px; height: 34px; border-radius: 10px; border: 1px solid #e2e8f0;
    background: #fff; color: #64748b; display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background 0.15s, color 0.15s, border-color 0.15s; font-size: 0.85rem;
}
.modal-close-btn:hover { background: #fff1f2; color: #e11d48; border-color: #fecdd3; }

.modal-box-body { padding: 1.5rem; }
.modal-label { display: block; font-size: 0.78rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.4rem; }
.modal-input {
    width: 100%; height: 42px; padding: 0 0.85rem; border-radius: 10px;
    border: 1.5px solid #e2e8f0; background: #f8fafd; color: #0f172a; font-size: 0.9rem;
    outline: none; transition: border-color 0.18s, box-shadow 0.18s, background 0.18s; appearance: auto;
}
.modal-input:focus { border-color: #2563eb; background: #ffffff; box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }
.modal-input.is-invalid { border-color: #e11d48; }

.modal-box-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;
    margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #eef2f6;
}
.modal-btn-cancel {
    padding: 0.55rem 1.3rem; border-radius: 10px; border: 1.5px solid #e2e8f0;
    background: #f8fafd; color: #475569; font-weight: 600; font-size: 0.85rem;
    cursor: pointer; transition: background 0.15s, border-color 0.15s;
}
.modal-btn-cancel:hover { background: #f1f5f9; border-color: #cbd5e1; }
.modal-btn-submit {
    padding: 0.55rem 1.5rem; border-radius: 10px; border: none;
    background: linear-gradient(135deg, #2563eb, #1e3a8a); color: #ffffff;
    font-weight: 600; font-size: 0.85rem; cursor: pointer;
    box-shadow: 0 4px 12px rgba(37,99,235,0.3); transition: opacity 0.15s, transform 0.15s;
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

document.addEventListener('DOMContentLoaded', function() {
    @if($errors->any() && old('_customer_id'))
        openModal('editCustOverlay-{{ old('_customer_id') }}');
    @endif
});
</script>
@endsection
