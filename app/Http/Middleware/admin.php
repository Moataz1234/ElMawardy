<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() && Auth::user()->usertype === 'admin') {
            return $next($request);
        }
<<<<<<< HEAD
        return redirect('/admin/dashboard'); // Redirect if not admin
=======
        return redirect('admin/dashboard'); // Redirect if not admin
>>>>>>> f6b866230d849c5df5c291e82aeecf4c795c326e
    }
}
