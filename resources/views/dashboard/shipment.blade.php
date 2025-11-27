@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
    <style>
        .notification-item.new-notification {
            border-left: 3px solid #28a745;
            background-color: #f8f9fa;
            animation: slideInRight 0.5s ease-out;
        }

        .notification-bounce {
            animation: bounce 0.6s ease-in-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes bounce {

            0%,
            20%,
            53%,
            80%,
            100% {
                animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
                transform: translate3d(0, 0, 0);
            }

            40%,
            43% {
                animation-timing-function: cubic-bezier(0.755, 0.050, 0.855, 0.060);
                transform: translate3d(0, -8px, 0);
            }

            70% {
                animation-timing-function: cubic-bezier(0.755, 0.050, 0.855, 0.060);
                transform: translate3d(0, -4px, 0);
            }

            90% {
                transform: translate3d(0, -1px, 0);
            }
        }

        /* Statistics card tweaks */
        .dash-widget {
            padding: 1.5rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-height: 72px;
            border-radius: 7px;
        }

        .dash-widgetimg span img {
            width: 44px;
            height: 44px;
            display: inline-block;
        }

        .dash-widgetcontent h5 {
            font-size: 1.375rem;
            margin: 0;
        }

        .dash-widgetcontent h6 {
            margin: 0;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="content">
            <div class="page-header">
                <div class="page-title">
                    <h4>Dashboard</h4>
                    <h6>Welcome back, {{ Auth::user()->name }}</h6>
                </div>
            </div>

            <div class="mb-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="fw-bold mb-0">Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#ff9f1a;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">Total Shipments</div>
                                            <div class="h4 mb-0">{{ $totalShipments ?? 0 }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a href="{{ route('shipments.index') }}">More infos ›</a></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#2b6ca3;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">Pending Processing</div>
                                            <div class="h4 mb-0">{{ $pendingCount ?? 0 }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a
                                            href="{{ route('shipments.index', ['filter' => 'pending']) }}">More infos ›</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#28a745;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">New Notifications</div>
                                            <div class="h4 mb-0" id="dashboard-notification-count">
                                                {{ $notifications->count() ?? 0 }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a href="{{ route('dashboard') }}">More infos ›</a></div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card p-3">
                                    <div class="d-flex align-items-center">
                                        <div
                                            style="width:14px;height:14px;border-radius:50%;background:#6f42c1;margin-right:12px">
                                        </div>
                                        <div>
                                            <div class="text-muted">Recent Activity</div>
                                            <div class="h4 mb-0">{{ $recentShipments->count() ?? 0 }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2"><a href="{{ route('shipments.index') }}">More infos ›</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Notifications Panel -->
                <div class="col-lg-6 col-sm-12 col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Recent Notifications</h5>
                            <div>
                                <button onclick="loadLatestNotifications()" class="btn btn-sm btn-outline-secondary me-2"
                                    title="Refresh notifications">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <a href="#" class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">Mark All
                                    Read</a>
                            </div>
                        </div>
                        <div class="card-body" id="notifications-container">
                            @if ($notifications->count() > 0)
                                <div id="notifications-list">
                                    @foreach ($notifications as $notification)
                                        <div class="notification-item border-bottom pb-3 mb-3"
                                            data-id="{{ $notification->id }}">
                                            <div class="d-flex">
                                                <div class="notification-icon me-3">
                                                    <i class="fas fa-ship text-primary"></i>
                                                </div>
                                                <div class="notification-content flex-grow-1">
                                                    @if ($notification->type === 'App\\Notifications\\NewShipmentNotification')
                                                        <h6 class="mb-1">New Shipment Created</h6>
                                                        <p class="mb-1 text-muted">
                                                            {{ $notification->data['message'] ?? 'New shipment notification' }}
                                                        </p>
                                                        @if (isset($notification->data['shipment_number']))
                                                            <small class="text-primary">Shipment:
                                                                {{ $notification->data['shipment_number'] }}</small>
                                                        @endif
                                                    @else
                                                        <p class="mb-1">
                                                            {{ $notification->data['message'] ?? 'New notification' }}</p>
                                                    @endif
                                                    <small
                                                        class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                                </div>
                                                <div class="notification-actions">
                                                    @if (isset($notification->data['action_url']))
                                                        <a href="{{ $notification->data['action_url'] }}"
                                                            class="btn btn-sm btn-outline-primary">View</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-4" id="no-notifications">
                                    <i class="fas fa-bell-slash fa-3x mb-3"></i>
                                    <p>No new notifications</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pending Shipments -->
                <div class="col-lg-6 col-sm-12 col-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Pending Shipments</h5>
                            <a href="{{ route('shipments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body">
                            @if ($pendingShipments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            @foreach ($pendingShipments as $shipment)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('shipments.show', $shipment) }}"
                                                            class="text-decoration-none">
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ $shipment->customer->user->profile_picture ? asset('public/storage/' . $shipment->customer->user->profile_picture) : asset('public/storage/images/default_avatar.png') }}"
                                                                    class="rounded me-2" width="32" height="32"
                                                                    style="object-fit: cover;">
                                                                <div>
                                                                    <div class="fw-semibold">
                                                                        {{ $shipment->shipment_number }}</div>
                                                                    <small
                                                                        class="text-muted">{{ $shipment->customer->user->name }}</small>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </td>
                                                    <td class="text-end">
                                                        <small
                                                            class="text-muted">{{ $shipment->shipment_date->format('M d') }}</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-shipping-fast fa-3x mb-3"></i>
                                    <p>No pending shipments</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function markAllAsRead() {
            // Use global function to sync all notification displays
            if (typeof window.markAllNotificationsAsReadGlobally === 'function') {
                window.markAllNotificationsAsReadGlobally().then(success => {
                    if (!success) {
                        alert('Could not mark notifications as read.');
                    }
                });
            } else {
                // Fallback to local method if global function not available
                const csrfToken = document.querySelector('meta[name="csrf-token"]');

                if (!csrfToken) {
                    alert('CSRF token not found. Please refresh the page and try again.');
                    return;
                }

                fetch('{{ route('notifications.markAsRead') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update dashboard notification display
                            updateShipmentDashboardNotificationDisplay([]);
                            updateShipmentDashboardNotificationBadge(0);
                        } else {
                            alert('Could not mark notifications as read: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Could not mark notifications as read. Please check your internet connection and try again. Error: ' +
                            error.message);
                    });
            }
        }

        function searchShipments() {
            // Redirect to shipments index with search focus
            window.location.href = '{{ route('shipments.index') }}';
        }

        function printReports() {
            // Implement print functionality
            alert('Print reports functionality coming soon!');
        }

        // Debug function to test notifications
        function testNotifications() {
            fetch('{{ route('notifications.getCount') }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Notification Count:\nUnread: ${data.unread_count}\nTotal: ${data.total_count}`);
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error testing notifications: ' + error.message);
                });
        }

        // Manual refresh function (fallback)
        function loadLatestNotifications() {
            fetch('{{ route('notifications.getLatest') }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Use global sync functions if available
                        if (typeof window.syncNotificationCount === 'function' &&
                            typeof window.syncNotificationDisplay === 'function') {
                            window.syncNotificationCount(data.unread_count);
                            window.syncNotificationDisplay(data.notifications);
                        } else {
                            // Fallback to local updates
                            updateShipmentDashboardNotificationDisplay(data.notifications);
                            updateShipmentDashboardNotificationBadge(data.unread_count);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading notifications:', error);
                });
        }

        // Shipment dashboard specific notification display functions
        function updateShipmentDashboardNotificationDisplay(notifications) {
            const container = document.getElementById('notifications-container');
            const notificationsList = document.getElementById('notifications-list');
            const noNotificationsDiv = document.getElementById('no-notifications');

            if (notifications.length > 0) {
                // Hide no notifications message
                if (noNotificationsDiv) {
                    noNotificationsDiv.style.display = 'none';
                }

                // Create notifications list if it doesn't exist
                if (!notificationsList) {
                    const newList = document.createElement('div');
                    newList.id = 'notifications-list';
                    container.appendChild(newList);
                }

                // Update notifications list
                const listElement = document.getElementById('notifications-list');
                listElement.innerHTML = notifications.map(notification => {
                        const isNewShipment = notification.type === 'App\\Notifications\\NewShipmentNotification';
                        return `
                        <div class="notification-item border-bottom pb-3 mb-3 new-notification" data-id="${notification.id}">
                            <div class="d-flex">
                                <div class="notification-icon me-3">
                                    <i class="fas fa-ship text-primary"></i>
                                </div>
                                <div class="notification-content flex-grow-1">
                                    ${isNewShipment ? `
    <<<<<<< HEAD
                                                                                            <h6 class="mb-1">New Shipment Created</h6>
                                                                                            <p class="mb-1 text-muted">${notification.data ? notification.data.message || notification.message || 'New shipment notification' : 'New shipment notification'}</p>
                                                                                            ${notification.data && notification.data.shipment_number ? `<small class="text-primary">Shipment: ${notification.data.shipment_number}</small>` : ''}
                                                                                        ` : `
                                                                                            <p class="mb-1">${notification.data ? notification.data.message || notification.message || 'New notification' : 'New notification'}</p>
                                                                                        `}
=======
                                                                            <h6 class="mb-1">New Shipment Created</h6>
                                                                            <p class="mb-1 text-muted">${notification.data ? notification.data.message || notification.message || 'New shipment notification' : 'New shipment notification'}</p>
                                                                            ${notification.data && notification.data.shipment_number ? `<small class="text-primary">Shipment: ${notification.data.shipment_number}</small>` : ''}
                                                                        `: `
                                                                            <p class="mb-1">${notification.data ? notification.data.message || notification.message || 'New notification' : 'New notification'}</p>
                                                                        `
                    } >>>
                    >>> > 5 bb5d6f0ac97586efbe039646ab59a3deb4bc774 <
                    small class = "text-muted d-block" > $ {
                        notification.created_at || 'Just now'
                    } < /small> <
                    /div> <
                    div class = "notification-actions" >
                    $ {
                        notification.data && notification.data.action_url ? `
<<<<<<< HEAD
                                                                                        <a href="${notification.data.action_url}" class="btn btn-sm btn-outline-primary">View</a>
                                                                                    ` : ''
                    } ===
                    === = <
                    a href = "${notification.data.action_url}"
                    class = "btn btn-sm btn-outline-primary" > View < /a>
                    ` : ''}
>>>>>>> 5bb5d6f0ac97586efbe039646ab59a3deb4bc774
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

            // Remove new-notification class after animation
            setTimeout(() => {
                document.querySelectorAll('.notification-item.new-notification').forEach(item => {
                    item.classList.remove('new-notification');
                });
            }, 500);
        } else {
            // Show no notifications message
            if (notificationsList) {
                notificationsList.innerHTML = '';
            }
            if (noNotificationsDiv) {
                noNotificationsDiv.style.display = 'block';
            } else {
                container.innerHTML = `
                        <div class="text-center text-muted py-4" id="no-notifications">
                            <i class="fas fa-bell-slash fa-3x mb-3"></i>
                            <p>No new notifications</p>
                        </div>
                    `;
            }
        }
        }

        function updateShipmentDashboardNotificationBadge(count) {
            // Update notification count in statistics card
            const notificationCountElement = document.getElementById('dashboard-notification-count');
            if (notificationCountElement) {
                notificationCountElement.textContent = count;
            }
        }

        // Function to handle new notifications from Echo (called by the Echo script)
        function updateShipmentDashboardFromEcho(notification) {
            console.log('Updating shipment dashboard from Echo:', notification);

            // Add a small delay to ensure database transaction completes before fetching count
            setTimeout(() => {
                // Fetch the actual unread count from server instead of incrementing
                fetch('{{ route('notifications.getCount') }}', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Fetched dashboard notification count from server:', data);
                        if (data.success) {
                            const dashboardCountElement = document.getElementById(
                                'dashboard-notification-count');
                            if (dashboardCountElement) {
                                console.log('Previous dashboard count:', dashboardCountElement.textContent,
                                    'New count:', data.unread_count);
                                dashboardCountElement.textContent = data.unread_count;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching notification count for dashboard:', error);
                        // Fallback: increment the current count
                        const dashboardCountElement = document.getElementById('dashboard-notification-count');
                        if (dashboardCountElement) {
                            let currentCount = parseInt(dashboardCountElement.textContent) || 0;
                            dashboardCountElement.textContent = currentCount + 1;
                        }
                    });
            }, 500); // 500ms delay to ensure database consistency

            // Add new notification to the dashboard list immediately (no delay needed for UI update)
            const container = document.getElementById('notifications-container');
            const notificationsList = document.getElementById('notifications-list');
            const noNotificationsDiv = document.getElementById('no-notifications');

            if (container) {
                // Hide no notifications message
                if (noNotificationsDiv) {
                    noNotificationsDiv.style.display = 'none';
                }

                // Create notifications list if it doesn't exist
                if (!notificationsList) {
                    const newList = document.createElement('div');
                    newList.id = 'notifications-list';
                    container.appendChild(newList);
                }

                const isNewShipment = notification.type === 'App\\Notifications\\NewShipmentNotification';
                const newNotificationHtml = `
                    <div class="notification-item border-bottom pb-3 mb-3 new-notification" data-id="${notification.id}">
                        <div class="d-flex">
                            <div class="notification-icon me-3">
                                <i class="fas fa-ship text-primary"></i>
                            </div>
                            <div class="notification-content flex-grow-1">
                                ${isNewShipment ? `
    <<<<<<< HEAD
                                                                                        <h6 class="mb-1">New Shipment Created</h6>
                                                                                        <p class="mb-1 text-muted">${notification.message || 'New shipment notification'}</p>
                                                                                        ${notification.shipment_number ? `<small class="text-primary">Shipment: ${notification.shipment_number}</small>` : ''}
                                                                                    ` : `
                                                                                        <p class="mb-1">${notification.message || 'New notification'}</p>
                                                                                    `}
=======
                                                                        <h6 class="mb-1">New Shipment Created</h6>
                                                                        <p class="mb-1 text-muted">${notification.message || 'New shipment notification'}</p>
                                                                        ${notification.shipment_number ? `<small class="text-primary">Shipment: ${notification.shipment_number}</small>` : ''}
                                                                    `: `
                                                                        <p class="mb-1">${notification.message || 'New notification'}</p>
                                                                    `
            } >>>
            >>> > 5 bb5d6f0ac97586efbe039646ab59a3deb4bc774
                <
                small class = "text-muted d-block" > Just now < /small> <
                /div> <
                div class = "notification-actions" >
                $ {
                    notification.action_url ? `
<<<<<<< HEAD
                                                                                    <a href="${notification.action_url}" class="btn btn-sm btn-outline-primary">View</a>
                                                                                ` : ''
                } ===
                === = <
                a href = "${notification.action_url}"
            class = "btn btn-sm btn-outline-primary" > View < /a>
            ` : ''}
>>>>>>> 5bb5d6f0ac97586efbe039646ab59a3deb4bc774
                            </div>
                        </div>
                    </div>
                `;

            // Prepend the new notification
            const listElement = document.getElementById('notifications-list');
            listElement.insertAdjacentHTML('afterbegin', newNotificationHtml);

            // Remove new-notification class after animation
            setTimeout(() => {
                const newNotif = listElement.querySelector('.notification-item.new-notification');
                if (newNotif) {
                    newNotif.classList.remove('new-notification');
                }
            }, 500);
        }
        }

        // Set up the Echo handler for shipment dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Register the shipment dashboard update function globally for Echo to call
            window.updateShipmentDashboardFromEcho = updateShipmentDashboardFromEcho;
        });
    </script>
@endpush
