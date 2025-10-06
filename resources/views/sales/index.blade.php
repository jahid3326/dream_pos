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
                        <a href="{{ route('pos.index') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add
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
                                    <th>Purchase</th>
                                    @if (hasActionPermission('Sale', 'update') || hasActionPermission('Sale', 'delete'))
                                        <th class="no-sort"></th>
                                    @endif
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
                                        <td>
                                            @if (ucfirst($sale->order_status) == 'Delivered')
                                                <span class="badge bg-success">Delivered</span>
                                            @elseif(ucfirst($sale->order_status) == 'In process')
                                                <span class="badge" style="background-color: #0d6efd">In process</span>
                                            @else
                                                <span class="badge" style="background-color: #fd7e14">On process</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold">${{ number_format($sale->grand_total, 2) }}</td>
                                        <td class="text-end text-success">${{ number_format($sale->paid_amount, 2) }}</td>
                                        <td class="text-end text-danger">${{ number_format($sale->due_amount, 2) }}</td>
                                        <td>
                                            @if ($sale->payment_status == 'Paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($sale->payment_status == 'Deposit')
                                                <span class="badge" style="background-color: #fd7e14">Deposit</span>
                                            @else
                                                <span class="badge bg-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{-- Check if the 'purchases' collection is not empty --}}
                                            @if ($sale->purchases->isNotEmpty())
                                                <span class="d-inline-block bg-success rounded-circle"
                                                    style="width: 15px; height: 15px;" data-bs-toggle="tooltip"
                                                    title="Purchase Order Created">
                                                </span>
                                            @endif
                                        </td>
                                        @if (hasActionPermission('Sale', 'update') || hasActionPermission('Sale', 'delete'))
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        {{-- Convert to Purchase --}}
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center convert-to-purchase-btn"
                                                                href="#" data-bs-toggle="modal"
                                                                data-bs-target="#convertToPurchaseModal"
                                                                data-url="{{ route('sales.purchasePreview', $sale->id) }}"
                                                                data-action="{{ route('sales.convertToPurchase', $sale->id) }}">
                                                                <i class="fas fa-shopping-bag fa-fw me-2"></i> Convert to
                                                                Purchase
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>

                                                        {{-- View, Edit, Delete --}}
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center"
                                                                href="{{ route('sales.show', $sale->id) }}">
                                                                <i class="fas fa-eye fa-fw me-2"></i> View
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center"
                                                                href="{{ route('sales.edit', $sale->id) }}">
                                                                <i class="fas fa-edit fa-fw me-2"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('sales.destroy', $sale->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" style="margin-left: .4rem"
                                                                    class="dropdown-item d-flex align-items-center delete-button text-danger">
                                                                    <i class="fas fa-trash fa-fw me-2"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>

                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>

                                                        {{-- Payment Actions --}}
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center view-payments-btn"
                                                                href="#" data-bs-toggle="modal"
                                                                data-bs-target="#viewPaymentsModal"
                                                                data-url="{{ route('sales.payments.get', $sale->id) }}">
                                                                <i class="fas fa-wallet fa-fw me-2"></i> View Payments
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center add-payment-btn"
                                                                href="#" data-bs-toggle="modal"
                                                                data-bs-target="#addPaymentModal"
                                                                data-sale-id="{{ $sale->id }}"
                                                                data-invoice-number="{{ $sale->invoice_number }}"
                                                                data-due-amount="{{ $sale->due_amount }}">
                                                                <i class="fas fa-dollar-sign fa-fw me-2"></i> Add New
                                                                Payment
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>

                                                        {{-- Invoice Actions --}}
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center"
                                                                href="#">
                                                                <i class="fas fa-shopping-cart fa-fw me-2"></i> POS Invoice
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center"
                                                                href="{{ route('sales.downloadInvoice.pdf', $sale->id) }}">
                                                                <i class="fas fa-download fa-fw me-2"></i> Download Invoice
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex print-invoice-btn align-items-center"
                                                                href="javascript:void(0);"
                                                                data-url="{{ route('sales.print.invoice', $sale->id) }}">
                                                                <i class="fas fa-print fa-fw me-2"></i> Print Invoice
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        @endif
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
                    <div class="d-flex justify-content-end mt-3">{{ $sales->links() }}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Payment Modal -->
    <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Add New Payment for Invoice #<span
                            id="payment-invoice-number"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-payment-form">
                    @csrf
                    {{-- This hidden input will store the URL for the form submission --}}
                    <input type="hidden" id="add-payment-form-action" value="">

                    <div class="modal-body">
                        <div id="payment-errors" class="alert alert-danger" style="display: none;"></div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" id="payment-date" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Mode<span class="text-danger">*</span></label>
                                <select name="payment_mode" class="form-select" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Amount Paid<span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" id="payment-amount"
                                    class="form-control" required autocomplete="off">
                                <small class="form-text text-muted">Amount Due: <span id="due-amount-text"
                                        class="fw-bold"></span></small>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Payment Note</label>
                                <textarea name="note" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- View Payments Modal -->
    <div class="modal fade" id="viewPaymentsModal" tabindex="-1" aria-labelledby="viewPaymentsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewPaymentsModalLabel">Payments for Invoice #<span
                            id="payments-invoice-number"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- The table for payments will be dynamically inserted here --}}
                    <div id="payments-list-container">
                        <p class="text-center text-muted">Loading payments...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Convert to Purchase Modal -->
    <div class="modal fade" id="convertToPurchaseModal" tabindex="-1" aria-labelledby="convertToPurchaseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="convertToPurchaseModalLabel">Convert to Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="convert-purchase-form" method="POST">
                    @csrf
                    <div class="modal-body" id="purchase-preview-body">
                        {{-- AJAX content will be loaded here --}}
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // =========================================================================
            // 1. SCRIPT FOR THE ACCORDION +/- TOGGLE ICON
            // =========================================================================
            // We listen for Bootstrap's built-in collapse events.

            // When a collapsible section starts to SHOW
            $('.collapse').on('show.bs.collapse', function() {
                // Find the specific button that controls this section and change its icon.
                $('a.accordion-toggle[href="#' + this.id + '"] i')
                    .removeClass('fa-plus')
                    .addClass('fa-minus');
            });

            // When a collapsible section starts to HIDE
            $('.collapse').on('hide.bs.collapse', function() {
                // Find the specific button and change the icon back.
                $('a.accordion-toggle[href="#' + this.id + '"] i')
                    .removeClass('fa-minus')
                    .addClass('fa-plus');
            });


            // =========================================================================
            // 2. DEFINITIVE FIX FOR DROPDOWN VISIBILITY IN A SCROLLING TABLE
            // =========================================================================
            // We use event delegation on the '.table-responsive' container.

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

            // --- CONVERT TO PURCHASE SCRIPT ---
            let purchaseModal = new bootstrap.Modal(document.getElementById('convertToPurchaseModal'));

            $('.convert-to-purchase-btn').on('click', function() {
                const previewUrl = $(this).data('url');
                const formAction = $(this).data('action');
                const modalBody = $('#purchase-preview-body');
                const purchaseForm = $('#convert-purchase-form');

                purchaseForm.attr('action', formAction);
                modalBody.html(
                    '<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                );

                $.ajax({
                    url: previewUrl,
                    type: 'GET',
                    success: function(suppliers) {
                        // console.log(suppliers);

                        let content = '';
                        if (suppliers.length === 0) {
                            content =
                                '<p class="text-center text-muted">No products with assigned suppliers found in this sale.</p>';
                        } else {
                            suppliers.forEach((supplier, s_idx) => {
                                content += `
                                <div class="mb-4 border rounded p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <input type="hidden" name="suppliers[${s_idx}][supplier_id]" value="${supplier.supplier_id}">
                                        <img src="public/storage/${supplier.supplier_image_url}" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                        <h6 class="mb-0">${supplier.supplier_name}</h6>
                                    </div>
                                    <table class="table table-sm">
                                        <thead><tr><th>#</th><th>Name</th><th class="text-end">Quantity</th><th class="text-end">Unit Price</th><th class="text-end">Total Price</th></tr></thead>
                                        <tbody>`;
                                supplier.products.forEach((product, p_idx) => {
                                    content += `
                                    <tr>
                                    <input type="hidden" name="suppliers[${s_idx}][products][${p_idx}][product_id]" value="${product.product_id}">
                                    <input type="hidden" name="suppliers[${s_idx}][products][${p_idx}][variation_id]" value="${product.variation_id || ''}">
                                    <input type="hidden" name="suppliers[${s_idx}][products][${p_idx}][product_name]" value="${product.product_name}">
                                    <input type="hidden" name="suppliers[${s_idx}][products][${p_idx}][quantity]" value="${product.quantity}">
                                    <input type="hidden" name="suppliers[${s_idx}][products][${p_idx}][unit_price]" value="${product.unit_price}">
                                    <input type="hidden" name="suppliers[${s_idx}][products][${p_idx}][total_price]" value="${product.total_price}">
                                    
                                    <td>${p_idx + 1}</td>
                                    
                                    {{-- DETAILED PRODUCT NAME CELL --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="public/${product.image_url}" class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                            <div>
                                                <strong>${product.product_name}</strong><br>
                                                <small class="text-muted">${product.category_name} (${product.measurement || 'N/A'})</small>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="text-end">${product.quantity}</td>
                                    <td class="text-end">$${parseFloat(product.unit_price).toFixed(2)}</td>
                                    <td class="text-end">$${parseFloat(product.total_price).toFixed(2)}</td>
                                </tr>`;
                                });
                                content += `</tbody></table></div>`;
                            });

                            // Add Documents Section
                            content += `
                        <div class="mt-4">
                            <h6>Required Documents</h6>
                            <div class="row">
                                <div class="col-md-4 form-check form-switch"><input class="form-check-input" type="checkbox" name="documents[]" value="Proforma Invoice (PI)" id="doc1"><label class="form-check-label" for="doc1">Proforma Invoice (PI)</label></div>
                                <div class="col-md-4 form-check form-switch"><input class="form-check-input" type="checkbox" name="documents[]" value="Packing List" id="doc2"><label class="form-check-label" for="doc2">Packing List</label></div>
                                <div class="col-md-4 form-check form-switch"><input class="form-check-input" type="checkbox" name="documents[]" value="Certificate of Origin (COO)" id="doc3"><label class="form-check-label" for="doc3">Certificate of Origin (COO)</label></div>
                                <div class="col-md-4 form-check form-switch"><input class="form-check-input" type="checkbox" name="documents[]" value="MSDS / Safety Data" id="doc4"><label class="form-check-label" for="doc4">MSDS / Safety Data</label></div>
                                <div class="col-md-4 form-check form-switch"><input class="form-check-input" type="checkbox" name="documents[]" value="Insurance" id="doc5"><label class="form-check-label" for="doc5">Insurance</label></div>
                                <div class="col-md-4 form-check form-switch"><input class="form-check-input" type="checkbox" name="documents[]" value="Fumigation Certificate" id="doc6"><label class="form-check-label" for="doc6">Fumigation Certificate</label></div>
                            </div>
                        </div>
                    `;
                        }
                        modalBody.html(content);

                    },
                    error: function() {
                        modalBody.html(
                            '<p class="text-center text-danger">Could not load purchase preview data.</p>'
                        );
                    }
                });
            });
        });

        const addPaymentModal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
        const paymentForm = $('#add-payment-form');

        $(document).on('click', '.add-payment-btn', function() {
            const saleId = $(this).data('sale-id');
            const invoiceNumber = $(this).data('invoice-number');
            const dueAmount = parseFloat($(this).data('due-amount')).toFixed(2);

            // Set the form's action URL dynamically
            const url = `{{ url('sales') }}/${saleId}/payments`;
            paymentForm.attr('action', url);

            // Populate the modal's display fields
            $('#payment-invoice-number').text(invoiceNumber);
            $('#due-amount-text').text(`$${dueAmount}`);

            // Set the 'max' attribute and default value for the amount input
            $('#payment-amount').attr('max', dueAmount);

            // Set default date to today
            $('#payment-date').val(new Date().toISOString().split('T')[0]);

            // Clear old errors
            $('#payment-errors').hide().html('');
        })

        // --- Listener to SUBMIT the payment via AJAX ---
        paymentForm.on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const url = form.attr('action'); // Get URL from the hidden input
            const submitButton = form.find('button[type="submit"]');
            const originalButtonText = submitButton.text();

            submitButton.prop('disabled', true).text('Processing...');

            $.ajax({
                url: url,
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        addPaymentModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Added!',
                            text: response.message,
                        }).then(() => {
                            // Reload the page to see the updated paid/due amounts
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        const errorContainer = $('#payment-errors');
                        let errorHtml = '<ul>';
                        // Use Object.values to get all error messages
                        Object.values(errors).forEach(error => errorHtml += '<li>' + error +
                            '</li>');
                        errorHtml += '</ul>';
                        errorContainer.html(errorHtml).show();
                    } else {
                        alert('An unexpected server error occurred.');
                    }
                },
                complete: function() {
                    submitButton.prop('disabled', false).text(originalButtonText);
                }
            });
        });

        $(document).on('click', '.delete-button', function() {
            // 1. Prevent the form from submitting immediately
            event.preventDefault();

            // 2. Find the closest parent form of the clicked button
            const form = $(this).closest('form');

            // 3. Show the SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                // 4. If the user clicks "Yes, delete it!"
                if (result.isConfirmed) {
                    // Submit the form
                    form.submit();
                }
            });
        })

        $(document).on('click', '.print-invoice-btn', function(e) {
            e.preventDefault();

            // Get the URL from the data-attribute
            const url = $(this).data('url');

            // Open the URL in a new window. It's important to keep a reference to it.
            const printWindow = window.open(url, '_blank');

            // Focus on the new window (optional, but good UX)
            if (printWindow) {
                printWindow.focus();
            } else {
                alert('Please allow pop-ups for this site to print the invoice.');
            }
        })

        $(document).on('click', '.view-payments-btn', function(e) {
            e.preventDefault();

            const url = $(this).data('url');
            const paymentsContainer = $('#payments-list-container');

            // Show a loading state
            paymentsContainer.html('<p class="text-center text-muted">Loading payments...</p>');

            // Make the AJAX call to the controller
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update the modal title
                        $('#payments-invoice-number').text(response.invoice_number);

                        // Build the payments table HTML
                        let tableHtml = '';
                        if (response.payments && response.payments.length > 0) {
                            tableHtml = `
                            <table class="table table-bordered table-striped">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Payment Mode</th>
                                        <th>Note</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                            response.payments.forEach(function(payment) {
                                tableHtml += `
                                <tr>
                                    <td>${payment.date}</td>
                                    <td>${payment.mode}</td>
                                    <td>${payment.note || ''}</td>
                                    <td class="text-end">$${payment.amount}</td>
                                </tr>`;
                            });

                            tableHtml += `</tbody></table>`;
                        } else {
                            tableHtml =
                                '<p class="text-center text-muted">No payments have been recorded for this sale.</p>';
                        }

                        // Inject the finished HTML into the modal
                        paymentsContainer.html(tableHtml);
                    }
                },
                error: function() {
                    paymentsContainer.html(
                        '<p class="text-center text-danger">Could not load payment information.</p>'
                    );
                }
            });
        });
    </script>
@endpush
