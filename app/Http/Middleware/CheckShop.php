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
        $user = Auth::user(); // Get the authenticated user

        // Ensure the user belongs to the correct shop
        if ($user->shop_id != $request->route('shop')) {
            return redirect('dashboard')->with('error', 'Unauthorized access to this shop.');
        }

        return $next($request);
    }
}
