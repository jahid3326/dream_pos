@extends('layouts.app')

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>All Payments</h4>
                    <h6>View All Recorded Payments</h6>
                </div>
                {{-- Button to go back to the main sales list --}}
                <div class="page-btn">
                    @if (hasActionPermission('Sale', 'create'))
                        <a href="{{ route('payments.create') }}" class="btn btn-primary"><i
                                class="ti ti-circle-plus me-1"></i>Add
                            New Payment</a>
                    @endif
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">View Sales List</a>
                </div>
            </div>


            @include('layouts._messages')

            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3">
                    <div class="search-set">
                        <div class="search-input">
                            <span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Payment Date</th>
                                    <th>User</th>
                                    <th class="text-end">Amount</th>

                                    @if (hasActionPermission('SalePayment', 'update') || hasActionPermission('SalePayment', 'delete'))
                                        <th class="no-sort"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                        <td>
                                            {{-- Display the customer's name, linked to their parent sale --}}
                                            <a href="{{ route('sales.show', $payment->sale_id) }}"
                                                title="View Invoice #{{ $payment->sale->invoice_number }}">
                                                {{ $payment->sale->customer->user->name ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                                        @if (hasActionPermission('SalePayment', 'update') || hasActionPermission('SalePayment', 'delete'))
                                            <td class="text-end">
                                                <div
                                                    class="edit-delete-action d-flex align-items-center justify-content-end">
                                                    @if (hasActionPermission('SalePayment', 'update'))
                                                        <a href="{{ route('payments.edit', $payment->id) }}"
                                                            class="me-2 p-2 d-flex align-items-center border rounded">
                                                            <i data-feather="edit" class="feather-edit"></i></a>
                                                    @endif
                                                    @if (hasActionPermission('SalePayment', 'delete'))
                                                        <form action="{{ route('payments.destroy', $payment->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="me-2 p-2 d-flex align-items-center border rounded delete-button">
                                                                <i data-feather="trash-2"
                                                                    class="feather-trash-2"></i></button>
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
