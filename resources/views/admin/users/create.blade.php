@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">User Management</h4>
                        <h6>Create new user</h6>
                    </div>
                </div>
            </div>
            @include('layouts._messages')
            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('admin.users._form', ['buttonText' => 'Create User'])
            </form>
        </div>
    </div>
@endsection
