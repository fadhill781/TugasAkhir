<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login-user')->with('error', 'Silakan login terlebih dahulu');
        }

        Log::info('UserMiddleware - User authenticated', [
            'user_id' => Auth::id(),
            'role' => Auth::user()->role
        ]);

        if (Auth::user()->role !== 'user') {
            Log::warning('UserMiddleware - Access denied', ['role' => Auth::user()->role]);
            return redirect('/')->with('error', 'Akses ditolak. Halaman khusus user.');
        }

        return $next($request);
    }
}
