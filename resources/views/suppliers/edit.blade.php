@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Supplier</h4>
                    <h6>Edit supplier</h6>
                </div>
            </div>
        </div>
        @include('layouts._messages')
        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('suppliers._form', ['buttonText' => 'Update Supplier'])
        </form>
    </div>
</div>
@endsection