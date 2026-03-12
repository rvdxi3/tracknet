@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h4 class="fw-bold mb-0">New Purchase Order</h4>
            <small class="text-muted">Order stock from a supplier</small>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.purchase-orders.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('inventory.purchase-orders.store') }}">
        @csrf
        <div class="row g-3">

            {{-- Left column: order details --}}
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold text-primary">Order Details</h6>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                <option value="">— Select Supplier —</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Order Date <span class="text-danger">*</span></label>
                            <input type="date" name="order_date"
                                   class="form-control @error('order_date') is-invalid @enderror"
                                   value="{{ old('order_date', date('Y-m-d')) }}" required>
                            @error('order_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Expected Delivery <span class="text-danger">*</span></label>
                            <input type="date" name="expected_delivery_date"
                                   class="form-control @error('expected_delivery_date') is-invalid @enderror"
                                   value="{{ old('expected_delivery_date') }}" required>
                            @error('expected_delivery_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                      rows="3" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right column: product items --}}
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">Products to Order</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="add-row">
                            <i class="fas fa-plus me-1"></i>Add Product
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3" style="min-width:200px;">Product</th>
                                        <th style="width:90px;">Qty</th>
                                        <th style="width:130px;">Unit Price (₱)</th>
                                        <th style="width:110px;" class="text-end">Line Total</th>
                                        <th style="width:46px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="product-rows"></tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="ps-3 fw-bold text-end pe-3">Grand Total</td>
                                        <td class="text-end fw-bold" id="grand-total">$0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @error('products')
                    <div class="card-footer bg-danger-subtle">
                        <span class="text-danger small">{{ $message }}</span>
                    </div>
                    @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Create Purchase Order
                    </button>
                    <a href="{{ route('inventory.purchase-orders.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name]));
let rowIndex = 0;

function addRow(selectedId, qty, price) {
    selectedId = selectedId ?? '';
    qty        = qty        ?? 1;
    price      = price      ?? '';

    const tbody = document.getElementById('product-rows');
    const i = rowIndex++;

    const options = products.map(p =>
        `<option value="${p.id}" ${p.id == selectedId ? 'selected' : ''}>${p.name}</option>`
    ).join('');

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="ps-3">
            <select name="products[${i}][id]" class="form-select form-select-sm" required>
                <option value="">— Select —</option>
                ${options}
            </select>
        </td>
        <td>
            <input type="number" name="products[${i}][quantity]"
                   class="form-control form-control-sm qty-input"
                   value="${qty}" min="1" required>
        </td>
        <td>
            <input type="number" name="products[${i}][unit_price]"
                   class="form-control form-control-sm price-input"
                   value="${price}" min="0" step="0.01" required placeholder="0.00">
        </td>
        <td class="text-end fw-semibold row-total pe-2">$0.00</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    tr.querySelector('.qty-input').addEventListener('input',   () => updateRowTotal(tr));
    tr.querySelector('.price-input').addEventListener('input', () => updateRowTotal(tr));
    tr.querySelector('.remove-row').addEventListener('click',  () => { tr.remove(); updateGrandTotal(); });

    tbody.appendChild(tr);
    if (qty && price) updateRowTotal(tr);
    updateGrandTotal();
}

function updateRowTotal(tr) {
    const qty   = parseFloat(tr.querySelector('.qty-input').value)   || 0;
    const price = parseFloat(tr.querySelector('.price-input').value) || 0;
    tr.querySelector('.row-total').textContent = '₱' + (qty * price).toFixed(2);
    updateGrandTotal();
}

function updateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.row-total').forEach(el => {
        total += parseFloat(el.textContent.replace('₱', '')) || 0;
    });
    document.getElementById('grand-total').textContent = '₱' + total.toFixed(2);
}

document.getElementById('add-row').addEventListener('click', () => addRow());

// Restore old input on validation failure, otherwise start with one empty row
@if(old('products'))
    @foreach(old('products') as $p)
        addRow('{{ $p['id'] ?? '' }}', '{{ $p['quantity'] ?? 1 }}', '{{ $p['unit_price'] ?? '' }}');
    @endforeach
@else
    addRow();
@endif
</script>
@endpush
