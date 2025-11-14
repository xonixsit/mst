<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeoutMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = Session::get('last_activity');
            $timeout = config('session.lifetime') * 60; // Convert minutes to seconds
            
            if ($lastActivity && (time() - $lastActivity) > $timeout) {
                // Session has expired
                Auth::logout();
                Session::flush();
                Session::regenerate();
                
                return redirect()->route('admin.login')->with('message', 'Your session has expired. Please log in again.');
            }
            
            // Update last activity timestamp
            Session::put('last_activity', time());
        }

        return $next($request);
    }
}