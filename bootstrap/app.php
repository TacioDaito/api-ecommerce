<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
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
        $request = app(Request::class);
        // if ($request->is('api/*')) {
        if (true) {
            $exceptions->render(function (QueryException $error) {
                return response()->json([
                    'success' => false,
                    'error' => config('app.debug')
                    ? $error->getMessage() : 'Database query error'
                ], 500);
            });
            $exceptions->render(function (ValidationException $error) {
                $errorBag = $error->validator->errors()->all();
                $debug = config('app.debug');
                return response()->json([
                    'success' => false,
                    count($errorBag) > 1 && $debug
                    ? 'errors' : 'error' => $debug
                    ? $errorBag : 'Validation error',
                ], 422);
            });
            $exceptions->render(function (AuthorizationException $error) {
                return response()->json([
                    'success' => false,
                    'error' => $error->getMessage(),
                ], 403);
            });
            $exceptions->render(function (MethodNotAllowedHttpException $error) {
                return response()->json([
                    'success' => false,
                    'error' => 'Not found',
                ], 404);
            });
            $exceptions->render(function (NotFoundHttpException $error) {
                return response()->json([
                    'success' => false,
                    'error' => 'Not found',
                ], 404);
            });
        }
    })->create();
