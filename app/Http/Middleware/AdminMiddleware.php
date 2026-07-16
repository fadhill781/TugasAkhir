<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login-admin')->with('error', 'Silakan login sebagai admin');
        }

        // Both admin and superadmin can access admin pages
        if (!in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            return redirect('/')->with('error', 'Akses ditolak. Halaman khusus admin.');
        }

        return $next($request);
    }
}
