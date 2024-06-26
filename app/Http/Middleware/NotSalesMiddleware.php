<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotSalesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role->name === 'Sales' && $request->url() === route('dashboard')) {
            return redirect()->route('pos.salesPos');
        }
        
        if (auth()->user()->role->name !== 'Sales') {
            return $next($request);
        }

        return abort(403, 'You are not allowed to access this page.');
    }
}
