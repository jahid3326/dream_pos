@auth
    <script>
        let echoConnected = false;
        let pollingInterval = null;

        window.addEventListener('load', function() {
            // Try to establish Echo connection first
            if (typeof window.Echo !== 'undefined') {
                const userId = {{ Auth::id() }};

                try {
                    // Laravel's default private channel for a user's notifications
                    window.Echo.private(`App.Models.User.${userId}`)
                        .notification((notification) => {
                            console.log('New Notification Received via Echo:', notification);
                            echoConnected = true;

                            // Update Header Notifications
                            updateHeaderFromEcho(notification);

                            // Update Shipment Dashboard if we're on that page
                            if (typeof updateShipmentDashboardFromEcho === 'function') {
                                updateShipmentDashboardFromEcho(notification);
                            }
                        })
                        .error((error) => {
                            console.warn('Echo connection failed:', error);
                            // startFallbackPolling();
                        });

                    // Check if Echo is actually working after 3 seconds
                    setTimeout(() => {
                        if (!echoConnected) {
                            console.warn('Echo not responding, falling back to polling');
                            // startFallbackPolling();
                        }
                    }, 3000);

                } catch (error) {
                    console.warn('Echo initialization failed:', error);
                    // startFallbackPolling();
                }
            } else {
                console.warn('Echo not available, using polling fallback');
                // startFallbackPolling();
            }
        });

        // Fallback polling function for when Echo fails
        function startFallbackPolling() {
            if (pollingInterval) return; // Already polling

            console.log('Starting fallback polling for notifications');

            pollingInterval = setInterval(() => {
                fetch('/api/notifications/unread-count', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.notifications && data.notifications.length > 0) {
                            // Check for new notifications and update UI
                            updateHeaderFromPolling(data.notifications);
                        }
                    })
                    .catch(error => {
                        console.error('Polling failed:', error);
                    });
            }, 5000); // Poll every 5 seconds
        }

        // Update header from polling data
        function updateHeaderFromPolling(notifications) {
            const currentCount = parseInt(document.getElementById('notification-count')?.textContent || '0');
            const newCount = notifications.length;

            if (newCount > currentCount) {
                // New notifications found, refresh header
                updateHeaderFromEcho(null, true); // Trigger refresh
            }
        }

        // Header notification update function
        function updateHeaderFromEcho(notification, forceRefresh = false) {
            console.log('Updating header from Echo notification:', notification);

            // If this is a forced refresh from polling, reload all notifications
            if (forceRefresh) {
                fetch('/api/notifications/latest', {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        refreshNotificationDisplay(data.notifications);
                        syncNotificationCount(data.count);
                    })
                    .catch(error => console.error('Failed to refresh notifications:', error));
                return;
            }

            if (!notification) return;

            // Add the notification to header immediately (for instant UI feedback)
            const listContainer = document.getElementById('notification-list-container');
            if (listContainer) {
                // Remove the "No new notifications" message if it exists
                const noNotificationsMsg = document.getElementById('no-new-notifications');
                if (noNotificationsMsg) {
                    noNotificationsMsg.remove();
                }

                // 2. Build the new notification HTML for header
                let sender_image = notification.sender_avatar ? notification.sender_avatar : 'images/default_avatar.png';
                const newNotificationHtml = `
                    <li class="notification-message new-notification">
                        <a href="${notification.action_url || '#'}">
                            <div class="media d-flex">
                                <span class="avatar flex-shrink-0">
                                    <img alt="Img" src="/public/storage/${sender_image}">
                                </span>
                                <div class="flex-grow-1">
                                    <p class="noti-details">
                                        <span class="noti-title">${notification.sender_name || 'System'}</span>
                                        ${notification.message || 'New notification'}
                                    </p>
                                    <p class="noti-time">Just now</p>
                                </div>
                            </div>
                        </a>
                    </li>`;

                listContainer.insertAdjacentHTML('afterbegin', newNotificationHtml);

                // Remove the new-notification class after animation
                setTimeout(() => {
                    const newNotif = listContainer.querySelector('.notification-message.new-notification');
                    if (newNotif) {
                        newNotif.classList.remove('new-notification');
                    }
                }, 500);
            }

            // Add a small delay to ensure database transaction completes before fetching count
            setTimeout(() => {
                // Fetch the actual unread count and sync all displays
                fetch('/notifications/count', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Fetched notification count from server:', data);
                        if (data.success) {
                            // Use global sync function to update all notification counts
                            if (typeof window.syncNotificationCount === 'function') {
                                window.syncNotificationCount(data.unread_count);
                            } else {
                                // Fallback to direct update
                                const countBadge = document.getElementById('notification-count');
                                if (countBadge) {
                                    console.log('Previous count:', countBadge.innerText, 'New count:', data
                                        .unread_count);
                                    countBadge.innerText = data.unread_count;

                                    if (data.unread_count > 0) {
                                        countBadge.style.visibility = 'visible';
                                    } else {
                                        countBadge.style.visibility = 'hidden';
                                    }
                                }
                            }

                            // Add pulse animation
                            const countBadge = document.getElementById('notification-count');
                            if (countBadge) {
                                countBadge.classList.add('notification-count-update');
                                setTimeout(() => {
                                    countBadge.classList.remove('notification-count-update');
                                }, 600);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching notification count:', error);
                        // Fallback: just increment the current count
                        const countBadge = document.getElementById('notification-count');
                        if (countBadge) {
                            let currentCount = parseInt(countBadge.innerText) || 0;
                            countBadge.innerText = currentCount + 1;
                            countBadge.style.visibility = 'visible';
                        }
                    });
            }, 500); // 500ms delay to ensure database consistency
        }

        // Global function to update shipment dashboard (will be used by individual pages if needed)
        window.updateShipmentDashboardFromEcho = null; // Will be set by individual pages if needed

        // Global notification synchronization functions
        window.syncNotificationCount = function(count) {
            // Update header notification count
            const headerCountBadge = document.getElementById('notification-count');
            if (headerCountBadge) {
                headerCountBadge.innerText = count;
                if (count > 0) {
                    headerCountBadge.style.visibility = 'visible';
                } else {
                    headerCountBadge.style.visibility = 'hidden';
                }
            }

            // Update dashboard notification count if it exists
            const dashboardCountElement = document.getElementById('dashboard-notification-count');
            if (dashboardCountElement) {
                dashboardCountElement.textContent = count;
            }
        };

        // Refresh entire notification display from server data
        function refreshNotificationDisplay(notifications) {
            const listContainer = document.getElementById('notification-list-container');
            if (!listContainer) return;

            // Clear existing notifications
            listContainer.innerHTML = '';

            if (notifications.length === 0) {
                listContainer.innerHTML =
                    '<li class="notification-message" id="no-new-notifications"><a href="#"><div class="media d-flex"><div class="flex-grow-1"><p class="noti-details"><span class="noti-title">No new notifications</span></p></div></div></a></li>';
                return;
            }

            // Build notification HTML
            notifications.forEach(notification => {
                let sender_image = notification.data && notification.data.sender_avatar ?
                    notification.data.sender_avatar : 'images/default_avatar.png';

                const notificationHtml = `
                    <li class="notification-message">
                        <a href="${notification.data && notification.data.action_url ? notification.data.action_url : '#'}">
                            <div class="media d-flex">
                                <span class="avatar flex-shrink-0">
                                    <img alt="Img" src="/public/storage/${sender_image}">
                                </span>
                                <div class="flex-grow-1">
                                    <p class="noti-details">
                                        <span class="noti-title">${notification.data && notification.data.sender_name ? notification.data.sender_name : 'System'}</span>
                                        ${notification.data && notification.data.message ? notification.data.message : 'New notification'}
                                    </p>
                                    <p class="noti-time">${formatTimeAgo(notification.created_at)}</p>
                                </div>
                            </div>
                        </a>
                    </li>`;
                listContainer.innerHTML += notificationHtml;
            });
        }

        // Helper function to format time ago
        function formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / 60000);

            if (diffInMinutes < 1) return 'Just now';
            if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
            if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
            return `${Math.floor(diffInMinutes / 1440)}d ago`;
        }

        window.syncNotificationDisplay = function(notifications) {
            // Update header notifications
            const headerListContainer = document.getElementById('notification-list-container');
            if (headerListContainer) {
                if (notifications.length > 0) {
                    const notificationHtml = notifications.map(notification => {
                        let sender_image = notification.data && notification.data.sender_avatar ?
                            notification.data.sender_avatar : 'images/default_avatar.png';

                        return `
                            <li class="notification-message">
                                <a href="${notification.data && notification.data.action_url ? notification.data.action_url : '#'}">
                                    <div class="media d-flex">
                                        <span class="avatar flex-shrink-0">
                                            <img alt="Img" src="/public/storage/${sender_image}">
                                        </span>
                                        <div class="flex-grow-1">
                                            <p class="noti-details">
                                                <span class="noti-title">${notification.data && notification.data.sender_name ? notification.data.sender_name : 'System'}</span>
                                                ${notification.data && notification.data.message ? notification.data.message : 'New notification'}
                                            </p>
                                            <p class="noti-time">${notification.created_at || 'Just now'}</p>
                                        </div>
                                    </div>
                                </a>
                            </li>`;
                    }).join('');

                    headerListContainer.innerHTML = notificationHtml;
                } else {
                    headerListContainer.innerHTML = `
                        <li class="notification-message" id="no-new-notifications">
                            <div class="media d-flex justify-content-center">
                                <p class="text-muted mt-3">No new notifications</p>
                            </div>
                        </li>
                    `;
                }
            }

            // Update dashboard notifications if the function exists
            if (typeof updateShipmentDashboardNotificationDisplay === 'function') {
                updateShipmentDashboardNotificationDisplay(notifications);
            }
        };

        window.markAllNotificationsAsReadGlobally = function() {
            return fetch('/notifications/mark-as-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Sync all notification displays
                        window.syncNotificationCount(0);
                        window.syncNotificationDisplay([]);
                        return true;
                    }
                    return false;
                })
                .catch(error => {
                    console.error('Error marking notifications as read:', error);
                    return false;
                });
        };
    </script>
@endauth
