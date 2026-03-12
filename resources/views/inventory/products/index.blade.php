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
                                <i class="fas fa-warehouse me-1"></i> Inventory
                            </span>
                        </div>
                        <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">Products</h2>
                        <p class="text-white-50 mb-0" style="font-size:.9rem;">
                            <i class="fas fa-boxes me-1"></i> Manage your product catalog
                        </p>
                    </div>
                    <button onclick="openProdModal('addProductOverlay')" class="prod-hdr-btn">
                        <i class="fas fa-plus me-2"></i> Add Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Alert Messages ── --}}

    {{-- ── Products Table ── --}}
    <div class="prod-card">
        <div class="prod-card-header">
            <div class="d-flex align-items-center gap-2">
                <div class="prod-card-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <div class="prod-card-title">Product Catalog</div>
                    <div class="prod-card-sub">{{ $products->total() }} total products</div>
                </div>
            </div>
        </div>
        <div style="padding:0;">
            <div class="table-responsive">
                <table class="prod-table">
                    <thead>
                        <tr>
                            <th style="width:68px;">Image</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                <img src="{{ $product->image_url }}"
                                     alt="{{ $product->name }}" class="prod-thumb">
                            </td>
                            <td>
                                <div class="prod-name">{{ $product->name }}</div>
                                <div class="prod-desc-snippet">{{ \Illuminate\Support\Str::limit($product->description, 55) }}</div>
                            </td>
                            <td><span class="prod-sku-pill">{{ $product->sku }}</span></td>
                            <td><span class="prod-cat-pill">{{ $product->category->name ?? '—' }}</span></td>
                            <td><span class="prod-price">₱{{ number_format($product->price, 2) }}</span></td>
                            <td>
                                @php $qty = $product->inventory->quantity ?? 0; $isLow = $product->inventory && $qty <= $product->inventory->low_stock_threshold; @endphp
                                <span class="prod-stock {{ $isLow ? 'low' : '' }}">{{ $qty }}</span>
                                @if($isLow)
                                    <span class="prod-low-badge">Low</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_featured)
                                    <span class="prod-status-pill featured">Featured</span>
                                @else
                                    <span class="prod-status-pill regular">Regular</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button onclick="openProdModal('viewProd-{{ $product->id }}')" class="prod-act-btn view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="openProdModal('editProd-{{ $product->id }}')" class="prod-act-btn edit" title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('inventory.products.destroy', $product) }}" method="POST" class="d-inline" id="delForm-{{ $product->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $product->id }}, '{{ addslashes($product->name) }}')" class="prod-act-btn del" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="prod-empty-state">
                                    <div class="prod-empty-icon"><i class="fas fa-boxes"></i></div>
                                    <div class="prod-empty-text">No products found</div>
                                    <div class="prod-empty-sub">Click "Add Product" to create your first product</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3" style="border-top:1px solid #f1f5f9;">
                <div style="font-size:.78rem;color:#64748b;">
                    Showing <strong>{{ $products->firstItem() }}–{{ $products->lastItem() }}</strong>
                    of <strong>{{ $products->total() }}</strong> products
                </div>
                <div class="d-flex gap-1 align-items-center">
                    @if($products->onFirstPage())
                        <span class="prod-page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" class="prod-page-btn"><i class="fas fa-chevron-left"></i></a>
                    @endif
                    @foreach($products->getUrlRange(max(1, $products->currentPage()-2), min($products->lastPage(), $products->currentPage()+2)) as $page => $url)
                        <a href="{{ $url }}" class="prod-page-btn {{ $page == $products->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                    @endforeach
                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" class="prod-page-btn"><i class="fas fa-chevron-right"></i></a>
                    @else
                        <span class="prod-page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════
     ADD PRODUCT MODAL
