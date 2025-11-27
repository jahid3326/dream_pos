@extends('layouts.app')
@section('title', 'Shipping Taxes')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Shipping Tax</h4>
                        <h6>Edit Shipping Tax</h6>
                    </div>
                </div>
            </div>
            @include('layouts._messages')

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('shipping-taxes.update', $item) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $item->name) }}"
                                required>
                        </div>

                        <button class="btn btn-primary">Update</button>
                        <a href="{{ route('shipping-taxes.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
