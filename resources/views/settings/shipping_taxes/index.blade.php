@extends('layouts.app')
@section('title', 'Shipping Taxes')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Shipping Taxes</h4>
                        <h6>Manage your shipping taxes</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('ShippingTax', 'create'))
                        <a href="{{ route('shipping-taxes.create') }}" class="btn btn-primary"><i
                                class="ti ti-circle-plus me-1"></i>New Shipping Taxes</a>
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
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    @if (hasActionPermission('ShippingTax', 'update') || hasActionPermission('ShippingTax', 'delete'))
                                        <td class="text-end">
                                            <div class="d-flex gap-0 justify-content-end">
                                                @if (hasActionPermission('ShippingTax', 'update'))
                                                    <a href="{{ route('shipping-taxes.edit', $item->id) }}"
                                                        class="me-2 p-2 d-flex align-items-center border rounded">
                                                        <i data-feather="edit" class="feather-edit"></i>
                                                    </a>
                                                @endif
                                                @if (hasActionPermission('ShippingTax', 'delete'))
                                                    <form action="{{ route('shipping-taxes.destroy', $item->id) }}"
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
                                    {{-- <td class="text-end">
                                        <a href="{{ route('shipping-taxes.edit', $item) }}"
                                            class="btn btn-sm btn-secondary">Edit</a>

                                        <form action="{{ route('shipping-taxes.destroy', $item) }}" method="POST"
                                            style="display:inline-block"
                                            onsubmit="return confirm('Delete this shipping tax?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3">{{ $items->links() }}</div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // SweetAlert2 delete confirmation for shipping taxes
        $(document).on('click', '.delete-button', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const $form = $btn.closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: 'This shipping tax will be permanently deleted.',
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
