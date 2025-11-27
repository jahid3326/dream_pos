@extends('layouts.app')
@section('title', 'Taxes')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Taxes</h4>
                        <h6>Manage your taxes</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Tax', 'create'))
                        <a href="{{ route('taxes.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add
                            New Tax</a>
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
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table datatable" id="customer-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Rate (%)</th>
                                    <th>Status</th>
                                    @if (hasActionPermission('Tax', 'update') || hasActionPermission('Tax', 'delete'))
                                        <th class="no-sort"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($taxes as $tax)
                                    <tr>
                                        <td>{{ $tax->name }}</td>
                                        <td>{{ number_format($tax->rate, 2) }}%</td>
                                        <td>
                                            <span
                                                class="d-inline-flex align-items-center p-1 pe-2 rounded-1 text-white {{ $tax->status == 1 ? 'bg-success' : 'bg-danger' }} fs-10"><i
                                                    class="ti ti-point-filled me-1 fs-11"></i>{{ $tax->status == 1 ? 'Enabled' : 'Disabled' }}</span>
                                        </td>
                                        @if (hasActionPermission('Tax', 'update') || hasActionPermission('Tax', 'delete'))
                                            <td class="text-end">
                                                <div class="d-flex gap-0 justify-content-end">
                                                    @if (hasActionPermission('Tax', 'update'))
                                                        <a href="{{ route('taxes.edit', $tax->id) }}"
                                                            class="me-2 p-2 d-flex align-items-center border rounded">
                                                            <i data-feather="edit" class="feather-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (hasActionPermission('Tax', 'delete'))
                                                        <form action="{{ route('taxes.destroy', $tax->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')<button type="submit"
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
