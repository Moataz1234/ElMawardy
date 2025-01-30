<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

class DynamicBaseUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get the request's host (IP address)
        $host = $request->getHost();
    
        // Set the base URL and redirect URI dynamically based on the host
        if ($host === '192.168.10.178') {
            Config::set('app.url', 'http://192.168.10.178:8001');
            Config::set('services.asgardeo.redirect', 'http://192.168.10.178:8001/callback');
        } else {
            Config::set('app.url', 'http://172.29.206.251:8001');
            Config::set('services.asgardeo.redirect', 'http://172.29.206.251:8001/callback');
        }
    
        return $next($request);
    }
}