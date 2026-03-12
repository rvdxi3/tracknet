@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- ── Page Header ── --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%); margin-top: -1rem; position: relative; overflow: hidden;">
                <div style="position:absolute; top:-40px; right:-40px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.04); pointer-events:none;"></div>
                <div class="position-relative" style="z-index:1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.2); color:rgba(255,255,255,0.9); padding:.35rem 1rem; border-radius:20px; font-size:.7rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase;">
                            <i class="fas fa-edit me-1"></i> Edit Order
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold text-white mb-1" style="font-size:2rem;">{{ $order->order_number }}</h2>
                            <p class="text-white-50 mb-0" style="font-size:.9rem;">
                                <i class="fas fa-user me-1"></i> Customer: {{ $order->user->name ?? '—' }}
                            </p>
                        </div>
                        <a href="{{ route('sales.orders.show', $order) }}" class="btn" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.3); color:white; padding:.5rem 1.2rem; border-radius:12px; font-weight:600; font-size:.85rem;">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-5">

            {{-- Update Form --}}
            <div class="card border-0 mb-4" style="border-radius:20px; box-shadow:0 10px 40px -5px rgba(13,20,40,.15); overflow:hidden;">
                <div class="card-header bg-transparent py-3 px-4 d-flex align-items-center gap-3" style="border-bottom:1px solid #eef2f6;">
                    <div style="width:40px; height:40px; border-radius:12px; background:linear-gradient(135deg,#2563eb15,#1e3a8a15); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-sliders-h" style="color:#2563eb;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:1rem;">Update Order Status</h6>
                        <small class="text-muted">Payment & fulfillment</small>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('sales.orders.update', $order) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="edit-label">Payment Status <span style="color:#e11d48;">*</span></label>
                            <div style="position:relative;">
                                <select name="payment_status" class="edit-input @error('payment_status') is-invalid @enderror" style="padding-right:2.5rem;">
                                    @foreach(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded'] as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('payment_status', $order->sale->payment_status ?? 'pending') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.72rem;pointer-events:none;"></i>
                            </div>
                            @error('payment_status')<div class="invalid-feedback d-block" style="font-size:.8rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="edit-label">Fulfillment Status <span style="color:#e11d48;">*</span></label>
                            <div style="position:relative;">
                                <select name="fulfillment_status" class="edit-input @error('fulfillment_status') is-invalid @enderror" style="padding-right:2.5rem;">
                                    @foreach(['pending' => 'Pending', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'] as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('fulfillment_status', $order->sale->fulfillment_status ?? 'pending') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:.85rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.72rem;pointer-events:none;"></i>
                            </div>
                            @error('fulfillment_status')<div class="invalid-feedback d-block" style="font-size:.8rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="edit-label">Order Notes</label>
                            <textarea name="notes" rows="3" class="edit-input @error('notes') is-invalid @enderror"
                                      style="height:auto; padding:.65rem .85rem; resize:vertical;"
                                      placeholder="Internal notes about this order…">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback d-block" style="font-size:.8rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="edit-submit-btn" style="flex:1;">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('sales.orders.show', $order) }}" class="edit-cancel-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Order Summary Reference --}}
            <div class="card border-0" style="border-radius:20px; box-shadow:0 10px 40px -5px rgba(13,20,40,.1); overflow:hidden;">
                <div class="card-header bg-transparent py-3 px-4 d-flex align-items-center gap-3" style="border-bottom:1px solid #eef2f6;">
                    <div style="width:40px; height:40px; border-radius:12px; background:linear-gradient(135deg,#f59e0b15,#d9770615); display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-receipt" style="color:#f59e0b;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:1rem;">Order Summary</h6>
                        <small class="text-muted">Read-only reference</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:.6rem .5rem; font-size:.875rem;">
                        <div style="color:#94a3b8; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding-top:.2rem;">Order #</div>
                        <div style="font-weight:700; color:#0f172a;">{{ $order->order_number }}</div>

                        <div style="color:#94a3b8; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding-top:.2rem;">Placed</div>
                        <div style="color:#475569;">{{ $order->created_at->format('M d, Y') }}</div>

                        <div style="color:#94a3b8; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding-top:.2rem;">Customer</div>
                        <div style="color:#475569;">{{ $order->user->name ?? '—' }}</div>

                        <div style="color:#94a3b8; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding-top:.2rem;">Items</div>
                        <div style="color:#475569;">{{ $order->items->count() }}</div>

                        <div style="color:#94a3b8; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding-top:.2rem;">Total</div>
                        <div style="font-weight:800; color:#2563eb; font-size:.95rem;">₱{{ number_format($order->total, 2) }}</div>

                        <div style="color:#94a3b8; font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding-top:.2rem;">Payment Method</div>
                        <div style="color:#475569;">{{ ucfirst(str_replace('_', ' ', $order->payment_method ?? '—')) }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
.container-fluid { background:#f8fafd; min-height:100vh; }

.edit-label { display:block; font-size:.78rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.4rem; }
.edit-input { width:100%; height:42px; padding:0 .85rem; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafd; color:#0f172a; font-size:.9rem; outline:none; transition:border-color .18s, box-shadow .18s, background .18s; appearance:none; }
.edit-input:focus { border-color:#2563eb; background:#ffffff; box-shadow:0 0 0 3px rgba(37,99,235,.12); }
.edit-input.is-invalid { border-color:#e11d48; }

.edit-submit-btn { padding:.6rem 1.5rem; border-radius:10px; border:none; background:linear-gradient(135deg,#2563eb,#1e3a8a); color:#ffffff; font-weight:600; font-size:.88rem; cursor:pointer; box-shadow:0 4px 12px rgba(37,99,235,.35); transition:opacity .15s, transform .15s; display:inline-flex; align-items:center; justify-content:center; }
.edit-submit-btn:hover { opacity:.9; transform:translateY(-1px); }
.edit-cancel-btn { padding:.6rem 1.3rem; border-radius:10px; border:1.5px solid #e2e8f0; background:#f8fafd; color:#475569; font-weight:600; font-size:.88rem; cursor:pointer; transition:background .15s, border-color .15s, color .15s; display:inline-flex; align-items:center; justify-content:center; text-decoration:none; }
.edit-cancel-btn:hover { background:#fee2e2; color:#dc2626; border-color:#fca5a5; }
.edit-cancel-btn:active { background:#dc2626; color:#fff; border-color:#dc2626; }
</style>
@endsection
