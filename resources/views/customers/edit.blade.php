@extends('layouts.app')
@section('title', 'Customers')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Customer</h4>
                        <h6>Edit customer</h6>
                    </div>
                </div>
            </div>
            @include('layouts._messages')
            <form action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('customers._form', ['buttonText' => 'Update Customer'])
            </form>
        </div>
    </div>
@endsection
