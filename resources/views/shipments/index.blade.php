@extends('layouts.app')
@section('title', 'Shipments')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Shipment List</h4>
                    <h6>Manage your Shipments</h6>
                </div>
            </div>
            @include('layouts._messages')
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 5%;"></th>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Supplier(s)</th>
                                    <th>Created At</th>
                                    <th>Status</th>
                                    <th class="text-end">Shipping Cost</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th>Payment Status</th>
                                    <th class="no-sort">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($shipments as $shipment)
                                    <tr data-shipment-id="{{ $shipment->id }}">
                                        <td><a class="btn btn-sm btn-outline-secondary accordion-toggle"
                                                data-bs-toggle="collapse" href="#shipmentItems{{ $shipment->id }}"><i
                                                    class="fas fa-plus"></i></a></td>
                                        <td>{{ $shipment->shipment_number }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $shipment->customer->user->profile_picture ? asset('public/storage/' . $shipment->customer->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                                    alt="" class="rounded me-2" width="30" height="30"
                                                    style="object-fit: contain;">
                                                <span>{{ $shipment->customer->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                @foreach ($shipment->purchase->suppliers as $supplier)
                                                    <a href="#" class="avatar-group-item" data-bs-toggle="tooltip"
                                                        title="{{ $supplier->company_name }}"><img
                                                            src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                                            alt="{{ $supplier->company_name }}" class="rounded avatar-image"
                                                            style="object-fit: contain" width="30" height="30"
                                                            style="margin-left: -10px; border: 2px solid white;"></a>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>{{ $shipment->shipment_date->format('d-m-Y') }}</td>
                                        <td><span class="badge bg-warning">{{ $shipment->purchase->status }}</span></td>
                                        <td class="text-end">${{ number_format($shipment->total_amount, 2) }}</td>
                                        <td class="text-end paid-amount-cell">
                                            ${{ number_format($shipment->paid_amount, 2) }}</td>
                                        <td class="payment-status-cell">
                                            <span
                                                class="badge {{ $shipment->payment_status == 'Paid' ? 'bg-success' : ($shipment->payment_status == 'Partial' ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $shipment->payment_status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-sm" type="button"
                                                    data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('shipments.show', $shipment) }}">
                                                            <i class="fas fa-eye fa-fw me-2"></i> View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('shipments.edit', $shipment) }}">
                                                            <i class="fas fa-edit fa-fw me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item view-shipment-payments-btn" href="#"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#viewShipmentPaymentsModal"
                                                            data-url="{{ route('shipments.getPayments', $shipment) }}"
                                                            data-shipment-number="{{ $shipment->shipment_number }}">
                                                            <i class="fas fa-wallet fa-fw me-2"></i> View Payments
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item add-payment-btn" href="#"
                                                            data-bs-toggle="modal" data-bs-target="#addShipmentPaymentModal"
                                                            data-shipment-id="{{ $shipment->id }}"
                                                            data-shipment-number="{{ $shipment->shipment_number }}"
                                                            data-due-amount="{{ $shipment->due_amount }}">
                                                            <i class="fas fa-dollar-sign fa-fw me-2"></i> Add Payment
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('shipments.destroy', $shipment) }}"
                                                            method="POST" class="delete-shipment-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash fa-fw me-2"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- --- THIS IS THE CORRECTED COLLAPSIBLE ROW --- --}}
                                    <tr class="collapse-row">
                                        <td colspan="10" class="p-0"> {{-- Colspan must match number of columns --}}
                                            <div class="collapse" id="shipmentItems{{ $shipment->id }}">
                                                <div class="p-3 bg-light border-top">
                                                    <table class="table table-sm bg-white">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Supplier</th>
                                                                <th class="text-end">Quantity Products</th>
                                                                <th>Status Production</th>
                                                                <th>Ready for pickup</th>
                                                                <th class="text-end">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($shipment->purchase->suppliers as $supplier)
                                                                {{-- This is the supplier row that needs the data attributes --}}
                                                                <tr class="supplier-row"
                                                                    data-supplier-id="{{ $supplier->id }}"
                                                                    data-supplier-name="{{ $supplier->user->name }}">
                                                                    <td>
                                                                        <div class="d-flex align-items-center">
                                                                            <a class="btn btn-sm btn-outline-secondary me-2 product-toggle-btn collapsed"
                                                                                data-bs-toggle="collapse"
                                                                                href="#supplierProducts-ship-{{ $shipment->id }}-{{ $supplier->id }}">
                                                                                <i class="fas fa-angle-right"></i>
                                                                            </a>
                                                                            <img src="{{ $supplier->user->profile_picture ? asset('public/storage/' . $supplier->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                                                                class="rounded me-2"
                                                                                style="object-fit: contain" width="30"
                                                                                height="30">
                                                                            <span>{{ $supplier->company_name }}</span>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-end">{{ $supplier->total_quantity }}
                                                                    </td>
                                                                    <td><span
                                                                            class="badge bg-info">{{ $supplier->pivot->status_production }}</span>
                                                                    </td>
                                                                    <td>{{ $supplier->pivot->ready_date ? \Carbon\Carbon::parse($supplier->pivot->ready_date)->format('d-m-Y') : 'N/A' }}
                                                                    </td>
                                                                    <td class="text-end">
                                                                        <a href="{{ route('purchases.showSupplierDetails', ['purchase' => $shipment->purchase, 'supplier' => $supplier]) }}"
                                                                            class="btn btn-sm btn-outline-secondary"><i
                                                                                class="fas fa-eye"></i></a>
                                                                    </td>
                                                                </tr>
                                                                {{-- This is the nested product row --}}
                                                                <tr class="collapse-row">
                                                                    <td colspan="5" class="p-0 border-0">
                                                                        <div class="collapse"
                                                                            id="supplierProducts-ship-{{ $shipment->id }}-{{ $supplier->id }}">
                                                                            @php $supplierItems = $shipment->purchase->items->where('supplier_id', $supplier->id); @endphp
                                                                            @include(
                                                                                'purchases._purchase-items-details',
                                                                                ['items' => $supplierItems]
                                                                            )
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No shipments found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $shipments->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Shipment Payments Modal -->
    <div class="modal fade" id="viewShipmentPaymentsModal" tabindex="-1"
        aria-labelledby="viewShipmentPaymentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewShipmentPaymentsModalLabel">Payment History for SHIP #<span
                            id="payments-shipment-number"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="shipment-payments-list-container">
                        {{-- JS will load content here --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- This fresh JS includes all necessary accordion logic --}}
    <script>
        $(document).ready(function() {
            // --- MAIN SHIPMENT ACCORDION (+/- ICON) ---
            $('.collapse[id^="shipmentItems"]').on('show.bs.collapse', function() {
                $('a.accordion-toggle[href="#' + this.id + '"] i').removeClass('fa-plus').addClass(
                    'fa-minus');
            });
            $('.collapse[id^="shipmentItems"]').on('hide.bs.collapse', function() {
                $('a.accordion-toggle[href="#' + this.id + '"] i').removeClass('fa-minus').addClass(
                    'fa-plus');
            });

            // --- NESTED PRODUCT ACCORDION (STOP PROPAGATION) ---
            // Handles clicks and Bootstrap events to prevent conflicts
            $(document).on('click', '.product-toggle-btn', function(event) {
                event.stopPropagation();
            });
            $('.collapse[id^="supplierProducts-ship-"]').on('show.bs.collapse hide.bs.collapse', function(event) {
                event.stopPropagation();
            });

            // --- b) THIS IS THE NEW FIX: ICON ROTATION LOGIC ---
            // Listen for Bootstrap's 'show' event on the inner product collapse elements
            $('.collapse[id^="supplierProducts-ship-"]').on('show.bs.collapse', function() {
                // Find the specific button that controls this section
                const triggerButton = $(`a.product-toggle-btn[href="#${this.id}"]`);

                // Find the icon within that button and change it to the 'down' arrow
                triggerButton.find('i').removeClass('fa-angle-right').addClass('fa-angle-down');
            });

            // Listen for Bootstrap's 'hide' event on the inner product collapse elements
            $('.collapse[id^="supplierProducts-ship-"]').on('hide.bs.collapse', function() {
                // Find the specific button that controls this section
                const triggerButton = $(`a.product-toggle-btn[href="#${this.id}"]`);

                // Find the icon and change it back to the 'right' arrow
                triggerButton.find('i').removeClass('fa-angle-down').addClass('fa-angle-right');
            });
            // --- END OF NEW FIX ---

            // Listen for the 'show.bs.dropdown' event, which fires just before a menu is shown
            $('.table-responsive').on('show.bs.dropdown', function(e) {
                // 'e.relatedTarget' is the button that triggered the dropdown. This is the most reliable way.
                var button = $(e.relatedTarget);

                // Find the dropdown menu, which is the next sibling of the button
                var menu = button.next('.dropdown-menu');

                // Get the position of the button relative to the viewport
                var buttonRect = e.relatedTarget.getBoundingClientRect();

                // Move the menu from the table to the main body of the page.
                // This allows it to break free from the table's 'overflow' restriction.
                $('body').append(menu.detach());

                // Manually set the menu's CSS position to be perfectly aligned with the button
                menu.css({
                    'display': 'block',
                    'position': 'absolute',
                    'top': (buttonRect.top + buttonRect.height + window.scrollY) + 'px',
                    'left': 'auto', // Reset left positioning
                    'right': (window.innerWidth - buttonRect.right) + 'px' // Align right edge
                });

                // Store a reference to the original parent (the <div class="dropdown">)
                // so we know where to put the menu back.
                menu.data('original-parent', button.parent());
            });

            // When clicking ANYWHERE on the document, we check if we need to close our detached dropdown
            $(document).on('click', function(e) {
                var detachedMenu = $('body > .dropdown-menu');

                // If a detached menu exists AND the click was NOT on the dropdown button itself...
                // Note: .closest() is used to handle clicks on the icon inside the button.
                if (detachedMenu.length > 0 && $(e.target).closest('[data-bs-toggle="dropdown"]').length ===
                    0) {

                    var originalParent = detachedMenu.data('original-parent');

                    if (originalParent) {
                        // Put the menu back into its original container and hide it.
                        // We also remove the inline styles to prevent future positioning issues.
                        originalParent.append(detachedMenu.detach().hide().css({
                            position: '',
                            top: '',
                            left: '',
                            right: ''
                        }));
                    }
                }
            });

            // Tooltip initializer
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // --- DELETE SHIPMENT CONFIRMATION ---
            $('.delete-shipment-form').on('submit', function(event) {
                event.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete the shipment record but not the original purchase order.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // --- VIEW SHIPMENT PAYMENTS MODAL ---
            $('.view-shipment-payments-btn').on('click', function(e) {
                e.preventDefault();
                const url = $(this).data('url');
                const shipmentNumber = $(this).data('shipment-number');
                const container = $('#shipment-payments-list-container');

                $('#payments-shipment-number').text(shipmentNumber);
                container.html('<p class="text-center">Loading...</p>');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        let tableHtml =
                            '<p class="text-center text-muted">No payments recorded for this shipment cost.</p>';
                        if (response.payments && response.payments.length > 0) {
                            tableHtml =
                                `<table class="table table-bordered"><thead><tr><th>Date</th><th>Mode</th><th>Note</th><th class="text-end">Amount</th></tr></thead><tbody>`;
                            response.payments.forEach(p => {
                                tableHtml +=
                                    `<tr><td>${p.date}</td><td>${p.mode}</td><td>${p.note || ''}</td><td class="text-end">$${p.amount}</td></tr>`;
                            });
                            tableHtml += `</tbody></table>`;
                        }
                        container.html(tableHtml);
                    },
                    error: function() {
                        container.html(
                            '<p class="text-center text-danger">Could not load payments.</p>'
                            );
                    }
                });
            });
        });
    </script>
@endpush
