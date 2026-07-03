<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'api.auth' => \App\Http\Middleware\ApiTokenMiddleware::class,
        ]);
        $middleware->redirectTo(
            function (\Illuminate\Http\Request $request) {
                if ($request->is('admin') || $request->is('admin/*')) {
                    return route('admin.login');
                }
                return route('login');
            }
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            $isApi = $request->is('api/*') || $request->expectsJson();

            if ($e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
                if ($isApi) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Terlalu banyak percobaan. Silakan coba lagi dalam beberapa saat.'
                    ], 429);
                }

                $seconds = $e->getHeaders()['Retry-After'] ?? 60;
                return back()
                    ->withInput()
                    ->with('error', "Terlalu banyak percobaan. Silakan coba lagi dalam {$seconds} detik.");
            }

            if ($isApi) {
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Validasi gagal.',
                        'errors' => $e->errors()
                    ], 422);
                }

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'
                    ], 401);
                }

                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException || $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Data atau halaman tidak ditemukan.'
                    ], 404);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException || $e instanceof \Illuminate\Auth\AccessDeniedException) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Anda tidak memiliki akses ke halaman ini.'
                    ], 403);
                }

                return response()->json([
                    'status' => 'error',
                    'message' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan pada server.',
                    'trace' => config('app.debug') ? $e->getTrace() : null
                ], 500);
            }

            // Web Authorization redirect
            if ($e instanceof \Illuminate\Auth\AccessDeniedException || $e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
                $user = $request->user();
                if ($user) {
                    $route = $user->role === 'admin' ? 'admin.dashboard' : 'siswa.dashboard';
                    return redirect()->route($route)
                        ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
                }
            }
        });
    })->create();
