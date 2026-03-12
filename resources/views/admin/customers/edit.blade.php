@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="p-4 rounded-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #2563eb 100%); margin-top: -1rem; position: relative; overflow: hidden;">
                <div class="position-relative" style="z-index: 1;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.9); padding: 0.35rem 1rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase;">
                            <i class="fas fa-crown me-1"></i> Administrator Access
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h2 class="fw-bold text-white mb-1" style="font-size: 2rem;">Edit Customer</h2>
                            <p class="text-white-50 mb-0" style="font-size: 0.9rem;">Update account info for {{ $customer->name }}</p>
                        </div>
                        <a href="{{ route('admin.customers.show', $customer) }}" class="btn" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 0.5rem 1.2rem; border-radius: 12px; font-weight: 600; font-size: 0.85rem;">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card border-0" style="border-radius: 20px; box-shadow: 0 10px 40px -5px rgba(13, 20, 40, 0.15); overflow: hidden;">

                <div class="card-header bg-transparent py-3 px-4 d-flex align-items-center gap-3" style="border-bottom: 1px solid #eef2f6;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #16a34a15, #14532d15); display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-edit" style="color: #16a34a;"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0" style="color: #0f172a; font-size: 1rem;">Account Information</h6>
                        <small class="text-muted">Name and email only</small>
                    </div>
                </div>

                <div class="card-body p-4">

                    {{-- Customer preview --}}
                    <div style="display: flex; align-items: center; gap: 0.85rem; padding: 0.85rem 1rem; background: #f8fafd; border: 1px solid #eef2f6; border-radius: 12px; margin-bottom: 1.5rem;">
                        <div style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, #2563eb, #1e3a8a); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1rem; flex-shrink: 0;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem;">{{ $customer->name }}</div>
                            <div style="font-size: 0.8rem; color: #64748b;">Customer #{{ $customer->id }}</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="modal-label">Full Name</label>
                            <input type="text" name="name" class="modal-input @error('name') is-invalid @enderror"
                                   value="{{ old('name', $customer->name) }}" required>
                            @error('name')<div class="invalid-feedback d-block" style="font-size:0.8rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="modal-label">Email Address</label>
                            <input type="email" name="email" class="modal-input @error('email') is-invalid @enderror"
                                   value="{{ old('email', $customer->email) }}" required>
                            @error('email')<div class="invalid-feedback d-block" style="font-size:0.8rem;">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="edit-submit-btn" style="flex: 1;">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('admin.customers.show', $customer) }}" class="edit-cancel-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.container-fluid { background: #f8fafd; min-height: 100vh; }
.card { transition: transform 0.2s ease, box-shadow 0.2s ease; }

.modal-label { display: block; font-size: 0.78rem; font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.4rem; }
.modal-input { width: 100%; height: 42px; padding: 0 0.85rem; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #f8fafd; color: #0f172a; font-size: 0.9rem; outline: none; transition: border-color 0.18s, box-shadow 0.18s, background 0.18s; }
.modal-input:focus { border-color: #2563eb; background: #ffffff; box-shadow: 0 0 0 3px rgba(37,99,235,0.12); }
.modal-input.is-invalid { border-color: #e11d48; }

.edit-submit-btn { padding: 0.6rem 1.5rem; border-radius: 10px; border: none; background: linear-gradient(135deg, #16a34a, #14532d); color: #ffffff; font-weight: 600; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(22,163,74,0.3); transition: opacity 0.15s, transform 0.15s; display: inline-flex; align-items: center; justify-content: center; }
.edit-submit-btn:hover { opacity: 0.9; transform: translateY(-1px); }
.edit-cancel-btn { padding: 0.6rem 1.3rem; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #f8fafd; color: #475569; font-weight: 600; font-size: 0.88rem; cursor: pointer; transition: background 0.15s; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
.edit-cancel-btn:hover { background: #f1f5f9; color: #374151; }
</style>
@endsection
