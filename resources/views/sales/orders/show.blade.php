@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Order Details
                    <a href="{{ route('sales.orders.index') }}" class="btn btn-sm btn-secondary float-right">Back to List</a>
                </div>

                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Order Number:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $order->order_number }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Customer:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $order->customer->name }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Status:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $order->status }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('sales.orders.edit', $order->id) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('sales.orders.destroy', $order->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection