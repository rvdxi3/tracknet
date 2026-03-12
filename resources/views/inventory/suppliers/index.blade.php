@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- ── Page Header ── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#2563eb 100%); margin-top:-1rem; position:relative; overflow:hidden;">
                <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>
                <div style="position:absolute;bottom:-60px;left:40%;width:260px;height:260px;border-radius:50%;background:rgba(255,255,255,.02);pointer-events:none;"></div>
                <div class="position-relative d-flex align-items-center justify-content-between flex-wrap gap-3" style="z-index:1;">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);color:rgba(255,255,255,.9);padding:.35rem 1rem;border-radius:20px;font-size:.7rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;">
                                <i class="fas fa-warehouse me-1"></i> Procurement
                            </span>
                        </div>
                        <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">Suppliers</h2>
                        <p class="text-white-50 mb-0" style="font-size:.9rem;">
                            <i class="fas fa-handshake me-1"></i> Manage your supplier network
                        </p>
                    </div>
                    <button onclick="openSupModal('addSupplierOverlay')" class="sup-hdr-btn">
                        <i class="fas fa-plus me-2"></i> Add Supplier
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Suppliers Table ── --}}
    <div class="sup-card">
        <div class="sup-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="sup-card-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <div class="sup-card-title">Supplier Directory</div>
                    <div class="sup-card-sub">{{ $suppliers->total() }} total suppliers</div>
                </div>
            </div>
        </div>
        <div style="padding:0;">
            <div class="table-responsive">
                <table class="sup-table">
                    <thead>
                        <tr>
                            <th style="width:44px;">#</th>
                            <th>Supplier Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td><span class="sup-id">{{ $supplier->id }}</span></td>
                            <td>
                                <div class="sup-name">{{ $supplier->name }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="sup-avatar">{{ strtoupper(substr($supplier->contact_person, 0, 1)) }}</div>
                                    <span class="sup-contact">{{ $supplier->contact_person }}</span>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:{{ $supplier->email }}" class="sup-email">{{ $supplier->email }}</a>
                            </td>
                            <td><span class="sup-phone">{{ $supplier->phone }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button onclick="openSupModal('viewSup-{{ $supplier->id }}')" class="sup-act-btn view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="openSupModal('editSup-{{ $supplier->id }}')" class="sup-act-btn edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('inventory.suppliers.destroy', $supplier) }}" method="POST" class="d-inline" id="delSupForm-{{ $supplier->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmSupDelete({{ $supplier->id }}, '{{ addslashes($supplier->name) }}')" class="sup-act-btn del" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="sup-empty-state">
                                    <div class="sup-empty-icon"><i class="fas fa-handshake"></i></div>
                                    <div class="sup-empty-text">No suppliers found</div>
                                    <div class="sup-empty-sub">Click "Add Supplier" to add your first supplier</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($suppliers->hasPages())
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3" style="border-top:1px solid #f1f5f9;">
                <div style="font-size:.78rem;color:#64748b;">
                    Showing <strong>{{ $suppliers->firstItem() }}–{{ $suppliers->lastItem() }}</strong>
                    of <strong>{{ $suppliers->total() }}</strong> suppliers
                </div>
                <div class="d-flex gap-1 align-items-center">
                    @if($suppliers->onFirstPage())
                        <span class="sup-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $suppliers->previousPageUrl() }}" class="sup-page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($suppliers->getUrlRange(max(1, $suppliers->currentPage()-2), min($suppliers->lastPage(), $suppliers->currentPage()+2)) as $page => $url)
                        <a href="{{ $url }}" class="sup-page-btn {{ $page == $suppliers->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($suppliers->hasMorePages())
                        <a href="{{ $suppliers->nextPageUrl() }}" class="sup-page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="sup-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════
     ADD SUPPLIER MODAL
══════════════════════════════════════════ --}}
<div class="sup-overlay" id="addSupplierOverlay">
    <div class="sup-modal" style="max-width:580px;">
        {{-- Gradient Header (image-2 style) --}}
        <div class="sup-modal-hdr">
            <button onclick="closeSupModal('addSupplierOverlay')" class="sup-modal-close">&times;</button>
            <div class="sup-modal-hdr-tag"><i class="fas fa-plus me-1"></i> New Supplier</div>
            <div class="sup-modal-hdr-title">Add Supplier</div>
            <div class="sup-modal-hdr-sub">Fill in the supplier details below</div>
        </div>
        <div class="sup-modal-body">
            <form id="addSupForm" method="POST" action="{{ route('inventory.suppliers.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="sup-form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="sup-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('name')) is-invalid @endif"
                               value="{{ old('_method') !== 'PUT' ? old('name') : '' }}"
                               placeholder="e.g. Intel Corporation" required>
                        @if($errors->has('name') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="sup-form-label">Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="contact_person"
                               class="sup-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('contact_person')) is-invalid @endif"
                               value="{{ old('_method') !== 'PUT' ? old('contact_person') : '' }}"
                               placeholder="e.g. John Smith" required>
                        @if($errors->has('contact_person') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('contact_person') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="sup-form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="sup-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('email')) is-invalid @endif"
                               value="{{ old('_method') !== 'PUT' ? old('email') : '' }}"
                               placeholder="e.g. contact@supplier.com" required>
                        @if($errors->has('email') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="sup-form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone"
                               class="sup-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('phone')) is-invalid @endif"
                               value="{{ old('_method') !== 'PUT' ? old('phone') : '' }}"
                               placeholder="e.g. 800-123-4567" required>
                        @if($errors->has('phone') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="sup-form-label">Address <span class="text-danger">*</span></label>
                        <textarea name="address" rows="3"
                                  class="sup-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('address')) is-invalid @endif"
                                  placeholder="Full supplier address...">{{ old('_method') !== 'PUT' ? old('address') : '' }}</textarea>
                        @if($errors->has('address') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('address') }}</div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
        <div class="sup-modal-footer">
            <button onclick="closeSupModal('addSupplierOverlay')" class="sup-btn sup-btn-neutral">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button type="submit" form="addSupForm" class="sup-btn sup-btn-green">
                <i class="fas fa-save me-1"></i> Save Supplier
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     VIEW SUPPLIER MODALS (per row)
══════════════════════════════════════════ --}}
@foreach($suppliers as $supplier)
<div class="sup-overlay" id="viewSup-{{ $supplier->id }}">
    <div class="sup-modal" style="max-width:520px;">
        {{-- Gradient Header --}}
        <div class="sup-modal-hdr">
            <button onclick="closeSupModal('viewSup-{{ $supplier->id }}')" class="sup-modal-close">&times;</button>
            <div class="sup-modal-hdr-tag"><i class="fas fa-handshake me-1"></i> Supplier Details</div>
            <div class="sup-modal-hdr-title">{{ $supplier->name }}</div>
            <div class="sup-modal-hdr-sub">{{ $supplier->contact_person }} &middot; {{ $supplier->phone }}</div>
        </div>
        <div class="sup-modal-body">
            <div class="sup-info-grid">
                <div class="sup-info-item sup-info-full">
                    <div class="sup-info-label"><i class="fas fa-building me-1"></i> Supplier Name</div>
                    <div class="sup-info-value">{{ $supplier->name }}</div>
                </div>
                <div class="sup-info-item">
                    <div class="sup-info-label"><i class="fas fa-user me-1"></i> Contact Person</div>
                    <div class="sup-info-value">
                        <div class="d-flex align-items-center gap-2">
                            <div class="sup-avatar sm">{{ strtoupper(substr($supplier->contact_person, 0, 1)) }}</div>
                            {{ $supplier->contact_person }}
                        </div>
                    </div>
                </div>
                <div class="sup-info-item">
                    <div class="sup-info-label"><i class="fas fa-phone me-1"></i> Phone</div>
                    <div class="sup-info-value">{{ $supplier->phone }}</div>
                </div>
                <div class="sup-info-item sup-info-full">
                    <div class="sup-info-label"><i class="fas fa-envelope me-1"></i> Email</div>
                    <div class="sup-info-value">
                        <a href="mailto:{{ $supplier->email }}" class="sup-email">{{ $supplier->email }}</a>
                    </div>
                </div>
                <div class="sup-info-item sup-info-full">
                    <div class="sup-info-label"><i class="fas fa-map-marker-alt me-1"></i> Address</div>
                    <div class="sup-info-value" style="white-space:pre-line;">{{ $supplier->address }}</div>
                </div>
            </div>
        </div>
        <div class="sup-modal-footer">
            <button onclick="closeSupModal('viewSup-{{ $supplier->id }}'); openSupModal('editSup-{{ $supplier->id }}')" class="sup-btn sup-btn-amber">
                <i class="fas fa-edit me-1"></i> Edit
            </button>
            <button onclick="closeSupModal('viewSup-{{ $supplier->id }}')" class="sup-btn sup-btn-neutral">
                <i class="fas fa-times me-1"></i> Close
            </button>
        </div>
    </div>
