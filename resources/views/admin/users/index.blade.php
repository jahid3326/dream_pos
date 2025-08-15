@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="add-item d-flex">
                <div class="page-title">
                    <h4 class="fw-bold">Users Management</h4>
                    <h6>Manage your users</h6>
                </div>
            </div>
            <div class="page-btn">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add New User</a>
            </div>
        </div>

        @include('layouts._messages')
    
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                <div class="search-set">
                    <div class="search-input">
                        <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th width="280px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ $user->profile_picture ? asset('public/storage/' . $user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                        alt="{{ $user->name }}" width="50" height="50" style="border-radius: 50%;">
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge bg-info">{{ $user->role->name }}</span></td>
                                <td class="d-flex">
                                    <div class="edit-delete-action d-flex align-items-center">
                                        <a class="me-2 p-2 d-flex align-items-center border rounded" href="{{ route('admin.users.edit', $user->id) }}">
                                            <i data-feather="eye" class="feather-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="me-2 d-flex align-items-center border rounded delete-button">
                                                <i data-feather="trash-2" class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        $(document).on('click', '.delete-button', function(){

                // Prevent the form from submitting immediately
                event.preventDefault(); 

                // Find the closest parent form of the clicked button
                const form = this.closest('form');

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!", // ✅ This works
                    cancelButtonText: "Cancel",
                    customClass: {
                        confirmButton: "btn btn-primary",
                        cancelButton: "btn btn-danger ml-1"
                    },
                    buttonsStyling: false
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            })    
    </script>
@endpush