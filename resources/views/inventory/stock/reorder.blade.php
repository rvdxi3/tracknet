@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reorder Stock: {{ $product->name }}</h1>
        <a href="{{ route('inventory.stock.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Stock
        </a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Product Details</h6>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $product->name }}</p>
                    <p><strong>SKU:</strong> {{ $product->sku }}</p>
                    <p><strong>Current Stock:</strong>
                        <span class="badge {{ ($product->inventory && $product->inventory->quantity <= $product->inventory->low_stock_threshold) ? 'bg-danger' : 'bg-success' }}">
                            {{ $product->inventory->quantity ?? 0 }}
                        </span>
                    </p>
                    <p><strong>Low Stock Threshold:</strong> {{ $product->inventory->low_stock_threshold ?? 5 }}</p>
                    <p><strong>Price:</strong> ₱{{ number_format($product->price, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Reorder Form</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory.stock.processReorder', $product) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="supplier_id">Supplier</label>
                            <select class="form-control @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                <option value="">Select a supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="quantity">Quantity to Order</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                   id="quantity" name="quantity" value="{{ old('quantity', 10) }}" min="1" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="unit_price">Unit Price (₱)</label>
                            <input type="number" step="0.01" class="form-control @error('unit_price') is-invalid @enderror"
                                   id="unit_price" name="unit_price" value="{{ old('unit_price', $product->price) }}" min="0" required>
                            @error('unit_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-truck"></i> Place Reorder
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
