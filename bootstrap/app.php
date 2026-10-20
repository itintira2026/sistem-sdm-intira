<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middlewares\RoleMiddleware;
use Spatie\Permission\Middlewares\PermissionMiddleware;
use Spatie\Permission\Middlewares\RoleOrPermissionMiddleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    // ->withMiddleware(function (Middleware $middleware): void {
    //     //
    // })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    // bootstrap/app.php
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\CheckUserActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (HttpException $e, Request $request) {
            // Laravel mengubah TokenMismatchException jadi HttpException 419 sebelum
            // sampai ke sini, jadi dicek lewat status code-nya.
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi.'], 419);
            }

            $input = $request->except('password', 'password_confirmation', '_token');

            // 1. Submit dari halaman login: balik ke login, isian (kecuali password) dipertahankan
            if ($request->is('login')) {
                return redirect()->route('login')
                    ->withInput($input)
                    ->with('status', 'Halaman login kedaluwarsa. Silakan coba masuk lagi.');
            }

            // 2. Masih login tapi token tidak cocok (misalnya login di tab lain): balik ke form
            if ($request->user()) {
                return back()
                    ->withInput($input)
                    ->with('error', 'Halaman kedaluwarsa. Silakan kirim ulang.');
            }

            // 3. Sesi habis: login dulu, setelah itu dikembalikan ke halaman form sebelumnya
            return redirect()->guest(route('login'))
                ->with('status', 'Sesi Anda berakhir. Silakan masuk lagi.');
        });
    })->create();
