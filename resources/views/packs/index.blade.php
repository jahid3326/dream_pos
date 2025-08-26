@extends('layouts.app')
@section('title', 'Pack')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Pack</h4>
                        <h6>Manage your packs</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Pack', 'create'))
                        <a href="{{ route('packs.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add
                            New Pack</a>
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
                </div>
                <div class="card-body">
                    <table class="table datatable">
                        <thead class="thead-light">
                            <tr>
                                <th style="display: none">ID</th>
                                <th>Name</th>
                                <th>No. of Groups</th>
                                <th>Created At</th>
                                @if (hasActionPermission('Pack', 'show') ||
                                        hasActionPermission('Pack', 'update') ||
                                        hasActionPermission('Pack', 'delete'))
                                    <th class="no-sort"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packs as $pack)
                                <tr>
                                    <td style="display: none">
                                        {{ $pack->id }}
                                    </td>
                                    <td>{{ $pack->name }}</td>
                                    <td>{{ $pack->groups->count() }}</td>
                                    <td>{{ $pack->created_at->format('d M, Y') }}</td>
                                    @if (hasActionPermission('Pack', 'show') ||
                                            hasActionPermission('Pack', 'update') ||
                                            hasActionPermission('Pack', 'delete'))
                                        <td class="flex-end">
                                            <div class="d-flex gap-0 justify-content-end">
                                                @if (hasActionPermission('Pack', 'show'))
                                                    <a class="me-2 p-2 d-flex align-items-center border rounded"
                                                        href="{{ route('packs.show', $pack->id) }}">
                                                        <i data-feather="eye" class="feather-eye"></i>
                                                    </a>
                                                @endif
                                                @if (hasActionPermission('Pack', 'update'))
                                                    <a href="{{ route('packs.edit', $pack->id) }}"
                                                        class="me-2 p-2 d-flex align-items-center border rounded">
                                                        <i data-feather="edit" class="feather-edit"></i>
                                                    </a>
                                                @endif
                                                @if (hasActionPermission('Pack', 'delete'))
                                                    <form action="{{ route('packs.destroy', $pack->id) }}" method="POST">
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
