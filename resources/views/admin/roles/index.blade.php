@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Roles Management</h4>
                    <h6>Manage roles</h6>
                </div>
            </div>
            <div class="page-btn">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add New Role</a>
            </div>
        </div>
        
        @include('layouts._messages')

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Role Name</th>
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $key => $role)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST">
                                    <a class="btn btn-primary btn-sm" href="{{ route('admin.roles.edit', $role->id) }}">Edit</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this role?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No roles found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection