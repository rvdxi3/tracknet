@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Supplier Details
                    <a href="{{ route('inventory.suppliers.index') }}" class="btn btn-sm btn-secondary float-right">Back to List</a>
                </div>

                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Contact Person:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $supplier->contact_person }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Email:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $supplier->email }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Phone:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $supplier->phone }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-4 col-form-label text-md-right">Address:</label>
                        <div class="col-md-6">
                            <p class="form-control-plaintext">{{ $supplier->address }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <a href="{{ route('inventory.suppliers.edit', $supplier->id) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('inventory.suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this supplier?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection