<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use App\Http\Middleware\LogUserActivity;
use App\Http\Middleware\ApiRateLimit;
use App\Http\Middleware\ApiVersion;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SanitizeInput;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            LogUserActivity::class,
            SecurityHeaders::class,
            SanitizeInput::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'api.rate_limit' => ApiRateLimit::class,
            'api.version' => ApiVersion::class,
            'security.headers' => SecurityHeaders::class,
            'sanitize.input' => SanitizeInput::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
