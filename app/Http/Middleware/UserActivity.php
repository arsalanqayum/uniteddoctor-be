<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class UserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
//        dd('in');
        if (Auth::check()) {
            $user = Auth::user();
            $lastSeen = $user->last_seen;
            // Check if last seen is more than 2 minutes ago
            if (now()->diffInMinutes($lastSeen) > 2) {
                // Update last seen
                $user->update(['last_seen' => now()]);
            }
        }

        return $next($request);
    }
}
