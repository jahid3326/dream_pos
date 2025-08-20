@extends('layouts.app')
@section('title', 'Customers')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Customers</h4>
                        <h6>Manage your customers</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Customer', 'create'))
                        <a href="{{ route('customers.create') }}" class="btn btn-primary"><i
                                class="ti ti-circle-plus me-1"></i>Add New Customer</a>
                        <a href="{{ route('customers.import.show') }}" class="btn btn-secondary">Import Customer</a>
                    @endif
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
                    <div class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                        <div class="d-flex table-dropdown my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                            <div class="dropdown">
                                <a href="javascript:void(0);"
                                    class="dropdown-toggle btn btn-white btn-md d-inline-flex align-items-center"
                                    data-bs-toggle="dropdown">
                                    @if (request('status') === '1')
                                        Enabled
                                    @elseif(request('status') === '0')
                                        Disabled
                                    @else
                                        Status
                                    @endif
                                </a>
                                <ul class="dropdown-menu  dropdown-menu-end p-3">
                                    <li>
                                        <a href="{{ route('customers.index', ['status' => '1', 'search' => request('search')]) }}"
                                            class="dropdown-item rounded-1">Enabled</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('customers.index', ['status' => '0', 'search' => request('search')]) }}"
                                            class="dropdown-item rounded-1">Disabled</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('customers.index', ['search' => request('search')]) }}"
                                            class="dropdown-item rounded-1">All</a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table datatable" id="customer-table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="display: none">ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    @if (hasActionPermission('Customer', 'update') || hasActionPermission('Customer', 'delete'))
                                        <th class="no-sort"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                    <tr>
                                        <td style="display: none">
                                            {{ $customer->id }}
                                        </td>
                                        <td>
                                            <img src="{{ $customer->user->profile_picture ? asset('public/storage/' . $customer->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                                alt="" class="rounded-circle me-2" width="40" height="40">
                                            {{ $customer->user->name }}
                                        </td>
                                        <td>{{ $customer->user->email }}</td>
                                        <td>{{ $customer->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <span
                                                class="d-inline-flex align-items-center p-1 pe-2 rounded-1 text-white {{ $customer->status == 1 ? 'bg-success' : 'bg-danger' }} fs-10"><i
                                                    class="ti ti-point-filled me-1 fs-11"></i>{{ $customer->status == 1 ? 'Enabled' : 'Disabled' }}</span>
                                        </td>
                                        @if (hasActionPermission('Customer', 'update') || hasActionPermission('Customer', 'delete'))
                                            <td class="d-flex">
                                                <div class="edit-delete-action d-flex align-items-center">
                                                    @if (hasActionPermission('Customer', 'show'))
                                                        <a class="me-2 p-2 d-flex align-items-center border rounded"
                                                            href="{{ route('customers.show', $customer->id) }}">
                                                            <i data-feather="eye" class="feather-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if (hasActionPermission('Customer', 'update'))
                                                        <a href="{{ route('customers.edit', $customer->id) }}"
                                                            class="me-2 p-2 d-flex align-items-center border rounded">
                                                            <i data-feather="edit" class="feather-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (hasActionPermission('Customer', 'delete'))
                                                        <form action="{{ route('customers.destroy', $customer->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="me-2 p-2 d-flex align-items-center border rounded delete-button">
                                                                <i data-feather="trash-2" class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).on('click', '.delete-button', function() {

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
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        })
    </script>
@endpush
