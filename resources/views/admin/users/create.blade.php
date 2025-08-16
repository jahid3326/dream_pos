@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div class="container">
        <h1>Create New User</h1>

        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.users._form', ['buttonText' => 'Create User'])
        </form>
    </div>
@endsection
