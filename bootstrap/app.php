<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Named middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Redirect already-authenticated admin users away from /admin/login
        $middleware->redirectGuestsTo(fn (Request $request) =>
            $request->is('admin/*')
                ? route('admin.login')
                : route('home')
        );

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Render 403 with a clean admin-friendly page when inside /admin
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, Request $request) {
            if ($e->getStatusCode() === 403 && $request->is('admin/*')) {
                return response()->view('errors.403', ['message' => $e->getMessage()], 403);
            }
        });

    })
    ->create();
