@extends('layouts.app')
@section('title', 'Shipping Types')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Shipping Type</h4>
                        <h6>Edit Shipping Type</h6>
                    </div>
                </div>
            </div>
            @include('layouts._messages')
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('shipping-types.update', $type) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $type->name) }}"
                                required>
                        </div>

                        <button class="btn btn-primary">Update</button>
                        <a href="{{ route('shipping-types.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
