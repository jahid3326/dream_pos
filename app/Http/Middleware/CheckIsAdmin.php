<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated AND has the Super Admin role.
        if (Auth::check() && Auth::user()->isSuperAdmin()) {
            return $next($request);
        }

        // If not, abort with a 403 Forbidden error.
        abort(403, 'Unauthorized Action. You do not have permission to access this area.');
    }
}
