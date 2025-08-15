@extends('layouts.app')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Category</h4>
                    <h6>Edit your Category: {{ $category->name }}</h6>
                </div>
            </div>
            
        </div>
        <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('categories._form', ['buttonText' => 'Update Category'])
        </form>
    </div>
</div>
@endsection