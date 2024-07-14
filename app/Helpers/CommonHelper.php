<?php

use App\Models\User;

if (! function_exists('userOnlineStatus')) {
    function userOnlineStatus($user_id)
    {
        $user = User::find($user_id);
        $lastSeen = \Carbon\Carbon::create($user->last_seen);
        if ($user->last_seen == null && $lastSeen->diffInMinutes(now()) > 2) {
            return 'offline';
        }
        return 'online';
    }
}
if (! function_exists('dollarToCent')) {
    function dollarToCent($amount)
    {
        return (int)$amount * 100;
    }
}
if (! function_exists('centToDollar')) {
    function centToDollar($amount)
    {
        return (int)$amount / 100;
    }
}
// create a function which took amount and percentage and return the percentage amount
if (! function_exists('percentageAmount')) {
    function percentageAmount($amount, $percentage)
    {
        return ($amount / 100) * $percentage;
    }
}
