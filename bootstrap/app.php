<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use PHPUnit\Event\Runtime\Runtime;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $error, Request $request) {
            if ($request->is('api/*')) {
                if (method_exists($error, 'getStatusCode')) {
                    $statusCode = $error->getStatusCode();
                } elseif (isset($error->status)) {
                    $statusCode = $error->status;
                } else {
                    $statusCode = 500;
                }
                return response()->json([
                    'success' => false,
                    'error' => config('app.debug')
                    ? $error->getMessage() : 'An error occurred',
                ], config('app.debug') ? $statusCode : 500);
            }
        });
    })->create();
