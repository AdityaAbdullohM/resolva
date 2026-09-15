<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SiswaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access for 'siswa' and 'mahasiswa' roles
        if (Auth::check() && in_array(Auth::user()->role, ['siswa', 'mahasiswa'])) {
            return $next($request);
        }

        return redirect('/dashboard');
    }
}
