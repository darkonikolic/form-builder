<?php

declare(strict_types=1);

use App\Exceptions\AuthenticationException as AppAuthenticationException;
use App\Exceptions\ResourceNotFoundException as AppResourceNotFoundException;
use App\Exceptions\ServerException as AppServerException;
use App\Http\Middleware\ViteMiddleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            AddLinkHeadersForPreloadedAssets::class,
            ViteMiddleware::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (AppAuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 401);
        });

        $exceptions->renderable(function (AppResourceNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        });

        $exceptions->renderable(function (AppServerException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        });

        $exceptions->renderable(function (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Field not found',
            ], 404);
        });
    })->create();
