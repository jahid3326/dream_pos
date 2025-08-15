@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Customer</h4>
                    <h6>Create new customer</h6>
                </div>
            </div>
        </div>
        @include('layouts._messages')
        <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('customers._form', ['buttonText' => 'Save Customer'])
        </form>
    </div>
</div>
@endsection