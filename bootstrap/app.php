<?php

use App\Http\Middleware\AuthGuard;
use App\Http\Middleware\EnsureApiRequest;
use App\Http\Middleware\EnsureApiResult;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\TrustHosts;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('global', [
            TrustHosts::class,
            TrustProxies::class,
            ValidatePostSize::class,
            ConvertEmptyStringsToNull::class,
        ]);

        $middleware->group('api', [
            EnsureApiRequest::class,
            SubstituteBindings::class,
            EnsureApiResult::class,
        ]);

        $middleware->alias([
            'auth'         => Authenticate::class,
            'throttle'     => ThrottleRequests::class,
            'auth.sanctum' => EnsureFrontendRequestsAreStateful::class,
            'auth.guard'   => AuthGuard::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