</div>
@endforeach

{{-- ══════════════════════════════════════════
     EDIT SUPPLIER MODALS (per row)
══════════════════════════════════════════ --}}
@foreach($suppliers as $supplier)
@php $isThisSup = $errors->any() && old('_method') === 'PUT' && old('supplier_id') == $supplier->id; @endphp
<div class="sup-overlay" id="editSup-{{ $supplier->id }}">
    <div class="sup-modal" style="max-width:580px;">
        {{-- Gradient Header --}}
        <div class="sup-modal-hdr" style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#334155 100%);">
            <button onclick="closeSupModal('editSup-{{ $supplier->id }}')" class="sup-modal-close">&times;</button>
            <div class="sup-modal-hdr-tag"><i class="fas fa-edit me-1"></i> Edit Supplier</div>
            <div class="sup-modal-hdr-title">{{ $supplier->name }}</div>
            <div class="sup-modal-hdr-sub">Update supplier information</div>
        </div>
        <div class="sup-modal-body">
            <form id="editSupForm-{{ $supplier->id }}" method="POST" action="{{ route('inventory.suppliers.update', $supplier) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="sup-form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="sup-form-input {{ $isThisSup && $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ $isThisSup ? old('name') : $supplier->name }}" required>
                        @if($isThisSup && $errors->has('name'))
                            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="sup-form-label">Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="contact_person"
                               class="sup-form-input {{ $isThisSup && $errors->has('contact_person') ? 'is-invalid' : '' }}"
                               value="{{ $isThisSup ? old('contact_person') : $supplier->contact_person }}" required>
                        @if($isThisSup && $errors->has('contact_person'))
                            <div class="invalid-feedback">{{ $errors->first('contact_person') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="sup-form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email"
                               class="sup-form-input {{ $isThisSup && $errors->has('email') ? 'is-invalid' : '' }}"
                               value="{{ $isThisSup ? old('email') : $supplier->email }}" required>
                        @if($isThisSup && $errors->has('email'))
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="sup-form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone"
                               class="sup-form-input {{ $isThisSup && $errors->has('phone') ? 'is-invalid' : '' }}"
                               value="{{ $isThisSup ? old('phone') : $supplier->phone }}" required>
                        @if($isThisSup && $errors->has('phone'))
                            <div class="invalid-feedback">{{ $errors->first('phone') }}</div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="sup-form-label">Address <span class="text-danger">*</span></label>
                        <textarea name="address" rows="3"
                                  class="sup-form-input {{ $isThisSup && $errors->has('address') ? 'is-invalid' : '' }}">{{ $isThisSup ? old('address') : $supplier->address }}</textarea>
                        @if($isThisSup && $errors->has('address'))
                            <div class="invalid-feedback">{{ $errors->first('address') }}</div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
        <div class="sup-modal-footer">
            <button onclick="closeSupModal('editSup-{{ $supplier->id }}')" class="sup-btn sup-btn-neutral">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button type="submit" form="editSupForm-{{ $supplier->id }}" class="sup-btn sup-btn-blue">
                <i class="fas fa-save me-1"></i> Update Supplier
            </button>
        </div>
    </div>
</div>
@endforeach

{{-- ══════════════════════════════════════════
     DELETE CONFIRM MODAL
══════════════════════════════════════════ --}}
<div class="sup-overlay" id="deleteSupOverlay">
    <div class="sup-modal" style="max-width:460px;">
        <div class="sup-modal-hdr" style="background:linear-gradient(135deg,#450a0a 0%,#7f1d1d 60%,#dc2626 100%);">
            <button onclick="closeSupModal('deleteSupOverlay')" class="sup-modal-close">&times;</button>
            <div class="sup-modal-hdr-tag"><i class="fas fa-exclamation-triangle me-1"></i> Confirm Deletion</div>
            <div class="sup-modal-hdr-title">Delete Supplier</div>
            <div class="sup-modal-hdr-sub">This action cannot be undone</div>
        </div>
        <div class="sup-modal-body">
            <div style="display:flex;align-items:flex-start;gap:1rem;">
                <div style="width:44px;height:44px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;color:#dc2626;font-size:1.2rem;flex-shrink:0;">
                    <i class="fas fa-trash"></i>
                </div>
                <div>
                    <p style="font-weight:700;color:#0f172a;margin-bottom:.4rem;font-size:.95rem;">
                        Are you sure you want to delete <strong id="deleteSupName" style="color:#dc2626;"></strong>?
                    </p>
                    <p style="font-size:.85rem;color:#64748b;margin-bottom:0;">
                        This will permanently remove the supplier and all associated records.
                    </p>
                </div>
            </div>
        </div>
        <div class="sup-modal-footer">
            <button onclick="closeSupModal('deleteSupOverlay')" class="sup-btn sup-btn-neutral">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button onclick="submitSupDelete()" class="sup-btn sup-btn-red">
                <i class="fas fa-trash me-1"></i> Delete Supplier
            </button>
        </div>
    </div>
</div>

<style>
.container-fluid { background:#f8fafd; min-height:100vh; }

/* ── Header Button ── */
.sup-hdr-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.28);
    color: #fff; padding: .6rem 1.3rem; border-radius: 12px;
    font-weight: 600; font-size: .88rem; cursor: pointer;
    transition: background .15s, border-color .15s, transform .12s;
    backdrop-filter: blur(4px);
}
.sup-hdr-btn:hover  { background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.45); transform: translateY(-1px); }
.sup-hdr-btn:active { transform: scale(.95); }

