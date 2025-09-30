<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Only attempt to fetch notifications if a user is logged in.
        if (Auth::check()) {
            $user = Auth::user();

            // Get the 5 most recent unread notifications.
            $unreadNotifications = $user->unreadNotifications()->limit(5)->get();

            // Get the total count of all unread notifications.
            $unreadNotificationsCount = $user->unreadNotifications()->count();

            // Share these variables with the view this composer is attached to.
            $view->with('unreadNotifications', $unreadNotifications);
            $view->with('unreadNotificationsCount', $unreadNotificationsCount);
        } else {
            // Provide default empty values for guests to prevent errors.
            $view->with('unreadNotifications', collect());
            $view->with('unreadNotificationsCount', 0);
        }
    }
}
