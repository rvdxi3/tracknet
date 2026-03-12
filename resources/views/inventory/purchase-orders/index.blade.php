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
                                <i class="fas fa-file-invoice me-1"></i> Procurement
                            </span>
                        </div>
                        <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">Purchase Orders</h2>
                        <p class="text-white-50 mb-0" style="font-size:.9rem;">
                            <i class="fas fa-truck me-1"></i> Manage stock replenishment orders to suppliers
                        </p>
                    </div>
                    <button onclick="openPoModal('modal-create')" class="po-hdr-btn">
                        <i class="fas fa-plus me-2"></i> New Purchase Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── PO Table ── --}}
    <div class="po-card">
        <div class="po-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="po-card-icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <div class="po-card-title">Purchase Order List</div>
                    <div class="po-card-sub">{{ $purchaseOrders->total() }} total orders</div>
                </div>
            </div>
        </div>
        <div style="padding:0;">
            <div class="table-responsive">
                <table class="po-table">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Supplier</th>
                            <th>Order Date</th>
                            <th>Expected Delivery</th>
                            <th>Created By</th>
                            <th>Status</th>
                            <th style="width:110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                        @php
                            $statusCfg = [
                                'pending'   => ['cls' => 'po-pill-amber', 'label' => 'Pending'],
                                'approved'  => ['cls' => 'po-pill-blue',  'label' => 'Approved'],
                                'delivered' => ['cls' => 'po-pill-green', 'label' => 'Delivered'],
                                'cancelled' => ['cls' => 'po-pill-red',   'label' => 'Cancelled'],
                            ];
                            $sc = $statusCfg[$po->status] ?? ['cls' => 'po-pill-grey', 'label' => ucfirst($po->status)];
                        @endphp
                        <tr>
                            <td><span class="po-number-pill">{{ $po->po_number }}</span></td>
                            <td class="po-supplier-name">{{ $po->supplier->name ?? '—' }}</td>
                            <td class="po-date">{{ \Carbon\Carbon::parse($po->order_date)->format('M d, Y') }}</td>
                            <td class="po-date">{{ \Carbon\Carbon::parse($po->expected_delivery_date)->format('M d, Y') }}</td>
                            <td class="po-date">{{ $po->user->name ?? '—' }}</td>
                            <td><span class="po-status-pill {{ $sc['cls'] }}">{{ $sc['label'] }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button onclick="openPoModal('modal-show-{{ $po->id }}')" class="po-action-btn po-action-view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($po->status === 'pending')
                                    <button onclick="openPoModal('modal-edit-{{ $po->id }}')" class="po-action-btn po-action-edit" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="po-empty-state">
                                    <div class="po-empty-icon"><i class="fas fa-file-invoice"></i></div>
                                    <div class="po-empty-text">No purchase orders yet</div>
                                    <div class="po-empty-sub">
                                        <button onclick="openPoModal('modal-create')" class="po-empty-link">Create your first purchase order</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($purchaseOrders->hasPages())
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3" style="border-top:1px solid #f1f5f9;">
                <div style="font-size:.78rem;color:#64748b;">
                    Showing <strong>{{ $purchaseOrders->firstItem() }}–{{ $purchaseOrders->lastItem() }}</strong>
                    of <strong>{{ $purchaseOrders->total() }}</strong> orders
                </div>
                <div class="d-flex gap-1 align-items-center">
                    @if($purchaseOrders->onFirstPage())
                        <span class="po-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $purchaseOrders->previousPageUrl() }}" class="po-page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($purchaseOrders->getUrlRange(max(1,$purchaseOrders->currentPage()-2), min($purchaseOrders->lastPage(),$purchaseOrders->currentPage()+2)) as $page => $url)
                        <a href="{{ $url }}" class="po-page-btn {{ $page == $purchaseOrders->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($purchaseOrders->hasMorePages())
                        <a href="{{ $purchaseOrders->nextPageUrl() }}" class="po-page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="po-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ════════════════════════════════════
     CREATE PURCHASE ORDER MODAL
════════════════════════════════════ --}}
<div class="po-overlay" id="modal-create">
    <div class="po-modal po-modal-xl">
        <div class="po-modal-hdr">
            <button type="button" onclick="closePoModal('modal-create')" class="po-modal-close">&times;</button>
            <div class="po-modal-tag"><i class="fas fa-plus me-1"></i> New Order</div>
            <div class="po-modal-title">New Purchase Order</div>
            <div class="po-modal-sub">Create a new stock replenishment order</div>
        </div>

        <form method="POST" action="{{ route('inventory.purchase-orders.store') }}" id="form-create">
            @csrf
            <input type="hidden" name="_form" value="create">

            <div class="po-modal-body">
                @if($errors->any() && old('_form') === 'create')
                <div class="alert alert-danger alert-dismissible small mb-3" role="alert">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="row g-3">
                    <div class="col-lg-5">
                        <div class="po-section-card">
                            <div class="po-section-label"><i class="fas fa-info-circle me-1"></i> Order Details</div>

                            <div class="mb-3">
                                <label class="po-form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="po-form-input @error('supplier_id') po-input-error @enderror" required>
                                    <option value="">— Select Supplier —</option>
                                    @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')<div class="po-error-msg">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="po-form-label">Order Date <span class="text-danger">*</span></label>
                                <input type="date" name="order_date"
                                       class="po-form-input @error('order_date') po-input-error @enderror"
                                       value="{{ old('order_date', date('Y-m-d')) }}" required>
                                @error('order_date')<div class="po-error-msg">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="po-form-label">Expected Delivery <span class="text-danger">*</span></label>
                                <input type="date" name="expected_delivery_date"
                                       class="po-form-input @error('expected_delivery_date') po-input-error @enderror"
                                       value="{{ old('expected_delivery_date') }}" required>
                                @error('expected_delivery_date')<div class="po-error-msg">{{ $message }}</div>@enderror
                            </div>

                            <div>
                                <label class="po-form-label">Notes</label>
                                <textarea name="notes" rows="3"
                                          class="po-form-input @error('notes') po-input-error @enderror"
                                          placeholder="Optional notes...">{{ old('notes') }}</textarea>
                                @error('notes')<div class="po-error-msg">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="po-section-card" style="padding-bottom:.5rem;">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="po-section-label mb-0"><i class="fas fa-boxes me-1"></i> Products to Order</div>
                                <button type="button" id="add-create-row" class="po-add-row-btn">
                                    <i class="fas fa-plus me-1"></i> Add Product
                                </button>
                            </div>
                            @error('products')
                            @if(old('_form') === 'create')
                            <div class="po-error-msg mb-2">{{ $message }}</div>
                            @endif
                            @enderror
                        </div>

                        <div class="table-responsive mt-2" style="border-radius:12px;overflow:hidden;border:1.5px solid #e2e8f0;">
                            <table class="po-items-table">
                                <thead>
                                    <tr>
                                        <th style="min-width:170px;">Product</th>
                                        <th style="width:75px;">Qty</th>
                                        <th style="width:115px;">Unit Price (₱)</th>
                                        <th style="width:95px;" class="text-end">Total</th>
                                        <th style="width:40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="create-rows"></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold pe-2" style="font-size:.8rem;color:#475569;">Grand Total</td>
                                        <td class="text-end fw-bold" id="grand-total-create" style="color:#0f172a;font-size:.875rem;">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="po-modal-footer">
                <button type="button" onclick="closePoModal('modal-create')" class="po-btn po-btn-neutral">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="submit" class="po-btn po-btn-solid">
                    <i class="fas fa-save me-1"></i> Create Purchase Order
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════
     PER-PO SHOW & EDIT MODALS
