@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Edit Pack: {{ $pack->name }}</h1>
            <a href="{{ route('packs.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
        <form action="{{ route('packs.update', $pack->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.packs._form', ['buttonText' => 'Update Pack'])
        </form>
    </div>
@endsection
