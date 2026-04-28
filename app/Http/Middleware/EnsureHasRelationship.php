<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasRelationship
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->relationship()->exists()) {
            if (!$request->routeIs('onboarding*')) {
                return redirect()->route('onboarding');
            }
        }

        if (Auth::check() && Auth::user()->relationship()->exists() && $request->routeIs('onboarding*')) {
             return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
