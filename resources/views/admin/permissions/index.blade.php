@extends('layouts.app')
@section('title', 'Permissions')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Manage Permissions</h4>
                        <h6>Select a role to manage its access permissions.</h6>
                    </div>
                </div>
            </div>

            @include('layouts._messages')

            <div class="card">
                <div class="card-body">
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
                                        <a href="{{ route('admin.permissions.edit', $role->id) }}"
                                            class="btn btn-primary">Edit Permissions</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
