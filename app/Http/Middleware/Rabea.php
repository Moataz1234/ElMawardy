<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Rabea
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        {
            if (Auth::user() && (Auth::user()->usertype === 'rabea' || Auth::user()->usertype === 'super')) {
                return $next($request);
            }
            return redirect('/dashboard'); // Redirect if not admin
        }
    }
}
