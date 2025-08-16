@extends('layouts.app')
@section('title', 'Suppliers')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Supplier</h4>
                        <h6>Create new supplier</h6>
                    </div>
                </div>
            </div>
            @include('layouts._messages')
            <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('suppliers._form', ['buttonText' => 'Save Supplier'])
            </form>
        </div>
    </div>
@endsection
