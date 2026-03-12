@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h4 class="fw-bold mb-0">{{ $purchaseOrder->po_number }}</h4>
            <small class="text-muted">Purchase Order Details</small>
        </div>
        <div class="col-auto d-flex gap-2 flex-wrap">
            @if($purchaseOrder->status === 'pending')
                <form action="{{ route('inventory.purchase-orders.approve', $purchaseOrder) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-success">
                        <i class="fas fa-check me-1"></i>Approve
                    </button>
                </form>
                <a href="{{ route('inventory.purchase-orders.edit', $purchaseOrder) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-pen me-1"></i>Edit
                </a>
                <form action="{{ route('inventory.purchase-orders.cancel', $purchaseOrder) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Cancel this purchase order?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                </form>
                <form action="{{ route('inventory.purchase-orders.destroy', $purchaseOrder) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Permanently delete this purchase order?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            @elseif($purchaseOrder->status === 'approved')
                <form action="{{ route('inventory.purchase-orders.receive', $purchaseOrder) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Mark as received? This will add the items to inventory stock.')">
                    @csrf
                    <button class="btn btn-sm btn-primary">
                        <i class="fas fa-box-open me-1"></i>Mark as Received
                    </button>
                </form>
                <form action="{{ route('inventory.purchase-orders.cancel', $purchaseOrder) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Cancel this purchase order?')">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                </form>
            @endif
            <a href="{{ route('inventory.purchase-orders.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
    </div>

    <div class="row g-3">

        {{-- Left column: meta --}}
        <div class="col-lg-4">

            <div class="card shadow-sm mb-3">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Order Info</h6>
                </div>
                <div class="card-body" style="font-size:.875rem;">
                    @php
                        $statusMap = [
                            'pending'   => 'warning text-dark',
                            'approved'  => 'info text-dark',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                        ];
                    @endphp
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Status</span>
                        <span class="badge bg-{{ $statusMap[$purchaseOrder->status] ?? 'secondary' }}">
                            {{ ucfirst($purchaseOrder->status) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Order Date</span>
                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Expected Delivery</span>
                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Created By</span>
                        <span class="fw-semibold">{{ $purchaseOrder->user->name ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Supplier</h6>
                </div>
                <div class="card-body" style="font-size:.875rem;">
                    <div class="fw-bold mb-2">{{ $purchaseOrder->supplier->name }}</div>
                    @if($purchaseOrder->supplier->contact_person)
                        <div class="text-muted mb-1"><i class="fas fa-user fa-fw me-1"></i>{{ $purchaseOrder->supplier->contact_person }}</div>
                    @endif
                    @if($purchaseOrder->supplier->email)
                        <div class="text-muted mb-1"><i class="fas fa-envelope fa-fw me-1"></i>{{ $purchaseOrder->supplier->email }}</div>
                    @endif
                    @if($purchaseOrder->supplier->phone)
                        <div class="text-muted mb-1"><i class="fas fa-phone fa-fw me-1"></i>{{ $purchaseOrder->supplier->phone }}</div>
                    @endif
                    @if($purchaseOrder->supplier->address)
                        <div class="text-muted"><i class="fas fa-map-marker-alt fa-fw me-1"></i>{{ $purchaseOrder->supplier->address }}</div>
                    @endif
                </div>
            </div>

            @if($purchaseOrder->notes)
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Notes</h6>
                </div>
                <div class="card-body" style="font-size:.875rem;">
                    <p class="mb-0 text-muted">{{ $purchaseOrder->notes }}</p>
                </div>
            </div>
            @endif

        </div>

        {{-- Right column: items --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Ordered Items</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="pe-3 text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchaseOrder->items as $item)
                                <tr>
                                    <td class="ps-3 fw-semibold">{{ $item->product->name ?? '—' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="pe-3 text-end fw-semibold">₱{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="ps-3 fw-bold text-end">Grand Total</td>
                                    <td class="pe-3 text-end fw-bold fs-6">
                                        ₱{{ number_format($purchaseOrder->items->sum('total_price'), 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if($purchaseOrder->status === 'delivered')
                <div class="card-footer bg-success-subtle text-success small">
                    <i class="fas fa-check-circle me-1"></i>
                    This order was received and inventory stock was updated automatically.
                </div>
                @elseif($purchaseOrder->status === 'cancelled')
                <div class="card-footer bg-danger-subtle text-danger small">
                    <i class="fas fa-times-circle me-1"></i>
                    This purchase order has been cancelled.
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
