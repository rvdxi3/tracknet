@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%); margin-top: -1rem; position: relative; overflow: hidden;">
                <div style="position: absolute; inset: 0; opacity: 0.05; pointer-events: none;"></div>
                <div class="position-relative" style="z-index: 1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;">
                            <i class="fas fa-crown me-1"></i> Administrator Access
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold text-white mb-1" style="font-size: 2rem;">Customer Profile</h2>
                            <p class="text-white-50 mb-0" style="font-size: 0.9rem;">Full order history and account details</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.customers.index') }}" class="btn" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 0.5rem 1.2rem; border-radius: 12px; font-weight: 600; font-size: 0.85rem;">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 0.5rem 1.2rem; border-radius: 12px; font-weight: 600; font-size: 0.85rem;">
                                <i class="fas fa-edit me-2"></i>Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Customer Info Card --}}
        <div class="col-lg-4">
            <div class="card border-0" style="border-radius: 20px; box-shadow: 0 10px 40px -5px rgba(13, 20, 40, 0.15); overflow: hidden;">
                <div class="card-body text-center py-4 px-4">
                    <div style="width: 72px; height: 72px; border-radius: 18px; background: linear-gradient(135deg, #2563eb, #1e3a8a); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.8rem; margin: 0 auto 0.85rem;">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1" style="color: #0f172a;">{{ $customer->name }}</h5>
                    <p class="text-muted mb-3" style="font-size: 0.875rem;">{{ $customer->email }}</p>

                    {{-- Stats --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <div style="background: #f8fafd; border-radius: 12px; padding: 0.75rem; text-align: center; border: 1px solid #eef2f6;">
                            <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a;">{{ $orders->total() }}</div>
                            <div style="font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">Orders</div>
                        </div>
                        <div style="background: #f8fafd; border-radius: 12px; padding: 0.75rem; text-align: center; border: 1px solid #eef2f6;">
                            <div style="font-size: 1.4rem; font-weight: 800; color: #0f172a;">₱{{ number_format($orders->sum('total'), 0) }}</div>
                            <div style="font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">Spent</div>
                        </div>
                    </div>
                </div>

                {{-- Info Rows --}}
                <div style="border-top: 1px solid #eef2f6; padding: 1rem 1.25rem; display: flex; flex-direction: column; gap: 0.7rem;">
                    <div class="d-flex align-items-center justify-content-between">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Status</span>
                        @if($customer->is_active)
                            <span style="background: #dcfce7; color: #16a34a; padding: 0.2rem 0.7rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Active</span>
                        @else
                            <span style="background: #f1f5f9; color: #64748b; padding: 0.2rem 0.7rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Inactive</span>
                        @endif
                    </div>
                    <div style="height: 1px; background: #eef2f6;"></div>
                    <div class="d-flex align-items-center justify-content-between">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Member Since</span>
                        <span style="color: #0f172a; font-weight: 500; font-size: 0.85rem;">{{ $customer->created_at->format('M d, Y') }}</span>
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
                        <span style="color: #94a3b8; font-weight: 600; font-size: 0.82rem;">#{{ $customer->id }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="col-lg-8">
            <div class="card border-0" style="border-radius: 20px; box-shadow: 0 10px 40px -5px rgba(13, 20, 40, 0.15); overflow: hidden;">
                <div class="card-header bg-transparent py-3 px-4 d-flex align-items-center gap-3" style="border-bottom: 1px solid #eef2f6;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #2563eb15, #1e3a8a15); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shopping-bag" style="color: #2563eb;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color: #0f172a; font-size: 1rem;">Order History</h6>
                        <small class="text-muted">{{ $orders->total() }} total orders</small>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="min-width: 580px;">
                            <thead style="background: #f8fafd;">
                                <tr>
                                    <th class="px-4 py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Order #</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Date</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Total</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Payment</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Fulfillment</th>
                                    <th class="py-3" style="color: #64748b; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                @php
                                    $ps = $order->sale->payment_status ?? null;
                                    $fs = $order->sale->fulfillment_status ?? null;
                                    $payMap = ['paid'=>['#dcfce7','#16a34a'],'refunded'=>['#fee2e2','#dc2626'],'pending'=>['#fef3c7','#d97706']];
                                    $fulMap = ['delivered'=>['#dcfce7','#16a34a'],'shipped'=>['#dbeafe','#2563eb'],'cancelled'=>['#fee2e2','#dc2626'],'processing'=>['#e0f2fe','#0369a1'],'pending'=>['#fef3c7','#d97706']];
                                    [$pbg,$pt] = $payMap[$ps] ?? ['#f1f5f9','#64748b'];
                                    [$fbg,$ft] = $fulMap[$fs] ?? ['#f1f5f9','#64748b'];
                                @endphp
                                <tr style="border-bottom: 1px solid #eef2f6;">
                                    <td class="px-4 py-3" style="font-weight: 600; color: #0f172a; font-size: 0.88rem;">{{ $order->order_number }}</td>
                                    <td class="py-3" style="color: #475569; font-size: 0.85rem;">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="py-3" style="font-weight: 700; color: #0f172a;">₱{{ number_format($order->total, 2) }}</td>
                                    <td class="py-3">
                                        @if($ps)
                                        <span style="background: {{ $pbg }}; color: {{ $pt }}; padding: 0.25rem 0.7rem; border-radius: 20px; font-size: 0.72rem; font-weight: 600;">{{ ucfirst($ps) }}</span>
                                        @else
                                        <span style="color: #94a3b8;">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        @if($fs)
                                        <span style="background: {{ $fbg }}; color: {{ $ft }}; padding: 0.25rem 0.7rem; border-radius: 20px; font-size: 0.72rem; font-weight: 600;">{{ ucfirst($fs) }}</span>
                                        @else
                                        <span style="color: #94a3b8;">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <a href="{{ route('sales.orders.show', $order) }}"
                                           style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; border-radius: 8px; background: #eff6ff; color: #2563eb; border: none; text-decoration: none;" title="View Order">
                                            <i class="fas fa-eye" style="font-size: 0.75rem;"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-shopping-bag mb-2 d-block" style="font-size: 1.5rem; color: #cbd5e1;"></i>
                                        <p class="text-muted mb-0" style="font-size: 0.88rem;">No orders yet.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($orders->hasPages())
                <div class="card-footer bg-transparent py-3 px-4" style="border-top: 1px solid #eef2f6;">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <small class="text-muted">Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }} orders</small>
                        <nav class="custom-pagination">
                            @if($orders->onFirstPage())
                                <span class="page-btn disabled"><i class="fas fa-chevron-left"></i></span>
                            @else
                                <a href="{{ $orders->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                            @endif
                            @php $cp=$orders->currentPage();$lp=$orders->lastPage();$st=max(1,$cp-2);$en=min($lp,$cp+2); @endphp
                            @if($st>1)<a href="{{ $orders->url(1) }}" class="page-btn">1</a>@if($st>2)<span class="page-btn dots">…</span>@endif@endif
                            @for($p=$st;$p<=$en;$p++)
                                @if($p===$cp)<span class="page-btn active">{{$p}}</span>@else<a href="{{ $orders->url($p) }}" class="page-btn">{{$p}}</a>@endif
                            @endfor
                            @if($en<$lp)@if($en<$lp-1)<span class="page-btn dots">…</span>@endif<a href="{{ $orders->url($lp) }}" class="page-btn">{{$lp}}</a>@endif
                            @if($orders->hasMorePages())
                                <a href="{{ $orders->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                            @else
                                <span class="page-btn disabled"><i class="fas fa-chevron-right"></i></span>
                            @endif
                        </nav>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

<style>
.container-fluid { background: #f8fafd; min-height: 100vh; }
.card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
.card:hover { transform: translateY(-2px); box-shadow: 0 20px 40px -5px rgba(13,20,40,0.2) !important; }
.table-hover tbody tr:hover { background: #f1f5f9; }
.custom-pagination { display: flex; align-items: center; gap: 4px; }
.page-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 8px; border-radius: 10px; font-size: 0.82rem; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0; background: #ffffff; color: #475569; transition: background 0.18s, border-color 0.18s, color 0.18s, transform 0.18s, box-shadow 0.18s; cursor: pointer; user-select: none; }
.page-btn:hover { background: #eff6ff; border-color: #2563eb; color: #2563eb; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(37,99,235,0.12); }
.page-btn.active { background: linear-gradient(135deg, #2563eb, #1e3a8a); border-color: transparent; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,0.35); transform: translateY(-1px); pointer-events: none; }
.page-btn.disabled { background: #f8fafd; border-color: #e2e8f0; color: #cbd5e1; pointer-events: none; cursor: default; }
.page-btn.dots { border-color: transparent; background: transparent; color: #94a3b8; pointer-events: none; cursor: default; letter-spacing: 0.05em; }
</style>
@endsection
