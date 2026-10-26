<?php

use App\Http\Middleware\CorrelationIdMiddleware;
use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Services\ApiResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->api(prepend: [
            CorrelationIdMiddleware::class
        ]);

        // check if the API is in maintenance mode
        $middleware->api(prepend: [
            MaintenanceModeMiddleware::class
        ]);

        // rate limiting middleware using the detault api rate limiter
        $middleware->api(prepend: [
            ThrottleRequests::class.':api' // use the 'api' rate limiter
        ]);

        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // custom exception for Rate Limiting
        $exceptions->render(function(ThrottleRequestsException $e, $request){
            return ApiResponse::error('Too many requests',429);
        });

        $exceptions->render(function(AccessDeniedHttpException $e, Request $request){
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    message : $e->getMessage(),
                    code : 403,
                );
            }
        });

        // capture validation errors (ValidationException)
        $exceptions->render(function(ValidationException $e, Request $request){
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    code: 422,
                    errors: $e->errors()
                );
            }
        });

        // exception for everyting else ?!
        $exceptions->render(function(\Exception $e, Request $request){
            if ($request->is('api/*')) {
                if ($e->getMessage() == "Route [login] not defined.") {
                    return ApiResponse::error(
                        message: "Invalid or missing authentication token.",
                        code: 401
                    );
                }

                return ApiResponse::error(
                    message: "An unexpected error occurred.",
                    code: 500,
                    errors: [$e->getMessage()]
                );
            }
        });

    })->create();
