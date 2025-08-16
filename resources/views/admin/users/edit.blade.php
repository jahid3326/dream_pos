@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div class="container">
        <h1>Edit User: {{ $user->name }}</h1>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.users._form', ['buttonText' => 'Update User'])
        </form>
    </div>
@endsection
