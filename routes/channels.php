<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// ADD THIS CHANNEL FOR SUPPLIERS
Broadcast::channel('supplier.{supplierId}', function (User $user, $supplierId) {
    // This logic checks if the currently logged-in user has a supplier profile,
    // and if that profile's ID matches the channel ID.

    // IMPORTANT: Ensure the relationship is named 'supplierProfile' on the User model
    // and that it actually exists for the logged-in user.
    return $user->supplierProfile && $user->supplierProfile->id == $supplierId;
});
