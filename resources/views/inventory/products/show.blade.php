@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Product Details: {{ $product->name }}</span>
                    <div>
                        <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('images/placeholder.png') }}" 
                                 alt="{{ $product->name }}" class="img-fluid" style="max-height: 200px;">
                        </div>
                        <div class="col-md-8">
                            <table class="table table-sm">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>SKU:</th>
                                    <td>{{ $product->sku }}</td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>{{ $product->category->name }}</td>
                                </tr>
                                <tr>
                                    <th>Price:</th>
                                    <td>${{ number_format($product->price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Current Stock:</th>
                                    <td>
                                        {{ $product->inventory->quantity ?? 0 }}
                                        @if($product->inventory && $product->inventory->quantity <= $product->inventory->low_stock_threshold)
                                            <span class="badge badge-warning ml-2">Low Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Low Stock Threshold:</th>
                                    <td>{{ $product->inventory->low_stock_threshold ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Featured:</th>
                                    <td>{{ $product->is_featured ? 'Yes' : 'No' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Description</h5>
                        <p>{{ $product->description ?? 'No description available' }}</p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('inventory.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                        <form action="{{ route('inventory.products.destroy', $product) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                <i class="fas fa-trash"></i> Delete Product
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection