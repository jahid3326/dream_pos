@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Manage Permissions</h4>
                    <h6>Edit Navigation Permissions for: <strong>{{ $role->name }}</strong></h6>
                </div>
            </div>
        </div>
    
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.permissions.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <p>Select the navigation items and headers this role should have access to.</p>
                    <p class="text-muted small">Note: A user must have access to a parent (Header/Dropdown) to see the children within it.</p>

                    {{-- Use a list-group for nice styling --}}
                    <div class="list-group">
                        {{-- Loop through the top-level items and call the recursive partial --}}
                        @foreach ($allNavItems as $navItem)
                            @include('admin.permissions._nav_item_permission_checkbox', ['item' => $navItem, 'level' => 0])
                        @endforeach
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Save Permissions</button>
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection