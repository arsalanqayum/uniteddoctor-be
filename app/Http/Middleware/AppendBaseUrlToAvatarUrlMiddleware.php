<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppendBaseUrlToAvatarUrlMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        $response = $next($request);

        if ($response->headers->has('Content-Type') && $response->headers->get('Content-Type') === 'application/json') {
            $responseContent = json_decode($response->getContent(), true);

            if (isset($responseContent['avatar'])) {
                dd("yes");
                $responseContent['avatar'] = config('app.url') . '/' . $responseContent['avatar'];
            }

            $response->setContent(json_encode($responseContent));
        }

        return $response;
    }
}
