@extends('layouts.app')
@section('title', 'Quotation / Estimate')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="add-item d-flex">
                    <div class="page-title">
                        <h4 class="fw-bold">Quotation / Estimate List</h4>
                        <h6>Manage your Quotes</h6>
                    </div>
                </div>
                <div class="page-btn">
                    @if (hasActionPermission('Quote', 'create'))
                        <a href="{{ route('pos.index') }}" class="btn btn-primary"><i class="ti ti-circle-plus me-1"></i>Add
                            New Quotation / Estimate</a>
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
                                    <th>Quote #</th>
                                    <th>Quote Date</th>
                                    <th>Customer</th>
                                    <th>Quotes Status</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th class="text-end">Due Amount</th>
                                    <th>Payment Status</th>
                                    @if (hasActionPermission('Quote', 'update') || hasActionPermission('Quote', 'delete'))
                                        <th class="no-sort"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($quotes as $quote)
                                    {{-- Main Row for the Quote --}}
                                    <tr>
                                        <td>
                                            {{-- 1. THE TOGGLE BUTTON --}}
                                            <a class="btn btn-sm btn-outline-secondary accordion-toggle"
                                                data-bs-toggle="collapse" href="#quoteItems{{ $quote->id }}"
                                                role="button">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </td>
                                        <td>{{ $quote->quote_number }}</td>
                                        <td>{{ $quote->quote_date->format('d M, Y') }}</td>
                                        <td>{{ $quote->customer->user->name }}</td>
                                        <td>
                                            @if (ucfirst($quote->status) == 'Converted')
                                                <span class="badge bg-success">Converted to Sale</span>
                                            @elseif(ucfirst($quote->status) == 'In process')
                                                <span class="badge" style="background-color: #0d6efd">In process</span>
                                            @else
                                                <span class="badge" style="background-color: #fd7e14">On process</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold">${{ number_format($quote->grand_total, 2) }}</td>
                                        <td class="text-end text-success">${{ number_format($quote->paid_amount, 2) }}</td>
                                        <td class="text-end text-danger">${{ number_format($quote->due_amount, 2) }}</td>
                                        <td>
                                            @if ($quote->payment_status == 'Paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($quote->payment_status == 'Deposit')
                                                <span class="badge" style="background-color: #fd7e14">Deposit</span>
                                            @else
                                                <span class="badge bg-danger">Unpaid</span>
                                            @endif
                                        </td>
                                        @if (hasActionPermission('Quote', 'update') || hasActionPermission('Quote', 'delete'))
                                            <td class="text-end">
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm" type="button"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        {{-- Convert to Purchase --}}
                                                        @if ($quote->status != 'converted')
                                                            <li>
                                                                {{-- We use a form for the POST request --}}
                                                                <form
                                                                    action="{{ route('quotes.convertToSale', $quote->id) }}"
                                                                    method="POST" class="d-inline convert-to-sale-form">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="dropdown-item d-flex align-items-center">
                                                                        <i class="fas fa-exchange-alt fa-fw me-2"></i>
                                                                        Convert
                                                                        to Sale
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>

                                                        {{-- View, Edit, Delete --}}
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center"
                                                                href="{{ route('quotes.show', $quote->id) }}">
                                                                <i class="fas fa-eye fa-fw me-2"></i> View
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center"
                                                                href="{{ route('quotes.edit', $quote->id) }}">
                                                                <i class="fas fa-edit fa-fw me-2"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('quotes.destroy', $quote->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" style="margin-left: .4rem"
                                                                    class="dropdown-item d-flex align-items-center delete-button text-danger">
                                                                    <i class="fas fa-trash fa-fw me-2"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>

                                    {{-- Collapsible Row for quote Items --}}
                                    <tr class="collapse-row">
                                        <td colspan="10" class="p-0"> {{-- p-0 to remove default padding --}}
                                            <div class="collapse" id="quoteItems{{ $quote->id }}">
                                                <div class="p-3 bg-light border-top">
                                                    @include('quotes._quote-items-details', [
                                                        'quote' => $quote,
                                                    ])
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No quote found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $quotes->links() }}</div>
                </div>
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

        // =========================================================================
        // 3. SCRIPT FOR CONVERT TO SALE CONFIRMATION
        // =========================================================================
        $('.convert-to-sale-form').on('submit', function(event) {
            // Prevent the form from submitting automatically
            event.preventDefault();

            const form = this; // Get a reference to the form that was submitted

            Swal.fire({
                title: 'Convert this Quote to a Sale?',
                text: "A new invoice will be generated. This action cannot be undone.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, convert it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                // If the user confirms, submit the form
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endpush
