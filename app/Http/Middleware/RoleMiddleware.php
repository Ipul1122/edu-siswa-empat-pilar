<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Check if user is authenticated under the guard corresponding to the required role
        $user = null;
        if ($role === 'admin') {
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
            }
        } elseif ($role === 'siswa') {
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
            }
        } else {
            // Fallback for general role checks
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
            } elseif (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
            }
        }

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized.'
                ], 401);
            }
            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->route('admin.login')->with('error', 'Silakan login terlebih dahulu.');
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Check if user has the required role
        if ($user->role !== $role) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden. Access denied.'
                ], 403);
            }
            // Redirect based on current user role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman siswa.');
            }
            return redirect()->route('siswa.dashboard')->with('error', 'Akses ditolak. Halaman tersebut khusus Admin.');
        }

        return $next($request);
    }
}
