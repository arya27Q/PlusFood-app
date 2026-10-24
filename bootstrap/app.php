<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserRole::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('mitra') || $request->is('mitra/*')) {
                return route('mitra.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if (\Illuminate\Support\Facades\Auth::guard('mitra')->check() && ($request->is('mitra') || $request->is('mitra/*'))) {
                return route('mitra.dashboard');
            }

            if (\Illuminate\Support\Facades\Auth::guard('web')->check() && !($request->is('mitra') || $request->is('mitra/*'))) {
                return route('beranda');
            }

            return route('beranda');
        });

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
