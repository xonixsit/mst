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
            
            // Redirect based on user role and current route context
            $intendedUrl = $request->session()->get('url.intended');
            
            if ($user->isAdmin() || $user->isTaxProfessional()) {
                if (str_contains($intendedUrl, '/client/')) {
                    return redirect()->route('admin.login');
                }
                return redirect()->intended(route('admin.dashboard'));
            } else {
                if (str_contains($intendedUrl, '/admin/')) {
                    return redirect()->route('client.login');
                }
                return redirect()->intended(route('client.dashboard'));
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