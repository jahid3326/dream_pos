@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
    <style>
        /* Custom styles for status badges to match your design */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 500;
            text-align: center;
            border: 1px solid #ccc;
            min-width: 100px;
            /* Ensures badges have a consistent width */
        }

        /* Review Statuses */
        .status-complet {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .status-need-review-supplier,
        .status-pending {
            background-color: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
        }

        .status-modification-requested {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeeba;
        }

        /* Production Statuses */
        .status-in-process {
            background-color: #ffc107;
            color: #212529;
            border-color: #ffc107;
        }

        .status-waiting {
            background-color: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
        }

        /* Payment Statuses */
        .status-deposit-payed {
            background-color: #ffc107;
            color: #212529;
            border-color: #ffc107;
        }

        .status-full-payed {
            background-color: #28a745;
            color: white;
            border-color: #28a745;
        }

        .status-waiting-payment {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .file-list span {
            display: block;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4 class="fw-bold">Recent Activity</h4>
                    <h6>A summary of your recent orders</h6>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Order Number</th>
                                    <th>Order Date</th>
                                    <th class="text-end">Your Total Amount</th>
                                    <th>Status Review</th>
                                    <th>Status Production</th>
                                    <th>Payment Status</th>
                                    <th>Files</th>
                                    <th class="no-sort">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activities as $activity)
                                    <tr>
                                        <td>
                                            <a href="{{ route('orders.details', $activity) }}"
                                                class="fw-bold">#{{ $activity->purchase_number }}</a>
                                            @if ($activity->sale)
                                                <small class="d-block text-muted">Ref:
                                                    {{ $activity->sale->invoice_number }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $activity->purchase_date->format('d-m-Y') }}</td>
                                        <td class="text-end fw-bold">
                                            ${{ number_format($activity->supplier_total_amount, 2) }}</td>

                                        {{-- NEW: Displaying per-supplier statuses from the controller --}}
                                        <td><span
                                                class="status-badge status-{{ Str::slug($activity->status_review) }}">{{ ucfirst(str_replace('-', ' ', $activity->status_review)) }}</span>
                                        </td>
                                        <td><span
                                                class="status-badge status-{{ Str::slug($activity->status_production) }}">{{ ucfirst($activity->status_production) }}</span>
                                        </td>

                                        <td><span
                                                class="status-badge status-{{ Str::slug($activity->payment_status_text) }}">{{ $activity->payment_status_text }}</span>
                                        </td>
                                        <td class="file-list small">
                                            @if ($activity->has_missing_files)
                                                <a href="{{ route('orders.show', $activity) }}?tab=documents"
                                                    class="text-danger d-block mb-1"><i
                                                        class="fas fa-exclamation-triangle fa-fw"></i> Upload file missing
                                                    &gt;</a>
                                            @endif
                                            @foreach ($activity->file_list as $file)
                                                <span>
                                                    {{ $file['name'] }}:
                                                    <strong
                                                        class="{{ $file['status'] == 'Ok' ? 'text-success' : 'text-danger' }}">{{ $file['status'] }}</strong>
                                                </span>
                                            @endforeach
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-sm" type="button"
                                                    data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('orders.details', $activity) }}"><i
                                                                class="fas fa-tasks fa-fw me-2"></i> View / Take Action</a>
                                                    </li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('orders.show', $activity) }}"><i
                                                                class="fas fa-eye fa-fw me-2"></i> View Full Summary</a>
                                                    </li>
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('orders.show', $activity) }}?tab=payments"><i
                                                                class="fas fa-wallet fa-fw me-2"></i> View Payments</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <p class="text-muted">No recent activity found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $activities->links() }}
                    </div>
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
        })
    </script>
@endpush
