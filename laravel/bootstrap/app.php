<?php

use App\Http\Middleware\VerifyTrackingServiceToken;
use App\Http\Middleware\EnsureAdminUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(api: __DIR__.'/../routes/api.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias(['tracking.service' => VerifyTrackingServiceToken::class, 'admin.user' => EnsureAdminUser::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {})
    ->create();
