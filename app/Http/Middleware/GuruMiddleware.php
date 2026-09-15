<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GuruMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access for 'guru'/'dosen' and also 'admin' roles
        if (Auth::check() && in_array(Auth::user()->role, ['guru', 'dosen', 'admin'])) {
            return $next($request);
        }

        return redirect('/dashboard');
    }
}
