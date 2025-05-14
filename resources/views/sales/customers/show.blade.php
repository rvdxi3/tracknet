@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Customer Details
                    <a href="{{ route('sales.customers.index') }}" class="btn btn-sm btn-secondary float-right">Back to List</a>
                </div>

                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Name:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $customer->name }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Email:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $customer->email }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Phone:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $customer->phone }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Address:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $customer->address }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('sales.customers.edit', $customer->id) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('sales.customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection