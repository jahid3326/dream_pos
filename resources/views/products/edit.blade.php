@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit Product: {{ $product->name }}</h1>
    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('products._form', ['buttonText' => 'Update Product'])
    </form>
</div>
@endsection