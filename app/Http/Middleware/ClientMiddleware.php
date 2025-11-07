<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('client.login');
        }

        $user = auth()->user();
        
        // For now, allow all authenticated users to access client routes
        // In production, you would check if user has client role
        if (!$user) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}