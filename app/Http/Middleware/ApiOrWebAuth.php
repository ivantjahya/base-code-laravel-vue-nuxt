<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiOrWebAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('api')->check()) {
            Auth::shouldUse('api');
        } elseif (Auth::guard('web')->check()) {
            Auth::shouldUse('web');
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
