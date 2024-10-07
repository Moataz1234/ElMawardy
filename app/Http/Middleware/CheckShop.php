<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckShop
{
    public function handle(Request $request, Closure $next): Response
    {
        {
            if (Auth::user() && Auth::user()->usertype === 'user') {
                return $next($request);
            }
            return redirect('/dashboard'); // Redirect if not admin
        }
    }
}
