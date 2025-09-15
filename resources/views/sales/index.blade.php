@extends('layouts.app')
@section('title', 'Sales')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Sales List</h4>
                        <h6>Manage your Sales</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Sale', 'create'))
                        <a href="{{ route('sales.create') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add
                            New Sale</a>
                    @endif
                </div>
            </div>

            @include('layouts._messages')
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%;"></th> {{-- For the +/- toggle button --}}
                                    <th>Invoice #</th>
                                    <th>Sale Date</th>
                                    <th>Customer</th>
                                    <th>Sales Status</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th class="text-end">Due Amount</th>
                                    <th>Payment Status</th>
                                    <th class="text-end" style="width: 5%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sales as $sale)
                                    {{-- Main Row for the Sale --}}
                                    <tr>
                                        <td>
                                            {{-- 1. THE TOGGLE BUTTON --}}
                                            <a class="btn btn-sm btn-outline-secondary accordion-toggle"
                                                data-bs-toggle="collapse" href="#saleItems{{ $sale->id }}"
                                                role="button">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </td>
                                        <td>{{ $sale->invoice_number }}</td>
                                        <td>{{ $sale->sales_date->format('d M, Y') }}</td>
                                        <td>{{ $sale->customer->user->name }}</td>
                                        <td>{{ ucfirst($sale->order_status) }}</td>
                                        <td class="text-end fw-bold">${{ number_format($sale->grand_total, 2) }}</td>
                                        <td class="text-end text-success">${{ number_format($sale->paid_amount, 2) }}</td>
                                        <td class="text-end text-danger">${{ number_format($sale->due_amount, 2) }}</td>
                                        <td>
                                            @if ($sale->payment_status == 'Paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($sale->payment_status == 'Partial')
                                                <span class="badge bg-warning">Partial</span>
                                            @else
                                                <span class="badge bg-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{-- 2. THE ACTION BUTTON --}}
                                            <div class="dropdown">
                                                {{-- Remove 'dropdown-toggle' class to hide the arrow --}}
                                                <button class="btn btn-light btn-sm" type="button"
                                                    data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                {{-- Add 'data-bs-boundary' to the dropdown menu to prevent it from being cut off --}}
                                                <ul class="dropdown-menu dropdown-menu-end" data-bs-boundary="window">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('sales.show', $sale->id) }}">View</a></li>
                                                    <li><a class="dropdown-item" href="#">View Payments</a></li>
                                                    <li><a class="dropdown-item" href="#">Add New Payment</a></li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('sales.edit', $sale->id) }}">Edit</a></li>
                                                    <li>
                                                        <form action="{{ route('sales.destroy', $sale->id) }}"
                                                            method="POST">
                                                            @csrf @method('DELETE')
                                                            <button type="submit"
                                                                class="dropdown-item delete-button">Delete</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item" href="#">Print Invoice</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Collapsible Row for Sale Items --}}
                                    <tr class="collapse-row">
                                        <td colspan="10" class="p-0"> {{-- p-0 to remove default padding --}}
                                            <div class="collapse" id="saleItems{{ $sale->id }}">
                                                <div class="p-3 bg-light border-top">
                                                    @include('sales._sale-items-details', [
                                                        'sale' => $sale,
                                                    ])
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No sales found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $sales->links() }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // Listen for the show/hide events of the Bootstrap collapse component
            $('.collapse').on('show.bs.collapse', function() {
                // Find the toggle button associated with this collapse area
                // and change its icon to a minus sign.
                $('a.accordion-toggle[href="#' + this.id + '"] i').removeClass('fa-plus').addClass(
                    'fa-minus');
            }).on('hide.bs.collapse', function() {
                // Find the toggle button and change its icon back to a plus sign.
                $('a.accordion-toggle[href="#' + this.id + '"] i').removeClass('fa-minus').addClass(
                    'fa-plus');
            });
        });
    </script>
@endpush
