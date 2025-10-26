@auth
    <script>
        window.addEventListener('load', function() {
            if (typeof window.Echo !== 'undefined') {
                const userId = {{ Auth::id() }};

                // Laravel's default private channel for a user's notifications
                window.Echo.private(`App.Models.User.${userId}`)
                    .notification((notification) => {
                        console.log('New Notification Received:', notification);

                        // 1. Update the unread count badge
                        const countBadge = document.getElementById('notification-count');
                        let currentCount = parseInt(countBadge.innerText) || 0;
                        countBadge.innerText = currentCount + 1;
                        countBadge.style.visibility = 'visible';
                        let sender_image = notification.sender_avatar ? notification.sender_avatar :
                            'images/default_avatar.png';
                        // 2. Build the new notification HTML
                        const newNotificationHtml = `
                    <li class="notification-message">
                        <a href="#">
                            <div class="media d-flex">
                                <span class="avatar flex-shrink-0">
                                    <img alt="Img" src="/public/storage/${sender_image}">
                                </span>
                                <div class="flex-grow-1">
                                    <p class="noti-details">
                                        <span class="noti-title">${notification.sender_name}</span>
                                        ${notification.message}
                                    </p>
                                    <p class="noti-time">Just now</p>
                                </div>
                            </div>
                        </a>
                    </li>`;

                        // 3. Prepend the new notification to the list
                        const listContainer = document.getElementById('notification-list-container');
                        listContainer.insertAdjacentHTML('afterbegin', newNotificationHtml);

                        // 4. Remove the "No new notifications" message if it exists
                        const noNotificationsMsg = document.getElementById('no-new-notifications');
                        if (noNotificationsMsg) {
                            noNotificationsMsg.remove();
                        }
                    });
            }
        });
    </script>
@endauth
