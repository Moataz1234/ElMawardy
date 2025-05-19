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
            if (Auth::user() && (Auth::user()->usertype === 'user' || Auth::user()->usertype === 'admin' || Auth::user()->usertype === 'rabea' || Auth::user()->usertype === 'Acc' || Auth::user()->usertype === 'super')) {
                return $next($request);
            }
            return redirect('/dashboard'); // Redirect if not admin
        }
    }
}
