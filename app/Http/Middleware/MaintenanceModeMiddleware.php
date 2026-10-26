<?php

namespace App\Http\Middleware;

use App\Services\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(config('app.maintenance_mode')) {
            return ApiResponse::error(
                'API is under maintenance. Please try again later.',
                503 // Service unavailable
            );
        }

        return $next($request);
    }
}
