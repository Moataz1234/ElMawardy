<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Acc
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() && (Auth::user()->usertype === 'Acc' || Auth::user()->usertype === 'super')) {
            return $next($request);
        }
        return redirect('/dashboard')->withErrors('You do not have permission to access this page.');
    }
} 