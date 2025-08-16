@extends('layouts.app')
@section('title', 'Permissions')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Manage Action Permissions (CRUD)</h4>
                        <h6>Select a role to manage what they can do (Create, Read, Update, Delete) on specific pages.</h6>
                    </div>
                </div>
            </div>

            @include('layouts._messages')
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>
                                <a href="{{ route('admin.action-permissions.edit', $role->id) }}" class="btn btn-primary">Edit
                                    Permissions</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