════════════════════════════════════ --}}
@foreach($purchaseOrders as $po)
@php
    $scFull = [
        'pending'   => ['cls' => 'po-pill-amber', 'label' => 'Pending'],
        'approved'  => ['cls' => 'po-pill-blue',  'label' => 'Approved'],
        'delivered' => ['cls' => 'po-pill-green', 'label' => 'Delivered'],
        'cancelled' => ['cls' => 'po-pill-red',   'label' => 'Cancelled'],
    ];
    $sc2 = $scFull[$po->status] ?? ['cls' => 'po-pill-grey', 'label' => ucfirst($po->status)];
@endphp

{{-- ── SHOW MODAL ── --}}
<div class="po-overlay" id="modal-show-{{ $po->id }}">
    <div class="po-modal" style="max-width:660px;">
        <div class="po-modal-hdr">
            <button type="button" onclick="closePoModal('modal-show-{{ $po->id }}')" class="po-modal-close">&times;</button>
            <div class="po-modal-tag"><i class="fas fa-file-invoice me-1"></i> Purchase Order</div>
            <div class="po-modal-title">{{ $po->po_number }}</div>
            <div class="po-modal-sub">
                {{ $po->supplier->name ?? '—' }}
                &nbsp;&middot;&nbsp;
                <span class="po-status-pill {{ $sc2['cls'] }}" style="font-size:.62rem;padding:.12rem .5rem;">{{ $sc2['label'] }}</span>
            </div>
        </div>

        <div class="po-modal-body">
            {{-- Order Info --}}
            <div class="po-detail-section mb-3">
                <div class="po-detail-section-label"><i class="fas fa-info-circle me-1"></i> Order Info</div>
                <div class="po-detail-row">
                    <span class="po-detail-key">Status</span>
                    <span class="po-status-pill {{ $sc2['cls'] }}">{{ $sc2['label'] }}</span>
                </div>
                <div class="po-detail-row">
                    <span class="po-detail-key">Order Date</span>
                    <span class="po-detail-val">{{ \Carbon\Carbon::parse($po->order_date)->format('M d, Y') }}</span>
                </div>
                <div class="po-detail-row">
                    <span class="po-detail-key">Expected Delivery</span>
                    <span class="po-detail-val">{{ \Carbon\Carbon::parse($po->expected_delivery_date)->format('M d, Y') }}</span>
                </div>
                <div class="po-detail-row" style="border-bottom:none;">
                    <span class="po-detail-key">Created By</span>
                    <span class="po-detail-val">{{ $po->user->name ?? '—' }}</span>
                </div>
            </div>

            {{-- Supplier --}}
            <div class="po-detail-section mb-3">
                <div class="po-detail-section-label"><i class="fas fa-building me-1"></i> Supplier</div>
                <div class="po-supplier-block">
                    <div class="po-supplier-avatar">{{ strtoupper(substr($po->supplier->name ?? 'S', 0, 1)) }}</div>
                    <div>
                        <div class="po-supplier-name-lg">{{ $po->supplier->name ?? '—' }}</div>
                        @if($po->supplier->contact_person ?? null)
                        <div class="po-supplier-info"><i class="fas fa-user fa-fw me-1"></i>{{ $po->supplier->contact_person }}</div>
                        @endif
                        @if($po->supplier->phone ?? null)
                        <div class="po-supplier-info"><i class="fas fa-phone fa-fw me-1"></i>{{ $po->supplier->phone }}</div>
                        @endif
                        @if($po->supplier->email ?? null)
                        <div class="po-supplier-info"><i class="fas fa-envelope fa-fw me-1"></i>{{ $po->supplier->email }}</div>
                        @endif
                    </div>
                </div>
            </div>

            @if($po->notes)
            <div class="po-detail-section mb-3">
                <div class="po-detail-section-label"><i class="fas fa-sticky-note me-1"></i> Notes</div>
                <p class="mb-0" style="font-size:.855rem;color:#475569;">{{ $po->notes }}</p>
            </div>
            @endif

            {{-- Items Table --}}
            <div class="po-detail-section">
                <div class="po-detail-section-label"><i class="fas fa-boxes me-1"></i> Ordered Items</div>
                <div class="table-responsive mt-2" style="border-radius:10px;overflow:hidden;border:1.5px solid #e2e8f0;">
                    <table class="po-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center" style="width:60px;">Qty</th>
                                <th class="text-end" style="width:110px;">Unit Price</th>
                                <th class="text-end" style="width:110px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($po->items as $item)
                            <tr>
                                <td style="font-size:.855rem;font-weight:600;">{{ $item->product->name ?? '—' }}</td>
                                <td class="text-center" style="font-size:.855rem;">{{ $item->quantity }}</td>
                                <td class="text-end" style="font-size:.855rem;">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end fw-bold" style="font-size:.855rem;">₱{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold pe-2" style="font-size:.8rem;color:#475569;">Grand Total</td>
                                <td class="text-end fw-bold" style="color:#0f172a;">₱{{ number_format($po->items->sum('total_price'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @if($po->status === 'delivered')
                <div class="po-status-notice success mt-2">
                    <i class="fas fa-check-circle me-1"></i> Inventory stock was updated when this order was received.
                </div>
                @elseif($po->status === 'cancelled')
                <div class="po-status-notice danger mt-2">
                    <i class="fas fa-times-circle me-1"></i> This purchase order has been cancelled.
                </div>
                @endif
            </div>
        </div>

        <div class="po-modal-footer">
            @if($po->status === 'pending')
                <form action="{{ route('inventory.purchase-orders.destroy', $po) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Permanently delete this purchase order?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="po-btn po-btn-danger-sm"><i class="fas fa-trash me-1"></i> Delete</button>
                </form>
                <form action="{{ route('inventory.purchase-orders.cancel', $po) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Cancel this purchase order?')">
                    @csrf
                    <button type="submit" class="po-btn po-btn-outline-danger"><i class="fas fa-times me-1"></i> Cancel Order</button>
                </form>
                <form action="{{ route('inventory.purchase-orders.approve', $po) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="po-btn po-btn-outline-green"><i class="fas fa-check me-1"></i> Approve</button>
                </form>
                <button type="button" onclick="closePoModal('modal-show-{{ $po->id }}'); openPoModal('modal-edit-{{ $po->id }}')" class="po-btn po-btn-outline">
                    <i class="fas fa-pen me-1"></i> Edit
                </button>
            @elseif($po->status === 'approved')
                <form action="{{ route('inventory.purchase-orders.cancel', $po) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Cancel this purchase order?')">
                    @csrf
                    <button type="submit" class="po-btn po-btn-outline-danger"><i class="fas fa-times me-1"></i> Cancel</button>
                </form>
                <form action="{{ route('inventory.purchase-orders.receive', $po) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Mark as received? This will update inventory stock.')">
                    @csrf
                    <button type="submit" class="po-btn po-btn-solid"><i class="fas fa-box-open me-1"></i> Mark Received</button>
                </form>
            @else
                <button type="button" onclick="closePoModal('modal-show-{{ $po->id }}')" class="po-btn po-btn-neutral">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            @endif
        </div>
    </div>
</div>

{{-- ── EDIT MODAL (pending only) ── --}}
@if($po->status === 'pending')
@php $isEditTarget = old('_form') === 'edit-'.$po->id; @endphp
<div class="po-overlay" id="modal-edit-{{ $po->id }}">
    <div class="po-modal po-modal-xl">
        <div class="po-modal-hdr">
            <button type="button" onclick="closePoModal('modal-edit-{{ $po->id }}')" class="po-modal-close">&times;</button>
            <div class="po-modal-tag"><i class="fas fa-pen me-1"></i> Edit Order</div>
            <div class="po-modal-title">{{ $po->po_number }}</div>
            <div class="po-modal-sub">Update purchase order details and items</div>
        </div>

        <form method="POST" action="{{ route('inventory.purchase-orders.update', $po) }}" id="form-edit-{{ $po->id }}">
            @csrf @method('PUT')
            <input type="hidden" name="_form" value="edit-{{ $po->id }}">

            <div class="po-modal-body">
                @if($errors->any() && $isEditTarget)
                <div class="alert alert-danger alert-dismissible small mb-3" role="alert">
                    <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="row g-3">
                    <div class="col-lg-5">
                        <div class="po-section-card">
                            <div class="po-section-label"><i class="fas fa-info-circle me-1"></i> Order Details</div>

                            <div class="mb-3">
                                <label class="po-form-label">Supplier <span class="text-danger">*</span></label>
                                <select name="supplier_id" class="po-form-input" required>
                                    <option value="">— Select Supplier —</option>
                                    @foreach($suppliers as $s)
                                    @php $selVal = $isEditTarget ? old('supplier_id', $po->supplier_id) : $po->supplier_id; @endphp
                                    <option value="{{ $s->id }}" {{ $selVal == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="po-form-label">Order Date <span class="text-danger">*</span></label>
                                <input type="date" name="order_date" class="po-form-input"
                                       value="{{ $isEditTarget ? old('order_date', $po->order_date) : $po->order_date }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="po-form-label">Expected Delivery <span class="text-danger">*</span></label>
                                <input type="date" name="expected_delivery_date" class="po-form-input"
                                       value="{{ $isEditTarget ? old('expected_delivery_date', $po->expected_delivery_date) : $po->expected_delivery_date }}" required>
                            </div>

                            <div>
                                <label class="po-form-label">Notes</label>
                                <textarea name="notes" rows="3" class="po-form-input"
                                          placeholder="Optional notes...">{{ $isEditTarget ? old('notes', $po->notes) : $po->notes }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="po-section-card" style="padding-bottom:.5rem;">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="po-section-label mb-0"><i class="fas fa-boxes me-1"></i> Products to Order</div>
                                <button type="button" id="add-edit-row-{{ $po->id }}" class="po-add-row-btn">
                                    <i class="fas fa-plus me-1"></i> Add Product
                                </button>
                            </div>
                            @error('products')
                            @if($isEditTarget)
                            <div class="po-error-msg mb-2">{{ $message }}</div>
                            @endif
                            @enderror
                        </div>

                        <div class="table-responsive mt-2" style="border-radius:12px;overflow:hidden;border:1.5px solid #e2e8f0;">
                            <table class="po-items-table">
                                <thead>
                                    <tr>
                                        <th style="min-width:170px;">Product</th>
                                        <th style="width:75px;">Qty</th>
                                        <th style="width:115px;">Unit Price (₱)</th>
                                        <th style="width:95px;" class="text-end">Total</th>
                                        <th style="width:40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="edit-rows-{{ $po->id }}"></tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold pe-2" style="font-size:.8rem;color:#475569;">Grand Total</td>
                                        <td class="text-end fw-bold" id="grand-total-edit-{{ $po->id }}" style="color:#0f172a;font-size:.875rem;">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="po-modal-footer">
                <button type="button" onclick="closePoModal('modal-edit-{{ $po->id }}')" class="po-btn po-btn-neutral">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="submit" class="po-btn po-btn-solid">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endforeach

<style>
.container-fluid { background: #f8fafd; min-height: 100vh; }

/* ── Header Button ── */
.po-hdr-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.28);
    color: #fff; padding: .6rem 1.3rem; border-radius: 12px;
    font-weight: 600; font-size: .88rem; cursor: pointer; text-decoration: none;
    transition: background .15s, border-color .15s, transform .12s;
    backdrop-filter: blur(4px);
}
.po-hdr-btn:hover { background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.45); transform: translateY(-1px); color: #fff; }
.po-hdr-btn:active { transform: scale(.95); }

/* ── Card ── */
.po-card {
    background: #fff; border-radius: 20px;
    box-shadow: 0 4px 20px rgba(13,20,40,.07);
    border: 1.5px solid #f1f5f9; overflow: hidden; margin-bottom: 1.5rem;
}
.po-card-header {
    padding: 1rem 1.4rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.po-card-icon {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .9rem;
    background: linear-gradient(135deg,#2563eb1a,#1e3a8a1a); color: #2563eb;
}
.po-card-title { font-weight: 700; font-size: .9rem; color: #0f172a; }
.po-card-sub   { font-size: .72rem; color: #94a3b8; }

/* ── Table ── */
.po-table { width: 100%; border-collapse: collapse; font-size: .865rem; }
.po-table thead tr { background: linear-gradient(135deg,#0f172a,#1e3a8a); }
.po-table th {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #fff; padding: .7rem 1rem; border: none;
}
.po-table tbody tr { transition: background .12s; }
.po-table tbody tr:nth-child(even) { background: #f8fafc; }
.po-table tbody tr:nth-child(odd)  { background: #fff; }
.po-table tbody tr:hover { background: #eff6ff; }
.po-table td { padding: .7rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.po-table tbody tr:last-child td { border-bottom: none; }

/* ── PO Number Pill ── */
.po-number-pill {
    font-family: monospace; font-size: .8rem; font-weight: 700;
    background: #f1f5f9; color: #1e40af;
    padding: .2rem .65rem; border-radius: 6px; border: 1px solid #dbeafe;
    letter-spacing: .04em;
}
.po-supplier-name { font-weight: 600; color: #0f172a; }
.po-date          { font-size: .835rem; color: #64748b; }

/* ── Status Pills ── */
.po-status-pill {
    display: inline-flex; align-items: center;
    padding: .22rem .7rem; border-radius: 20px;
    font-size: .7rem; font-weight: 700; white-space: nowrap;
}
.po-pill-amber  { background: #fef3c7; color: #92400e; }
.po-pill-blue   { background: #dbeafe; color: #1e40af; }
.po-pill-green  { background: #d1fae5; color: #065f46; }
.po-pill-red    { background: #fee2e2; color: #991b1b; }
.po-pill-grey   { background: #f1f5f9; color: #475569; }

/* ── Action Buttons ── */
.po-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 8px;
    border: 1.5px solid; font-size: .78rem; cursor: pointer;
    background: transparent;
    transition: background .15s, border-color .15s, transform .12s;
}
.po-action-btn:hover  { transform: translateY(-1px); }
.po-action-btn:active { transform: scale(.91); }
.po-action-view       { border-color: #bfdbfe; color: #2563eb; }
.po-action-view:hover { background: #eff6ff; border-color: #93c5fd; }
.po-action-edit       { border-color: #bbf7d0; color: #059669; }
.po-action-edit:hover { background: #ecfdf5; border-color: #6ee7b7; }

/* ── Pagination ── */
.po-page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; border-radius: 8px; padding: 0 .5rem;
    font-size: .8rem; font-weight: 600;
    background: #fff; border: 1.5px solid #e2e8f0; color: #374151;
    text-decoration: none;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.po-page-btn:hover:not(.disabled):not(.active) { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; transform: translateY(-1px); text-decoration: none; }
.po-page-btn.active   { background: #2563eb; border-color: #2563eb; color: #fff; }
.po-page-btn.disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }

/* ── Empty State ── */
.po-empty-state { text-align: center; padding: 3rem 1rem; }
.po-empty-icon {
    width: 56px; height: 56px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.4rem; margin-bottom: .75rem;
    background: #dbeafe; color: #2563eb;
}
.po-empty-text { font-weight: 700; color: #374151; font-size: .95rem; }
.po-empty-sub  { font-size: .8rem; color: #94a3b8; margin-top: .3rem; }
.po-empty-link { background: none; border: none; color: #2563eb; font-weight: 600; font-size: .8rem; cursor: pointer; text-decoration: underline; padding: 0; }

/* ── Modal Overlay ── */
.po-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,.55); backdrop-filter: blur(3px);
    z-index: 1055; align-items: center; justify-content: center; padding: 1rem;
}
.po-overlay.open { display: flex; }

.po-modal {
    background: #fff; border-radius: 20px;
    box-shadow: 0 20px 60px rgba(15,23,42,.25);
    width: 100%; max-width: 660px; max-height: 90vh;
    overflow: hidden; display: flex; flex-direction: column;
    animation: poModalIn .2s ease; margin: auto;
}
.po-modal-xl { max-width: 860px; }
@keyframes poModalIn { from { opacity:0; transform:translateY(-16px) scale(.97); } to { opacity:1; transform:none; } }

/* ── Modal Header ── */
.po-modal-hdr {
    background: linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#2563eb 100%);
    padding: 1.5rem 1.5rem 1.3rem; position: relative;
    border-radius: 20px 20px 0 0; flex-shrink: 0; overflow: hidden;
}
.po-modal-hdr::before {
    content: ''; position: absolute; top: -30px; right: -30px;
    width: 130px; height: 130px; border-radius: 50%;
    background: rgba(255,255,255,.05); pointer-events: none;
}
.po-modal-hdr::after {
    content: ''; position: absolute; bottom: -40px; left: 30%;
    width: 160px; height: 160px; border-radius: 50%;
    background: rgba(255,255,255,.03); pointer-events: none;
}
.po-modal-tag   { font-size:.65rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:rgba(255,255,255,.55); margin-bottom:.5rem; position:relative; z-index:1; }
.po-modal-title { font-size:1.4rem; font-weight:800; color:#fff; line-height:1.2; margin-bottom:.3rem; position:relative; z-index:1; }
.po-modal-sub   { font-size:.82rem; color:rgba(255,255,255,.55); position:relative; z-index:1; display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
.po-modal-close {
    position: absolute; top: 1rem; right: 1rem; z-index: 2;
    width: 34px; height: 34px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; line-height: 1; cursor: pointer;
    background: rgba(255,255,255,.15); border: 1.5px solid rgba(255,255,255,.25); color: #fff;
    transition: background .15s, border-color .15s, transform .12s;
}
.po-modal-close:hover  { background: rgba(239,68,68,.55); border-color: rgba(239,68,68,.75); }
.po-modal-close:active { background: rgba(239,68,68,.75); border-color: #ef4444; transform: scale(.88); }

.po-modal-body {
    padding: 1.2rem 1.4rem; overflow-y: auto; flex: 1;
}
.po-modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: .6rem;
    padding: .9rem 1.4rem; border-top: 1px solid #f1f5f9; flex-shrink: 0;
    background: #f8fafc;
}

/* ── Show Modal Detail Sections ── */
.po-detail-section {
    background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 1rem 1.1rem;
}
.po-detail-section-label {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: #94a3b8; margin-bottom: .65rem;
}
.po-detail-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: .42rem 0; border-bottom: 1px solid #e2e8f0; font-size: .855rem;
}
.po-detail-key { color: #64748b; font-weight: 500; }
.po-detail-val { font-weight: 600; color: #0f172a; }

.po-supplier-block  { display: flex; align-items: flex-start; gap: .75rem; }
.po-supplier-avatar {
    width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; font-weight: 800; color: #fff;
    background: linear-gradient(135deg,#2563eb,#1e3a8a);
}
.po-supplier-name-lg { font-weight: 700; color: #0f172a; font-size: .9rem; margin-bottom: .25rem; }
.po-supplier-info    { font-size: .8rem; color: #64748b; margin-bottom: .18rem; }

.po-status-notice { padding: .5rem .85rem; border-radius: 8px; font-size: .8rem; font-weight: 600; }
.po-status-notice.success { background: #d1fae5; color: #065f46; }
.po-status-notice.danger  { background: #fee2e2; color: #991b1b; }

/* ── Section Card (create/edit modals) ── */
.po-section-card {
    background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 14px;
    padding: 1rem 1.1rem; margin-bottom: .5rem;
}
.po-section-label {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .08em; color: #94a3b8; margin-bottom: .85rem;
}

/* ── Form Inputs ── */
.po-form-label {
    display: block; font-size: .74rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #475569; margin-bottom: .35rem;
}
.po-form-input {
    width: 100%; border-radius: 10px; border: 1.5px solid #e2e8f0;
    padding: .5rem .75rem; font-size: .875rem; color: #0f172a;
    background: #fff; outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.po-form-input:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(147,197,253,.2); }
textarea.po-form-input { resize: vertical; min-height: 72px; }
.po-input-error { border-color: #fca5a5 !important; }
.po-error-msg   { font-size: .78rem; color: #dc2626; margin-top: .3rem; }

.po-input-group   { position: relative; display: flex; align-items: center; }
.po-input-prefix  { position: absolute; left: .75rem; font-weight: 700; color: #94a3b8; font-size: .875rem; pointer-events: none; }

/* ── Add Row Button ── */
.po-add-row-btn {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .32rem .8rem; border-radius: 8px;
    background: #eff6ff; border: 1.5px solid #bfdbfe; color: #1e40af;
    font-size: .76rem; font-weight: 600; cursor: pointer;
    transition: background .15s, border-color .15s;
}
.po-add-row-btn:hover { background: #dbeafe; border-color: #93c5fd; }

/* ── Items Table (inside modals) ── */
.po-items-table { width: 100%; border-collapse: collapse; font-size: .855rem; }
.po-items-table thead tr { background: linear-gradient(135deg,#1e3a8a,#2563eb); }
.po-items-table th {
    font-size: .65rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #fff; padding: .55rem .85rem; border: none;
}
.po-items-table td { padding: .52rem .85rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.po-items-table tbody tr:last-child td { border-bottom: none; }
.po-items-table tfoot td { padding: .6rem .85rem; background: #f8fafc; font-size: .82rem; border-top: 1.5px solid #e2e8f0; }

/* ── Remove Row Button ── */
.po-remove-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 6px;
    background: #fee2e2; border: 1.5px solid #fca5a5; color: #dc2626;
    font-size: .7rem; cursor: pointer;
    transition: background .15s, border-color .15s, transform .12s;
}
.po-remove-btn:hover  { background: #fecaca; border-color: #ef4444; }
.po-remove-btn:active { transform: scale(.88); }

/* ── Modal Buttons ── */
.po-btn {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .45rem 1rem; border-radius: 10px;
    font-weight: 600; font-size: .82rem; cursor: pointer;
    border: 1.5px solid transparent;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.po-btn:active { transform: scale(.95); }
.po-btn-neutral       { background: #f8fafc; border-color: #e2e8f0; color: #475569; }
.po-btn-neutral:hover { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
.po-btn-outline       { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
.po-btn-outline:hover { background: #dbeafe; border-color: #93c5fd; }
.po-btn-outline-danger       { background: #fff1f2; border-color: #fecaca; color: #dc2626; }
.po-btn-outline-danger:hover { background: #fee2e2; border-color: #fca5a5; }
.po-btn-outline-green       { background: #f0fdf4; border-color: #bbf7d0; color: #16a34a; }
.po-btn-outline-green:hover { background: #dcfce7; border-color: #86efac; }
.po-btn-danger-sm       { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
.po-btn-danger-sm:hover { background: #fecaca; border-color: #ef4444; }
.po-btn-solid {
    background: linear-gradient(135deg,#2563eb,#1e40af);
    border-color: transparent; color: #fff;
    box-shadow: 0 2px 8px rgba(37,99,235,.35);
}
.po-btn-solid:hover  { background: linear-gradient(135deg,#1d4ed8,#1e3a8a); box-shadow: 0 4px 14px rgba(37,99,235,.45); transform: translateY(-1px); }
.po-btn-solid:active { transform: scale(.96); box-shadow: none; }
</style>

<script>
const poProducts = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name]));
const poRowCounters = {};

function addPoRow(tbodyId, ns, selectedId, qty, price) {
    selectedId = selectedId ?? '';
    qty        = qty        ?? 1;
    price      = price      ?? '';
    if (!poRowCounters[ns]) poRowCounters[ns] = 0;
    const i = poRowCounters[ns]++;
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;

    const options = poProducts.map(p =>
        `<option value="${p.id}" ${String(p.id) === String(selectedId) ? 'selected' : ''}>${p.name}</option>`
    ).join('');

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td style="padding:.45rem .85rem;">
            <select name="products[${i}][id]" class="po-form-input" style="padding:.38rem .6rem;font-size:.82rem;" required>
                <option value="">— Select —</option>${options}
            </select>
        </td>
        <td style="padding:.45rem .6rem;">
            <input type="number" name="products[${i}][quantity]" class="po-form-input po-qty-inp"
                   style="padding:.38rem .55rem;font-size:.82rem;" value="${qty}" min="1" required>
        </td>
        <td style="padding:.45rem .6rem;">
            <div class="po-input-group">
                <span class="po-input-prefix">$</span>
                <input type="number" name="products[${i}][unit_price]" class="po-form-input po-price-inp"
                       style="padding:.38rem .55rem .38rem 1.5rem;font-size:.82rem;"
                       value="${price}" min="0" step="0.01" required placeholder="0.00">
            </div>
        </td>
        <td class="text-end fw-semibold po-row-total" style="padding:.45rem .85rem;font-size:.82rem;">$0.00</td>
        <td style="padding:.45rem .5rem;text-align:center;">
            <button type="button" class="po-remove-btn"><i class="fas fa-trash"></i></button>
        </td>
    `;

    const qtyEl   = tr.querySelector('.po-qty-inp');
    const priceEl = tr.querySelector('.po-price-inp');
    const totEl   = tr.querySelector('.po-row-total');

    function recalc() {
        const q = parseFloat(qtyEl.value)   || 0;
        const p = parseFloat(priceEl.value) || 0;
        totEl.textContent = '₱' + (q * p).toFixed(2);
        recalcGrand(tbodyId, ns);
    }
    qtyEl.addEventListener('input',   recalc);
    priceEl.addEventListener('input', recalc);
    tr.querySelector('.po-remove-btn').addEventListener('click', () => { tr.remove(); recalcGrand(tbodyId, ns); });

    tbody.appendChild(tr);
    if (qty && price) recalc();
    recalcGrand(tbodyId, ns);
}

function recalcGrand(tbodyId, ns) {
    const tbody = document.getElementById(tbodyId);
    if (!tbody) return;
    let total = 0;
    tbody.querySelectorAll('.po-row-total').forEach(el => {
        total += parseFloat(el.textContent.replace('₱', '')) || 0;
    });
    const gt = document.getElementById('grand-total-' + ns);
    if (gt) gt.textContent = '₱' + total.toFixed(2);
}

// ── Create modal rows ──
@if(old('_form') === 'create' && old('products'))
    @foreach(old('products') as $p)
        addPoRow('create-rows', 'create', '{{ $p['id'] ?? '' }}', '{{ $p['quantity'] ?? 1 }}', '{{ $p['unit_price'] ?? '' }}');
    @endforeach
@else
    addPoRow('create-rows', 'create');
@endif
document.getElementById('add-create-row')?.addEventListener('click', () => addPoRow('create-rows', 'create'));

// ── Edit modal rows ──
@foreach($purchaseOrders as $po)
@if($po->status === 'pending')
@if(old('_form') === 'edit-'.$po->id && old('products'))
    @foreach(old('products') as $p)
        addPoRow('edit-rows-{{ $po->id }}', 'edit-{{ $po->id }}', '{{ $p['id'] ?? '' }}', '{{ $p['quantity'] ?? 1 }}', '{{ $p['unit_price'] ?? '' }}');
    @endforeach
@else
    @foreach($po->items as $item)
        addPoRow('edit-rows-{{ $po->id }}', 'edit-{{ $po->id }}', {{ $item->product_id }}, {{ $item->quantity }}, {{ $item->unit_price }});
    @endforeach
@endif
document.getElementById('add-edit-row-{{ $po->id }}')?.addEventListener('click', () => addPoRow('edit-rows-{{ $po->id }}', 'edit-{{ $po->id }}'));
@endif
@endforeach

// ── Modal open / close ──
function openPoModal(id) {
    document.querySelectorAll('.po-overlay.open').forEach(el => el.classList.remove('open'));
    const el = document.getElementById(id);
    if (el) el.classList.add('open');
}
function closePoModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
}

document.addEventListener('DOMContentLoaded', function () {
    // Close on backdrop click
    document.querySelectorAll('.po-overlay').forEach(overlay => {
        overlay.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('open'); });
    });
    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') document.querySelectorAll('.po-overlay.open').forEach(el => el.classList.remove('open'));
    });
    // Re-open modal after validation error
    @if(old('_form'))
        openPoModal('modal-{{ old('_form') }}');
    @endif
});
</script>
@endsection
