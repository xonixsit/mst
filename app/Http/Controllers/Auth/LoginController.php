<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Check if email verification is required
            if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail()) {
                if ($user->isAdmin() || $user->isTaxProfessional()) {
                    return redirect()->route('admin.verification.notice');
                } else {
                    return redirect()->route('client.verification.notice');
                }
            }
            
            // Redirect based on current URL context or user role
            $intendedUrl = $request->session()->get('url.intended');
            
            if ($intendedUrl) {
                return redirect($intendedUrl);
            }
            
            // Check referer to determine context
            $referer = $request->headers->get('referer');
            if ($referer && str_contains($referer, '/admin/')) {
                return redirect()->route('admin.dashboard');
            } elseif ($referer && str_contains($referer, '/client/')) {
                return redirect()->route('client.dashboard');
            }
            
            // Default redirect based on user role
            if ($user->isAdmin() || $user->isTaxProfessional()) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('client.dashboard');
            }
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        // Revoke all tokens for the user (Sanctum)
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Determine which login page to redirect to based on current URL
        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, '/client/')) {
            return redirect()->route('client.login');
        }
        return redirect()->route('admin.login');
    }
}