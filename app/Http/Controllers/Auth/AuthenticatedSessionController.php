<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    Log::info('Login attempt', ['email' => $request->email]);
    
    try {
        $request->authenticate();
        
        $user = $request->user();
        
        // Check if user is active
        if (!$user->is_active) {
            Auth::guard('web')->logout();
            Log::warning('Login blocked - user is inactive', ['user_id' => $user->id, 'email' => $user->email]);
            
            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
            ]);
        }
        
        $request->session()->regenerate();
        Log::info('Login successful', ['user_id' => $user->id, 'role' => $user->role, 'email' => $user->email]);

        // Force session save
        $request->session()->save();

        if ($user->role === 'superadmin') {
            Log::info('Redirecting superadmin to /superadmin/users');
            return redirect()->intended('/superadmin/users');
        }

        if ($user->role === 'admin') {
            Log::info('Redirecting admin to /pesanan');
            return redirect()->intended('/pesanan');
        }

        Log::info('Redirecting user to /homepage');
        return redirect()->intended('/homepage');
    } catch (\Exception $e) {
        Log::error('Login failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        throw $e;
    }
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
