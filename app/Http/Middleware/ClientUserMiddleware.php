<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $hash)
    {
//        if (!empty(Auth::user()->role) && Auth::user()->role === 'client-user') {
//            if (!empty(Auth::user()->client_id) && $invoice->client_id !== Auth::user()->client_id) {
//                abort(403, 'Unauthorized');
//            }
//        }

        return $next($request);
    }
}