══════════════════════════════════════════ --}}
<div class="prod-overlay" id="addProductOverlay">
    <div class="prod-modal" style="max-width:680px;">
        <div class="prod-modal-hdr">
            <button type="button" onclick="closeProdModal('addProductOverlay')" class="prod-modal-close">&times;</button>
            <div class="prod-modal-tag"><i class="fas fa-plus me-1"></i> New Product</div>
            <div class="prod-modal-title">Add New Product</div>
            <div class="prod-modal-sub">Fill in the product details below</div>
        </div>
        <div class="prod-modal-body">
            <form id="addProdForm" method="POST" action="{{ route('inventory.products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="prod-form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="prod-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('name')) is-invalid @endif"
                               value="{{ old('_method') !== 'PUT' ? old('name') : '' }}"
                               placeholder="e.g. Wireless Mouse" required>
                        @if($errors->has('name') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="prod-form-label">SKU <span class="text-danger">*</span></label>
                        <input type="text" name="sku"
                               class="prod-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('sku')) is-invalid @endif"
                               value="{{ old('_method') !== 'PUT' ? old('sku') : '' }}"
                               placeholder="e.g. WM-001" required>
                        @if($errors->has('sku') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('sku') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="prod-form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id"
                                class="prod-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('category_id')) is-invalid @endif"
                                required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('_method') !== 'PUT' && old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('category_id') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('category_id') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="prod-form-label">Price <span class="text-danger">*</span></label>
                        <div class="prod-input-group">
                            <span class="prod-input-prefix">$</span>
                            <input type="number" step="0.01" name="price"
                                   class="prod-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('price')) is-invalid @endif"
                                   value="{{ old('_method') !== 'PUT' ? old('price') : '' }}"
                                   placeholder="0.00" min="0" required>
                        </div>
                        @if($errors->has('price') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('price') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="prod-form-label">Initial Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="initial_quantity"
                               class="prod-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('initial_quantity')) is-invalid @endif"
                               value="{{ old('_method') !== 'PUT' ? old('initial_quantity', 0) : 0 }}"
                               min="0" required>
                        @if($errors->has('initial_quantity') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('initial_quantity') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="prod-form-label">Low Stock Threshold <span class="text-danger">*</span></label>
                        <input type="number" name="low_stock_threshold"
                               class="prod-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('low_stock_threshold')) is-invalid @endif"
                               value="{{ old('_method') !== 'PUT' ? old('low_stock_threshold', 5) : 5 }}"
                               min="0" required>
                        @if($errors->has('low_stock_threshold') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('low_stock_threshold') }}</div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="prod-form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" rows="3"
                                  class="prod-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('description')) is-invalid @endif"
                                  placeholder="Product description...">{{ old('_method') !== 'PUT' ? old('description') : '' }}</textarea>
                        @if($errors->has('description') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <label class="prod-form-label">Product Image</label>
                        <input type="file" name="image"
                               class="prod-form-input @if($errors->any() && old('_method') !== 'PUT' && $errors->has('image')) is-invalid @endif"
                               accept="image/jpeg,image/png,image/jpg,image/gif">
                        <div style="font-size:.73rem;color:#94a3b8;margin-top:.25rem;">JPEG, PNG, JPG, GIF — max 2MB</div>
                        @if($errors->has('image') && old('_method') !== 'PUT')
                            <div class="invalid-feedback">{{ $errors->first('image') }}</div>
                        @endif
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <label class="prod-check-label" style="margin-top:1.2rem;">
                            <input type="checkbox" name="is_featured" value="1"
                                   {{ old('_method') !== 'PUT' && old('is_featured') ? 'checked' : '' }}>
                            <span>Featured Product</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="prod-modal-footer">
            <button type="button" onclick="closeProdModal('addProductOverlay')" class="prod-btn prod-btn-neutral">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button type="submit" form="addProdForm" class="prod-btn prod-btn-green">
                <i class="fas fa-save me-1"></i> Save Product
            </button>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     VIEW PRODUCT MODALS (per row)
══════════════════════════════════════════ --}}
@foreach($products as $product)
<div class="prod-overlay" id="viewProd-{{ $product->id }}">
    <div class="prod-modal" style="max-width:580px;">
        <div class="prod-modal-hdr">
            <button type="button" onclick="closeProdModal('viewProd-{{ $product->id }}')" class="prod-modal-close">&times;</button>
            <div class="prod-modal-tag"><i class="fas fa-eye me-1"></i> Product Details</div>
            <div class="prod-modal-title">{{ $product->name }}</div>
            <div class="prod-modal-sub">#{{ $product->sku }} &middot; {{ $product->category->name ?? '—' }}</div>
        </div>
        <div class="prod-modal-body">
            <div class="d-flex gap-4 mb-4">
                <img src="{{ $product->image_url }}"
                     alt="{{ $product->name }}" class="prod-view-img">
                <div class="flex-fill">
                    <h5 class="fw-bold mb-2" style="color:#0f172a;font-size:1.05rem;">{{ $product->name }}</h5>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if($product->is_featured)
                            <span class="prod-status-pill featured">Featured</span>
                        @else
                            <span class="prod-status-pill regular">Regular</span>
                        @endif
                        <span class="prod-cat-pill">{{ $product->category->name ?? '—' }}</span>
                    </div>
                    <div class="prod-price" style="font-size:1.6rem;letter-spacing:-.01em;">₱{{ number_format($product->price, 2) }}</div>
                </div>
            </div>
            <div class="prod-info-grid mb-3">
                <div class="prod-info-item">
                    <div class="prod-info-label">SKU</div>
                    <div class="prod-info-value"><span class="prod-sku-pill">{{ $product->sku }}</span></div>
                </div>
                <div class="prod-info-item">
                    <div class="prod-info-label">Current Stock</div>
                    <div class="prod-info-value">
                        @php $vQty = $product->inventory->quantity ?? 0; $vLow = $product->inventory && $vQty <= $product->inventory->low_stock_threshold; @endphp
                        <span class="prod-stock {{ $vLow ? 'low' : '' }}">{{ $vQty }} units</span>
                        @if($vLow)<span class="prod-low-badge">Low</span>@endif
                    </div>
                </div>
                <div class="prod-info-item">
                    <div class="prod-info-label">Low Stock Threshold</div>
                    <div class="prod-info-value">{{ $product->inventory->low_stock_threshold ?? '—' }} units</div>
                </div>
                <div class="prod-info-item">
                    <div class="prod-info-label">Category</div>
                    <div class="prod-info-value">{{ $product->category->name ?? '—' }}</div>
                </div>
            </div>
            @if($product->description)
            <div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:.4rem;">Description</div>
                <div style="font-size:.875rem;color:#374151;line-height:1.6;background:#f8fafc;border-radius:10px;padding:.75rem 1rem;border:1px solid #e2e8f0;">
                    {{ $product->description }}
                </div>
            </div>
            @endif
        </div>
        <div class="prod-modal-footer">
            <button onclick="closeProdModal('viewProd-{{ $product->id }}'); openProdModal('editProd-{{ $product->id }}')" class="prod-btn prod-btn-amber">
                <i class="fas fa-edit me-1"></i> Edit
            </button>
            <button onclick="closeProdModal('viewProd-{{ $product->id }}')" class="prod-btn prod-btn-neutral">
                <i class="fas fa-times me-1"></i> Close
            </button>
        </div>
    </div>
</div>
@endforeach

{{-- ══════════════════════════════════════════
     EDIT PRODUCT MODALS (per row)
══════════════════════════════════════════ --}}
@foreach($products as $product)
@php $isThisProd = $errors->any() && old('_method') === 'PUT' && old('product_id') == $product->id; @endphp
<div class="prod-overlay" id="editProd-{{ $product->id }}">
    <div class="prod-modal" style="max-width:680px;">
        <div class="prod-modal-hdr">
            <button type="button" onclick="closeProdModal('editProd-{{ $product->id }}')" class="prod-modal-close">&times;</button>
            <div class="prod-modal-tag"><i class="fas fa-edit me-1"></i> Edit Product</div>
            <div class="prod-modal-title">{{ $product->name }}</div>
            <div class="prod-modal-sub">#{{ $product->sku }}</div>
        </div>
        <div class="prod-modal-body">
            <form id="editProdForm-{{ $product->id }}" method="POST" action="{{ route('inventory.products.update', $product) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="prod-form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="prod-form-input {{ $isThisProd && $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ $isThisProd ? old('name') : $product->name }}" required>
                        @if($isThisProd && $errors->has('name'))
                            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="prod-form-label">SKU <span class="text-danger">*</span></label>
                        <input type="text" name="sku"
                               class="prod-form-input {{ $isThisProd && $errors->has('sku') ? 'is-invalid' : '' }}"
                               value="{{ $isThisProd ? old('sku') : $product->sku }}" required>
                        @if($isThisProd && $errors->has('sku'))
                            <div class="invalid-feedback">{{ $errors->first('sku') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="prod-form-label">Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="prod-form-input" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ ($isThisProd ? old('category_id') : $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="prod-form-label">Price <span class="text-danger">*</span></label>
                        <div class="prod-input-group">
                            <span class="prod-input-prefix">$</span>
                            <input type="number" step="0.01" name="price"
                                   class="prod-form-input {{ $isThisProd && $errors->has('price') ? 'is-invalid' : '' }}"
                                   value="{{ $isThisProd ? old('price') : $product->price }}" min="0" required>
                        </div>
                        @if($isThisProd && $errors->has('price'))
                            <div class="invalid-feedback">{{ $errors->first('price') }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="prod-form-label">Low Stock Threshold <span class="text-danger">*</span></label>
                        <input type="number" name="low_stock_threshold" class="prod-form-input"
                               value="{{ $isThisProd ? old('low_stock_threshold') : ($product->inventory->low_stock_threshold ?? 5) }}"
                               min="0" required>
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <label class="prod-check-label" style="margin-top:1.4rem;">
                            <input type="checkbox" name="is_featured" value="1"
                                   {{ ($isThisProd ? old('is_featured') : $product->is_featured) ? 'checked' : '' }}>
                            <span>Featured Product</span>
                        </label>
                    </div>
                    <div class="col-12">
                        <label class="prod-form-label">Description <span class="text-danger">*</span></label>
                        <textarea name="description" rows="3"
                                  class="prod-form-input {{ $isThisProd && $errors->has('description') ? 'is-invalid' : '' }}">{{ $isThisProd ? old('description') : $product->description }}</textarea>
                        @if($isThisProd && $errors->has('description'))
                            <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label class="prod-form-label">Product Image</label>
                        @if($product->image)
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <img src="{{ $product->image_url }}" alt="Current"
                                 style="width:50px;height:50px;object-fit:cover;border-radius:8px;border:1.5px solid #e2e8f0;">
                            <span style="font-size:.78rem;color:#64748b;">Current image</span>
                        </div>
                        @endif
                        <input type="file" name="image" class="prod-form-input" accept="image/jpeg,image/png,image/jpg,image/gif">
                        <div style="font-size:.73rem;color:#94a3b8;margin-top:.25rem;">Leave blank to keep current image</div>
                    </div>
                </div>
            </form>
        </div>
        <div class="prod-modal-footer">
            <button onclick="closeProdModal('editProd-{{ $product->id }}')" class="prod-btn prod-btn-neutral">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button type="submit" form="editProdForm-{{ $product->id }}" class="prod-btn prod-btn-blue">
                <i class="fas fa-save me-1"></i> Update Product
            </button>
        </div>
    </div>
</div>
@endforeach

{{-- ══════════════════════════════════════════
     DELETE CONFIRM MODAL
══════════════════════════════════════════ --}}
<div class="prod-overlay" id="deleteConfirmOverlay">
    <div class="prod-modal" style="max-width:440px;">
        <div class="prod-modal-hdr" style="background:linear-gradient(135deg,#7f1d1d 0%,#b91c1c 60%,#dc2626 100%);">
            <button type="button" onclick="closeProdModal('deleteConfirmOverlay')" class="prod-modal-close">&times;</button>
            <div class="prod-modal-tag"><i class="fas fa-trash me-1"></i> Delete</div>
            <div class="prod-modal-title">Delete Product</div>
            <div class="prod-modal-sub">This action cannot be undone</div>
        </div>
        <div class="prod-modal-body">
            <p style="color:#374151;font-size:.9rem;margin-bottom:0;">
                Are you sure you want to delete <strong id="deleteProductName"></strong>?
                This will permanently remove the product and all associated data.
            </p>
        </div>
        <div class="prod-modal-footer">
            <button onclick="closeProdModal('deleteConfirmOverlay')" class="prod-btn prod-btn-neutral">
                <i class="fas fa-times me-1"></i> Cancel
            </button>
            <button onclick="submitDelete()" class="prod-btn prod-btn-red">
                <i class="fas fa-trash me-1"></i> Delete Product
            </button>
        </div>
    </div>
</div>

<style>
.container-fluid { background:#f8fafd; min-height:100vh; }

/* ── Header Button ── */
.prod-hdr-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.12); border: 1.5px solid rgba(255,255,255,.28);
    color: #fff; padding: .6rem 1.3rem; border-radius: 12px;
    font-weight: 600; font-size: .88rem; cursor: pointer;
    transition: background .15s, border-color .15s, transform .12s;
    backdrop-filter: blur(4px);
}
.prod-hdr-btn:hover { background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.45); transform: translateY(-1px); }
.prod-hdr-btn:active { transform: scale(.95); }

/* ── Card ── */
.prod-card {
    background: #fff; border-radius: 20px;
    box-shadow: 0 4px 20px rgba(13,20,40,.07);
    border: 1.5px solid #f1f5f9; overflow: hidden; margin-bottom: 1.5rem;
}
.prod-card-header {
    padding: 1rem 1.4rem; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.prod-card-icon {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .9rem;
    background: linear-gradient(135deg,#2563eb1a,#1e3a8a1a); color: #2563eb;
}
.prod-card-title { font-weight: 700; font-size: .9rem; color: #0f172a; }
.prod-card-sub   { font-size: .72rem; color: #94a3b8; }

/* ── Table ── */
.prod-table { width: 100%; border-collapse: collapse; font-size: .865rem; }
.prod-table thead tr { background: linear-gradient(135deg, #0f172a, #1e3a8a); }
.prod-table th {
    font-size: .68rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #fff; padding: .7rem 1rem; border: none;
}
.prod-table tbody tr { transition: background .12s; }
.prod-table tbody tr:nth-child(even) { background: #f8fafc; }
.prod-table tbody tr:nth-child(odd)  { background: #fff; }
.prod-table tbody tr:hover { background: #eff6ff; }
.prod-table td { padding: .65rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.prod-table tbody tr:last-child td { border-bottom: none; }

/* ── Thumbnail ── */
.prod-thumb { width: 46px; height: 46px; object-fit: cover; border-radius: 10px; border: 1.5px solid #e2e8f0; }

/* ── Name / Snippet ── */
.prod-name { font-weight: 700; color: #0f172a; font-size: .875rem; }
.prod-desc-snippet { font-size: .72rem; color: #94a3b8; margin-top: .1rem; }

/* ── Pills ── */
.prod-sku-pill {
    font-family: monospace; font-size: .78rem; font-weight: 700;
    background: #f1f5f9; color: #475569;
    padding: .15rem .55rem; border-radius: 6px; border: 1px solid #e2e8f0;
}
.prod-cat-pill {
    font-size: .72rem; font-weight: 600;
    background: #dbeafe; color: #1e40af;
    padding: .2rem .6rem; border-radius: 20px;
}
.prod-price { font-weight: 800; color: #059669; font-size: .9rem; }

/* ── Stock ── */
.prod-stock { font-weight: 700; color: #0f172a; }
.prod-stock.low { color: #b45309; }
.prod-low-badge {
    display: inline-block; font-size: .65rem; font-weight: 700;
    background: #fef3c7; color: #92400e;
    padding: .1rem .45rem; border-radius: 20px; margin-left: .3rem;
    border: 1px solid #fde68a;
}

/* ── Status Pills ── */
.prod-status-pill { display: inline-flex; align-items: center; padding: .2rem .65rem; border-radius: 20px; font-size: .7rem; font-weight: 700; }
.prod-status-pill.featured { background: #d1fae5; color: #065f46; }
.prod-status-pill.regular  { background: #f1f5f9; color: #475569; }

/* ── Action Buttons ── */
.prod-act-btn {
    width: 32px; height: 32px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .78rem; cursor: pointer; border: 1.5px solid transparent;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
    background: transparent;
}
.prod-act-btn:active { transform: scale(.90); }
.prod-act-btn.view  { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; }
.prod-act-btn.view:hover  { background: #dbeafe; border-color: #93c5fd; color: #1e40af; }
.prod-act-btn.edit  { background: #fffbeb; border-color: #fde68a; color: #d97706; }
.prod-act-btn.edit:hover  { background: #fef3c7; border-color: #fcd34d; color: #b45309; }
.prod-act-btn.del   { background: #fff1f2; border-color: #fecaca; color: #dc2626; }
.prod-act-btn.del:hover   { background: #fee2e2; border-color: #f87171; color: #b91c1c; }

/* ── Pagination ── */
.prod-page-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; border-radius: 8px; padding: 0 .5rem;
    font-size: .8rem; font-weight: 600;
    background: #fff; border: 1.5px solid #e2e8f0; color: #374151;
    text-decoration: none;
    transition: background .15s, border-color .15s, color .15s, transform .12s;
}
.prod-page-btn:hover:not(.disabled):not(.active) { background: #eff6ff; border-color: #bfdbfe; color: #2563eb; transform: translateY(-1px); text-decoration: none; }
.prod-page-btn:active:not(.disabled) { transform: scale(.94); }
.prod-page-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
.prod-page-btn.disabled { opacity: .45; cursor: not-allowed; pointer-events: none; }

/* ── Empty State ── */
.prod-empty-state { text-align: center; padding: 3rem 1rem; }
.prod-empty-icon {
    width: 56px; height: 56px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.4rem; margin-bottom: .75rem;
    background: #dbeafe; color: #2563eb;
}
.prod-empty-text { font-weight: 700; color: #374151; font-size: .95rem; }
.prod-empty-sub  { font-size: .8rem; color: #94a3b8; margin-top: .3rem; }

/* ── Overlay / Modal ── */
.prod-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(15,23,42,.55); backdrop-filter: blur(3px);
    z-index: 1055; align-items: center; justify-content: center; padding: 1rem;
    overflow-y: auto;
}
.prod-overlay.open { display: flex; }

.prod-modal {
    background: #fff; border-radius: 20px;
    box-shadow: 0 20px 60px rgba(15,23,42,.25);
    width: 100%; max-height: 90vh; overflow: hidden;
    display: flex; flex-direction: column;
    animation: prodModalIn .2s ease;
    margin: auto;
}
@keyframes prodModalIn { from { opacity:0; transform:translateY(-16px) scale(.97); } to { opacity:1; transform:none; } }

/* ── Modal Gradient Header ── */
.prod-modal-hdr {
    background: linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#2563eb 100%);
    padding: 1.5rem 1.5rem 1.3rem; position: relative;
    border-radius: 20px 20px 0 0; flex-shrink: 0; overflow: hidden;
}
.prod-modal-hdr::before {
    content: ''; position: absolute; top: -30px; right: -30px;
    width: 130px; height: 130px; border-radius: 50%;
    background: rgba(255,255,255,.05); pointer-events: none;
}
.prod-modal-hdr::after {
    content: ''; position: absolute; bottom: -40px; left: 30%;
    width: 160px; height: 160px; border-radius: 50%;
    background: rgba(255,255,255,.03); pointer-events: none;
}
.prod-modal-tag   { font-size:.65rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; color:rgba(255,255,255,.55); margin-bottom:.5rem; position:relative; z-index:1; }
.prod-modal-title { font-size:1.35rem; font-weight:800; color:#fff; line-height:1.2; margin-bottom:.3rem; position:relative; z-index:1; }
.prod-modal-sub   { font-size:.82rem; color:rgba(255,255,255,.55); position:relative; z-index:1; }
.prod-modal-close {
    position: absolute; top: 1rem; right: 1rem; z-index: 2;
    width: 34px; height: 34px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; line-height: 1; cursor: pointer;
    background: rgba(255,255,255,.15); border: 1.5px solid rgba(255,255,255,.25); color: #fff;
    transition: background .15s, border-color .15s, transform .12s;
}
.prod-modal-close:hover  { background: rgba(239,68,68,.55); border-color: rgba(239,68,68,.75); }
.prod-modal-close:active { background: rgba(239,68,68,.75); border-color: #ef4444; transform: scale(.88); }

.prod-modal-body { padding: 1.3rem 1.4rem; overflow-y: auto; flex: 1; }
.prod-modal-footer {
    display: flex; align-items: center; justify-content: flex-end; gap: .6rem;
    padding: .9rem 1.4rem; border-top: 1px solid #f1f5f9; flex-shrink: 0;
    background: #f8fafc;
}

/* ── Form Inputs ── */
.prod-form-label {
    display: block; font-size: .74rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #475569; margin-bottom: .35rem;
}
.prod-form-input {
    width: 100%; border-radius: 10px; border: 1.5px solid #e2e8f0;
    padding: .5rem .75rem; font-size: .875rem; color: #0f172a;
    background: #fff; outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.prod-form-input:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(147,197,253,.2); }
.prod-form-input.is-invalid { border-color: #f87171; }
.prod-form-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(248,113,113,.18); }
textarea.prod-form-input { resize: vertical; }
select.prod-form-input { appearance: auto; cursor: pointer; }

.prod-input-group { position: relative; display: flex; align-items: center; }
.prod-input-prefix {
    position: absolute; left: .75rem;
    font-weight: 700; color: #94a3b8; font-size: .875rem; pointer-events: none;
}
.prod-input-group .prod-form-input { padding-left: 1.6rem; }

.prod-check-label { display: flex; align-items: center; gap: .5rem; cursor: pointer; font-size: .875rem; color: #374151; font-weight: 500; }
.prod-check-label input[type=checkbox] { width: 16px; height: 16px; accent-color: #2563eb; cursor: pointer; flex-shrink: 0; }

/* ── View Modal Extras ── */
.prod-view-img { width: 100px; height: 100px; object-fit: cover; border-radius: 14px; border: 2px solid #e2e8f0; flex-shrink: 0; }
.prod-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .65rem; }
.prod-info-item { background: #fff; border-radius: 10px; padding: .7rem 1rem; border: 1.5px solid #e2e8f0; }
.prod-info-label { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #94a3b8; margin-bottom: .3rem; }
.prod-info-value { font-weight: 700; color: #0f172a; font-size: .875rem; }

/* ── Modal Buttons ── */
.prod-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .48rem 1.1rem; border-radius: 10px;
    font-weight: 600; font-size: .82rem; cursor: pointer;
    border: 1.5px solid transparent;
    transition: background .15s, border-color .15s, color .15s, box-shadow .15s, transform .12s;
}
.prod-btn:active { transform: scale(.95); }

.prod-btn-green {
    background: linear-gradient(135deg,#16a34a,#15803d);
    border-color: transparent; color: #fff;
    box-shadow: 0 2px 8px rgba(22,163,74,.35);
}
.prod-btn-green:hover  { background: linear-gradient(135deg,#15803d,#166534); box-shadow: 0 4px 14px rgba(22,163,74,.45); transform: translateY(-1px); color: #fff; }
.prod-btn-green:active { transform: scale(.96); box-shadow: none; }

.prod-btn-blue {
    background: linear-gradient(135deg,#2563eb,#1e40af);
    border-color: transparent; color: #fff;
    box-shadow: 0 2px 8px rgba(37,99,235,.35);
}
.prod-btn-blue:hover  { background: linear-gradient(135deg,#1d4ed8,#1e3a8a); box-shadow: 0 4px 14px rgba(37,99,235,.45); transform: translateY(-1px); color: #fff; }
.prod-btn-blue:active { transform: scale(.96); box-shadow: none; }

.prod-btn-amber  { background: #fffbeb; border-color: #fde68a; color: #b45309; }
.prod-btn-amber:hover  { background: #fef3c7; border-color: #fcd34d; color: #92400e; }
.prod-btn-amber:active { background: #fde68a; border-color: #fbbf24; }

.prod-btn-red {
    background: linear-gradient(135deg,#dc2626,#b91c1c);
    border-color: transparent; color: #fff;
    box-shadow: 0 2px 8px rgba(220,38,38,.3);
}
.prod-btn-red:hover  { background: linear-gradient(135deg,#b91c1c,#991b1b); box-shadow: 0 4px 14px rgba(220,38,38,.4); transform: translateY(-1px); color: #fff; }
.prod-btn-red:active { transform: scale(.96); box-shadow: none; }

.prod-btn-neutral { background: #f8fafc; border-color: #e2e8f0; color: #475569; }
.prod-btn-neutral:hover  { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
.prod-btn-neutral:active { background: #fecaca; border-color: #ef4444; color: #b91c1c; }
</style>

<script>
function openProdModal(id) {
    document.querySelectorAll('.prod-overlay.open').forEach(function(el) { el.classList.remove('open'); });
    var el = document.getElementById(id);
    if (el) el.classList.add('open');
}
function closeProdModal(id) {
    var el = document.getElementById(id);
    if (el) el.classList.remove('open');
}

document.addEventListener('DOMContentLoaded', function () {
    // Close on backdrop click
    document.querySelectorAll('.prod-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('open');
        });
    });

    // Escape key closes any open modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.prod-overlay.open').forEach(function(el) { el.classList.remove('open'); });
        }
    });

    // Auto-open add modal on validation error (create form)
    @if($errors->any() && old('_method') !== 'PUT')
        openProdModal('addProductOverlay');
    @endif

    // Auto-open edit modal on validation error (update form)
    @if($errors->any() && old('_method') === 'PUT' && old('product_id'))
        openProdModal('editProd-{{ old('product_id') }}');
    @endif
});

// Delete confirmation
var deleteFormId = null;
function confirmDelete(productId, productName) {
    deleteFormId = productId;
    document.getElementById('deleteProductName').textContent = productName;
    openProdModal('deleteConfirmOverlay');
}
function submitDelete() {
    if (deleteFormId !== null) {
        document.getElementById('delForm-' + deleteFormId).submit();
    }
}
</script>
@endsection
