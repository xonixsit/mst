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
        
        // Check if user has client role
        if (!$user->isClient()) {
            abort(403, 'Access denied. Client privileges required.');
        }

        return $next($request);
    }
}