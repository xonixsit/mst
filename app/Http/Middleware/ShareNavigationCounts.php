<?php

namespace App\Http\Middleware;

use App\Services\NavigationCountService;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareNavigationCounts
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only add counts for authenticated users on Inertia responses
        if (auth()->check() && $request->header('X-Inertia')) {
            $user = auth()->user();
            
            if ($user->isClient()) {
                $counts = NavigationCountService::getClientCounts();
            } else {
                $counts = NavigationCountService::getAdminCounts();
            }

            Inertia::share('navigationCounts', $counts);
        }

        return $response;
    }
}