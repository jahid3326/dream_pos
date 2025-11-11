@auth
    <script>
        window.addEventListener('load', function() {
            if (typeof window.Echo !== 'undefined') {
                const userId = {{ Auth::id() }};

                // Laravel's default private channel for a user's notifications
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        console.log('New Notification Received via Echo:', notification);

                        // Update Header Notifications
                        updateHeaderFromEcho(notification);

                        // Update Shipment Dashboard if we're on that page
                        if (typeof updateShipmentDashboardFromEcho === 'function') {
                            updateShipmentDashboardFromEcho(notification);
                        }
                    });
            }
        });

        // Header notification update function
        function updateHeaderFromEcho(notification) {
            console.log('Updating header from Echo notification:', notification);

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