/* ── Card ── */
.sup-card {
    background: #fff; border-radius: 20px;
    box-shadow: 0 4px 20px rgba(13,20,40,.07);
    border: 1.5px solid #f1f5f9; overflow: hidden; margin-bottom: 1.5rem;
}
.sup-card-header {
    padding: 1rem 1.4rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.sup-card-icon {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .9rem;
    background: linear-gradient(135deg,#2563eb1a,#1e3a8a1a); color: #2563eb;
}
.sup-card-title { font-weight: 700; font-size: .9rem; color: #0f172a; }
.sup-card-sub   { font-size: .72rem; color: #94a3b8; }

/* ── Table ── */
.sup-table { width: 100%; border-collapse: collapse; font-size: .865rem; }
.sup-table thead tr { background: linear-gradient(135deg, #0f172a, #1e3a8a); }
.sup-table th {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #fff; padding: .7rem 1rem; border: none;
}
.sup-table tbody tr { transition: background .12s; }
.sup-table tbody tr:nth-child(even) { background: #f8fafc; }
.sup-table tbody tr:nth-child(odd)  { background: #fff; }
.sup-table tbody tr:hover { background: #eff6ff; }
.sup-table td { padding: .7rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.sup-table tbody tr:last-child td { border-bottom: none; }

/* ── Table Cell Styles ── */
.sup-id      { font-size: .78rem; font-weight: 700; color: #94a3b8; }
.sup-name    { font-weight: 700; color: #0f172a; font-size: .875rem; }
.sup-contact { font-size: .875rem; color: #374151; }
.sup-email   { font-size: .84rem; color: #2563eb; text-decoration: none; }
.sup-email:hover { text-decoration: underline; color: #1e40af; }
.sup-phone   { font-size: .84rem; color: #475569; font-family: monospace; }

/* ── Avatar ── */
.sup-avatar {
    width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg, #2563eb, #1e3a8a);
    color: #fff; font-size: .72rem; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
}
.sup-avatar.sm { width: 26px; height: 26px; font-size: .65rem; }

/* ── Action Buttons ── */
.sup-act-btn {
    width: 32px; height: 32px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .78rem; cursor: pointer; border: 1.5px solid transparent;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
    background: transparent;
}
.sup-act-btn:active { transform: scale(.90); }
.sup-act-btn.view  { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
.sup-act-btn.view:hover  { background: #dbeafe; border-color: #93c5fd; color: #1e40af; }
.sup-act-btn.edit  { background: #fffbeb; border-color: #fde68a; color: #d97706; }
.sup-act-btn.edit:hover  { background: #fef3c7; border-color: #fcd34d; color: #b45309; }
.sup-act-btn.del   { background: #fff1f2; border-color: #fecaca; color: #dc2626; }
.sup-act-btn.del:hover   { background: #fee2e2; border-color: #f87171; color: #b91c1c; }

/* ── Pagination ── */
.sup-page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; border-radius: 8px; padding: 0 .5rem;
    font-size: .8rem; font-weight: 600;
    background: #fff; border: 1.5px solid #e2e8f0; color: #374151;
    text-decoration: none;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.sup-page-btn:hover:not(.disabled):not(.active) { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; transform: translateY(-1px); text-decoration: none; }
.sup-page-btn:active:not(.disabled) { transform: scale(.94); }
.sup-page-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
.sup-page-btn.disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }

/* ── Empty State ── */
.sup-empty-state { text-align: center; padding: 3rem 1rem; }
.sup-empty-icon {
    width: 56px; height: 56px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.4rem; margin-bottom: .75rem;
    background: #dbeafe; color: #2563eb;
}
.sup-empty-text { font-weight: 700; color: #374151; font-size: .95rem; }
.sup-empty-sub  { font-size: .8rem; color: #94a3b8; margin-top: .3rem; }

/* ══════════════════════════════════════════
   MODAL (image-2 style — gradient header)
══════════════════════════════════════════ */
.sup-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,.6); backdrop-filter: blur(4px);
    z-index: 1055; align-items: center; justify-content: center; padding: 1rem;
}
.sup-overlay.open { display: flex; }

.sup-modal {
    background: #fff; border-radius: 20px;
    box-shadow: 0 24px 80px rgba(15,23,42,.3);
    width: 100%; max-height: 92vh; overflow: hidden;
    display: flex; flex-direction: column;
    animation: supModalIn .22s cubic-bezier(.34,1.56,.64,1);
    margin: auto;
}
@keyframes supModalIn { from { opacity:0; transform:translateY(-20px) scale(.96); } to { opacity:1; transform:none; } }

/* ── Gradient Header (image-2 style) ── */
.sup-modal-hdr {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%);
    padding: 1.5rem 1.5rem 1.3rem;
    position: relative;
    border-radius: 20px 20px 0 0;
    flex-shrink: 0;
    overflow: hidden;
}
.sup-modal-hdr::before {
    content: '';
    position: absolute; top: -30px; right: -30px;
    width: 130px; height: 130px; border-radius: 50%;
    background: rgba(255,255,255,.05); pointer-events: none;
}
.sup-modal-hdr::after {
    content: '';
    position: absolute; bottom: -40px; left: 30%;
    width: 160px; height: 160px; border-radius: 50%;
    background: rgba(255,255,255,.03); pointer-events: none;
}
.sup-modal-hdr-tag {
    font-size: .65rem; font-weight: 700; letter-spacing: .12em;
    text-transform: uppercase; color: rgba(255,255,255,.55);
    margin-bottom: .5rem; position: relative; z-index: 1;
}
.sup-modal-hdr-title {
    font-size: 1.45rem; font-weight: 800; color: #fff;
    line-height: 1.2; margin-bottom: .3rem;
    position: relative; z-index: 1;
}
.sup-modal-hdr-sub {
    font-size: .82rem; color: rgba(255,255,255,.55);
    position: relative; z-index: 1;
}

/* ── Circular X button inside gradient header ── */
.sup-modal-close {
    position: absolute; top: 1rem; right: 1rem; z-index: 2;
    width: 34px; height: 34px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; line-height: 1; cursor: pointer;
    background: rgba(255,255,255,.15); border: 1.5px solid rgba(255,255,255,.25); color: #fff;
    transition: background .15s, border-color .15s, transform .12s;
}
.sup-modal-close:hover  { background: rgba(239,68,68,.55); border-color: rgba(239,68,68,.75); }
.sup-modal-close:active { background: rgba(239,68,68,.75); border-color: #ef4444; transform: scale(.88); }

.sup-modal-body { padding: 1.4rem 1.5rem; overflow-y: auto; flex: 1; }
.sup-modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: .6rem;
    padding: .9rem 1.5rem; border-top: 1px solid #f1f5f9; flex-shrink: 0;
    background: #f8fafc; border-radius: 0 0 20px 20px;
}

/* ── Info Grid (view modal) ── */
.sup-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .8rem; }
.sup-info-item { background: #f8fafc; border-radius: 12px; padding: .8rem 1rem; border: 1px solid #f1f5f9; }
.sup-info-item.sup-info-full { grid-column: 1 / -1; }
.sup-info-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #94a3b8; margin-bottom: .3rem; }
.sup-info-value { font-weight: 600; color: #0f172a; font-size: .875rem; line-height: 1.5; }

/* ── Form Inputs ── */
.sup-form-label {
    display: block; font-size: .74rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #475569; margin-bottom: .35rem;
}
.sup-form-input {
    width: 100%; border-radius: 10px; border: 1.5px solid #e2e8f0;
    padding: .5rem .75rem; font-size: .875rem; color: #0f172a;
    background: #f8fafc; outline: none;
    transition: border-color .15s, background .15s, box-shadow .15s;
}
.sup-form-input:focus { border-color: #93c5fd; background: #fff; box-shadow: 0 0 0 3px rgba(147,197,253,.2); }
.sup-form-input.is-invalid { border-color: #f87171; background: #fff; }
.sup-form-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(248,113,113,.18); }
textarea.sup-form-input { resize: vertical; }

/* ── Modal Buttons ── */
.sup-btn {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .45rem 1.1rem; border-radius: 10px;
    font-weight: 600; font-size: .82rem; cursor: pointer;
    border: 1.5px solid transparent;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.sup-btn:active { transform: scale(.95); }

.sup-btn-green   { background: #f0fdf4; border-color: #86efac; color: #15803d; }
.sup-btn-green:hover   { background: #dcfce7; border-color: #4ade80; color: #166534; }
.sup-btn-green:active  { background: #bbf7d0; border-color: #22c55e; }

.sup-btn-blue    { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
.sup-btn-blue:hover    { background: #dbeafe; border-color: #93c5fd; color: #1e3a8a; }
.sup-btn-blue:active   { background: #bfdbfe; border-color: #60a5fa; }

.sup-btn-amber   { background: #fffbeb; border-color: #fde68a; color: #b45309; }
.sup-btn-amber:hover   { background: #fef3c7; border-color: #fcd34d; color: #92400e; }
.sup-btn-amber:active  { background: #fde68a; border-color: #fbbf24; }

.sup-btn-red     { background: #fff1f2; border-color: #fecaca; color: #be123c; }
.sup-btn-red:hover     { background: #ffe4e6; border-color: #f87171; color: #9f1239; }
.sup-btn-red:active    { background: #fecaca; border-color: #ef4444; }

.sup-btn-neutral { background: #f8fafc; border-color: #e2e8f0; color: #475569; }
.sup-btn-neutral:hover  { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
.sup-btn-neutral:active { background: #fecaca; border-color: #ef4444; color: #b91c1c; }
</style>

<script>
function openSupModal(id) {
    document.querySelectorAll('.sup-overlay.open').forEach(function(el) { el.classList.remove('open'); });
    var el = document.getElementById(id);
    if (el) el.classList.add('open');
}
function closeSupModal(id) {
    var el = document.getElementById(id);
    if (el) el.classList.remove('open');
}
document.addEventListener('DOMContentLoaded', function () {
    // Close on backdrop click
    document.querySelectorAll('.sup-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('open');
        });
    });
    // Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.sup-overlay.open').forEach(function(el) { el.classList.remove('open'); });
        }
    });
    // Auto-open add modal on validation error
    @if($errors->any() && old('_method') !== 'PUT')
        openSupModal('addSupplierOverlay');
    @endif
    // Auto-open edit modal on validation error
    @if($errors->any() && old('_method') === 'PUT' && old('supplier_id'))
        openSupModal('editSup-{{ old('supplier_id') }}');
    @endif
});

// Delete confirmation
var deleteSupFormId = null;
function confirmSupDelete(supplierId, supplierName) {
    deleteSupFormId = supplierId;
    document.getElementById('deleteSupName').textContent = supplierName;
    openSupModal('deleteSupOverlay');
}
function submitSupDelete() {
    if (deleteSupFormId !== null) {
        document.getElementById('delSupForm-' + deleteSupFormId).submit();
    }
}
</script>
@endsection
