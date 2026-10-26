<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CorrelationIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check if the correlation ID exists, otherwise create one
        $correlationId = $request->header('X-Correlation-ID') ?: Str::uuid()->toString();

        // set header for the request
        $request->headers->set('X-Correlation-ID', $correlationId);

        // proceed with the request
        $response = $next($request);

        // set the correlation ID to the response header
        $response->headers->set('X-Correlation-ID', $correlationId);

        // returns the response
        return $response;
    }
}
