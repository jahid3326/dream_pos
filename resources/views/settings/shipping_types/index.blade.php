@extends('layouts.app')
@section('title', 'Shipping Types')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Shipping Types</h4>
                        <h6>Manage your shipping types</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('ShippingType', 'create'))
                        <a href="{{ route('shipping-types.create') }}" class="btn btn-primary"><i
                                class="ti ti-circle-plus me-1"></i>New Shipping Type</a>
                    @endif
                </div>
            </div>

            @include('layouts._messages')

            <div class="card">
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($types as $type)
                                <tr>
                                    <td>{{ $type->name }}</td>
                                    @if (hasActionPermission('ShippingType', 'update') || hasActionPermission('ShippingType', 'delete'))
                                        <td class="text-end">
                                            <div class="d-flex gap-0 justify-content-end">
                                                @if (hasActionPermission('ShippingType', 'update'))
                                                    <a href="{{ route('shipping-types.edit', $type->id) }}"
                                                        class="me-2 p-2 d-flex align-items-center border rounded">
                                                        <i data-feather="edit" class="feather-edit"></i>
                                                    </a>
                                                @endif
                                                @if (hasActionPermission('ShippingType', 'delete'))
                                                    <form action="{{ route('shipping-types.destroy', $type->id) }}"
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

            <div class="mt-3">{{ $types->links() }}</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // SweetAlert2 delete confirmation for shipping types
        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $form = $btn.closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This shipping type will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $form.trigger('submit');
                }
            });
        });
    </script>
@endpush
