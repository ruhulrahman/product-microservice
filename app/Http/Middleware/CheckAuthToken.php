<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['message' => 'Unauthorized. No token provided.'], Response::HTTP_UNAUTHORIZED);
        }

        // Extract the token (Bearer token format)
        $token = str_replace('Bearer ', '', $token);

        // Check if token exists in Memcached
        if (!Cache::has("user_token_{$token}")) {
            return response()->json(['message' => 'Unauthorized. Invalid token.'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
