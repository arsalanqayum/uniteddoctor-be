<?php

use Illuminate\Support\Facades\Broadcast;

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

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel('private-send-message', function ($user) {
//     return true;
// });
Broadcast::channel('private-initiate-call', function () {
    // Your authentication logic here
    // Return true to grant access, or false to deny
    return true;
});
Broadcast::channel('private-appointment', function () {
    // Your authentication logic here
    // Return true to grant access, or false to deny
    return true;
});
Broadcast::channel('private-send-message', function () {
    // Your authentication logic here
    // Return true to grant access, or false to deny
    return true;
});

