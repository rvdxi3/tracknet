@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Edit Order
                    <a href="{{ route('sales.orders.index') }}" class="btn btn-sm btn-secondary float-right">Back to List</a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('sales.orders.update', $order->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row">
                            <label for="order_number" class="col-md-4 col-form-label text-md-right">Order Number</label>
                            <div class="col-md-6">
                                <input id="order_number" type="text" class="form-control @error('order_number') is-invalid @enderror" name="order_number" value="{{ old('order_number', $order->order_number) }}" required>
                                @error('order_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="customer_id" class="col-md-4 col-form-label text-md-right">Customer</label>
                            <div class="col-md-6">
                                <select id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" name="customer_id" required>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="status" class="col-md-4 col-form-label text-md-right">Status</label>
                            <div class="col-md-6">
                                <input id="status" type="text" class="form-control @error('status') is-invalid @enderror" name="status" value="{{ old('status', $order->status) }}" required>
                                @error('status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Update Order
                                </button>
                                <a href="{{ route('sales.orders.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection