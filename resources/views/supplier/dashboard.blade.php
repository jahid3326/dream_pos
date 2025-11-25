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
            @php
                $unreadNotifications = Auth::user()
                    ? Auth::user()->unreadNotifications()->latest()->take(6)->get()
                    : collect();
            @endphp
            <div class="page-header">
                <div class="page-title">
                    <h4>Dashboard</h4>
                    <h6>Welcome back, {{ Auth::user()->name }}</h6>
                </div>
            </div>
            {{-- Summary cards: New Order / In Process / Complete --}}
            <div class="mb-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="p-3 border rounded d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted">New Order</div>
                                        <h3 class="fw-bold">{{ $newOrdersCount ?? 0 }}</h3>
                                    </div>
                                    <div>
                                        <span class="badge bg-warning"
                                            style="width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted">In Progress</div>
                                        <h3 class="fw-bold">{{ $inProcessCount ?? 0 }}</h3>
                                    </div>
                                    <div>
                                        <span class="badge bg-info"
                                            style="width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-muted">Complete</div>
                                        <h3 class="fw-bold">{{ $completeCount ?? 0 }}</h3>
                                    </div>
                                    <div>
                                        <span class="badge bg-success"
                                            style="width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center">
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h5 class="fw-bold mb-0">Next actions</h5>
                            <small class="text-muted">Quick actions from your unread notifications</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary"
                                id="dashboard-notification-count">{{ $unreadNotifications->count() }}</span>
                            @if ($unreadNotifications->count() > 0)
                                <button class="btn btn-sm btn-link" id="mark-all-notifications">Mark all read</button>
                            @endif
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body p-3">
                            @if ($unreadNotifications->isEmpty())
                                <p class="text-muted mb-0">No next actions</p>
                            @else
                                <ul class="list-group list-group-flush">
                                    @foreach ($unreadNotifications as $notification)
                                        @php
                                            $data = $notification->data ?? [];
                                            $actionUrl = $data['action_url'] ?? ($data['action'] ?? '#');
                                            $message =
                                                $data['message'] ??
                                                (isset($data['purchase_number'])
                                                    ? 'Order #' . $data['purchase_number']
                                                    : 'Notification');
                                        @endphp
                                        <li class="list-group-item d-flex justify-content-between align-items-start"
                                            data-notification-id="{{ $notification->id }}">
                                            <div class="me-3">
                                                <a href="{{ $actionUrl }}"
                                                    class="fw-bold text-decoration-none">{!! Str::limit($message, 80) !!}</a>
                                                <div class="small text-muted">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </div>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-2 open-notification"
                                                    data-url="{{ $actionUrl }}">Open</button>
                                                <button class="btn btn-sm btn-outline-secondary mark-read-btn">Mark
                                                    read</button>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>

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
                                                            class="fas fa-exclamation-triangle fa-fw"></i> Upload file
                                                        missing
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
                                                                    class="fas fa-tasks fa-fw me-2"></i> View / Take
                                                                Action</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('orders.show', $activity) }}"><i
                                                                    class="fas fa-eye fa-fw me-2"></i> View Full Summary</a>
                                                        </li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('orders.show', $activity) }}?tab=payments"><i
                                                                    class="fas fa-wallet fa-fw me-2"></i> View Payments</a>
                                                        </li>
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

                // Next Actions: open and mark-as-read handlers
                $('#mark-all-notifications').on('click', function(e) {
                    e.preventDefault();
                    const btn = $(this);
                    btn.prop('disabled', true).text('Marking...');
                    fetch('{{ route('notifications.markAsRead') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                // Clear list
                                $('[data-notification-id]').remove();
                                $('#dashboard-notification-count').text('0');
                                if (typeof window.syncNotificationCount === 'function') window
                                    .syncNotificationCount(0);
                            } else {
                                alert('Could not mark all as read');
                            }
                        })
                        .catch(() => alert('Request failed'))
                        .finally(() => btn.prop('disabled', false).text('Mark all read'));
                });

                $(document).on('click', '.mark-read-btn', function() {
                    const li = $(this).closest('[data-notification-id]');
                    const id = li.data('notification-id');
                    const btn = $(this);
                    btn.prop('disabled', true).text('...');

                    fetch(`/notifications/${id}/mark-read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                li.slideUp(200, function() {
                                    $(this).remove();
                                });
                                // decrement count
                                const countEl = $('#dashboard-notification-count');
                                const newCount = Math.max(0, parseInt(countEl.text() || '0') - 1);
                                countEl.text(newCount);
                                if (typeof window.syncNotificationCount === 'function') window
                                    .syncNotificationCount(newCount);
                            } else {
                                alert('Could not mark notification as read');
                                btn.prop('disabled', false).text('Mark read');
                            }
                        })
                        .catch(() => {
                            alert('Request failed');
                            btn.prop('disabled', false).text('Mark read');
                        });
                });

                $(document).on('click', '.open-notification', function() {
                    const url = $(this).data('url') || '#';
                    // Optionally mark as read when opening
                    window.location.href = url;
                });
            })
        </script>
    @endpush
